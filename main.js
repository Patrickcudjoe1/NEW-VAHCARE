document.addEventListener('DOMContentLoaded', () => {
  console.log('Agewise Landing Page Initialized');

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

        // Simulate API call
        setTimeout(() => {
          alert('Thank you for your application! Our recruitment team will review your details and get back to you soon.');
          jobForm.reset();
          submitBtn.innerText = originalText;
          submitBtn.disabled = false;
          closeModal();
        }, 1500);
      });
    }
  }
});
