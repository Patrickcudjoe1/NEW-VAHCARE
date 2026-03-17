document.addEventListener('DOMContentLoaded', () => {
  console.log('Agewise Landing Page Initialized');

  // Fetch CSRF Token
  fetch('api/get_token.php')
    .then(response => response.json())
    .then(data => {
      const contactCsrf = document.getElementById('contact-csrf');
      const jobCsrf = document.getElementById('job-csrf');
      if (contactCsrf) contactCsrf.value = data.token;
      if (jobCsrf) jobCsrf.value = data.token;
    })
    .catch(err => console.error('Error fetching CSRF token:', err));

  // Intersection Observer for scroll animations
  const observerOptions = {
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-up');
      }
    });
  }, observerOptions);

  document.querySelectorAll('.scroll-animate').forEach(el => {
    observer.observe(el);
  });

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: 'smooth'
          });
        }
      }
    });
  });

  // Mobile Nav Toggle
  const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
  const header = document.querySelector('header');
  const navLinks = document.querySelector('.nav-links');

  if (mobileNavToggle) {
    mobileNavToggle.addEventListener('click', () => {
      header.classList.toggle('nav-active');
      const icon = mobileNavToggle.querySelector('i');
      if (header.classList.contains('nav-active')) {
        icon.setAttribute('data-lucide', 'x');
      } else {
        icon.setAttribute('data-lucide', 'menu');
      }
      lucide.createIcons();
    });
  }

  // Close menu when a link is clicked
  document.querySelectorAll('.nav-links a, .btn-register, .close-menu').forEach(link => {
    link.addEventListener('click', (e) => {
      if (header.classList.contains('nav-active')) {
        // Only close if it's not a dropdown trigger
        if (!link.parentElement.classList.contains('nav-item-dropdown')) {
          header.classList.remove('nav-active');
          const icon = mobileNavToggle.querySelector('i');
          if (icon) {
            icon.setAttribute('data-lucide', 'menu');
            lucide.createIcons();
          }
        }
      }
    });
  });

  // Handle dropdown in mobile view
  document.querySelectorAll('.nav-item-dropdown > a').forEach(dropdownToggle => {
    dropdownToggle.addEventListener('click', (e) => {
      if (window.innerWidth <= 1100) {
        e.preventDefault();
        const parent = dropdownToggle.parentElement;
        parent.classList.toggle('active');
        
        // Rotate chevron
        const chevron = dropdownToggle.querySelector('i[data-lucide="chevron-down"]');
        if (chevron) {
          chevron.style.transform = parent.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
        }
      }
    });
  });
  // Contact Form Submission
  const contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = contactForm.querySelector('.btn-submit');
      const originalText = submitBtn.innerHTML;
      
      submitBtn.innerHTML = 'Sending... <i data-lucide="loader"></i>';
      submitBtn.disabled = true;
      lucide.createIcons();

      const formData = new FormData(contactForm);
      // Manually add values if inputs don't have 'name' attributes
      if (!formData.has('name')) {
        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        formData.append('name', `${firstName} ${lastName}`);
        formData.append('email', document.getElementById('email').value);
        formData.append('phone', document.getElementById('phone').value);
        formData.append('service', document.getElementById('service-interest').value);
        formData.append('message', document.getElementById('message').value);
      }

      fetch('api/contact_handler.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          contactForm.reset();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again later.');
      })
      .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        lucide.createIcons();
      });
    });
  }

  // Job Application Modal Logic
  const jobModal = document.getElementById('job-modal');
  const applyButtons = document.querySelectorAll('.btn-apply');
  const closeModalBtn = document.querySelector('.close-modal');
  const cancelBtn = document.querySelector('.btn-cancel');
  const jobForm = document.getElementById('job-application-form');

  if (jobModal) {
    const openModal = () => {
      jobModal.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent scroll
    };

    const closeModal = () => {
      jobModal.classList.remove('active');
      document.body.style.overflow = ''; // Restore scroll
    };

    applyButtons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        openModal();
      });
    });

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Close on outside click
    window.addEventListener('click', (e) => {
      if (e.target === jobModal) {
        closeModal();
      }
    });

    // Form Submission
    if (jobForm) {
      jobForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const submitBtn = jobForm.querySelector('.btn-submit');
        const originalText = submitBtn.innerText;
        
        submitBtn.innerText = 'Submitting...';
        submitBtn.disabled = true;

        const formData = new FormData(jobForm);

        fetch('api/apply_handler.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            jobForm.reset();
            closeModal();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again later.');
        })
        .finally(() => {
          submitBtn.innerText = originalText;
          submitBtn.disabled = false;
        });
      });
    }
  }

  // Header Scroll Effect
  const handleScroll = () => {
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  };
  window.addEventListener('scroll', handleScroll);
  handleScroll(); // Initial check
});
