<?php
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

redirect_if_not_admin();

// Fetch Contact Inquiries
$stmt = $pdo->query("SELECT * FROM contact_inquiries ORDER BY created_at DESC");
$inquiries = $stmt->fetchAll();

// Fetch Job Applications
$stmt = $pdo->query("SELECT * FROM job_applications ORDER BY created_at DESC");
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VAH Care</title>
    <link rel="icon" type="image/png" href="../../assets/favicon.png">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="admin-style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="admin-dashboard">
    <header class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <img src="../../assets/logo.png" alt="VAH Care Logo" class="admin-logo">
                <div class="admin-user-info">
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                    <a href="logout.php" class="btn-logout">Logout <i data-lucide="log-out"></i></a>
                </div>
            </div>
        </div>
    </header>

    <main class="container admin-main">
        <h1 class="page-title">Management Dashboard</h1>

        <div class="dashboard-tabs">
            <button class="tab-btn active" onclick="openTab(event, 'inquiries')">Contact Inquiries (<?php echo count($inquiries); ?>)</button>
            <button class="tab-btn" onclick="openTab(event, 'applications')">Job Applications (<?php echo count($applications); ?>)</button>
        </div>

        <div id="inquiries" class="tab-content active">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email / Phone</th>
                            <th>Service</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $row): ?>
                        <tr id="inquiry-<?php echo $row['id']; ?>">
                            <td><?php echo date('d M, Y H:i', strtotime($row['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($row['email']); ?><br>
                                <small><?php echo htmlspecialchars($row['phone']); ?></small>
                            </td>
                            <td><span class="badge"><?php echo htmlspecialchars($row['service_interest']); ?></span></td>
                            <td class="col-message"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                            <td>
                                <button class="btn-delete" onclick="deleteItem('inquiry', <?php echo $row['id']; ?>)">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inquiries)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No inquiries found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="applications" class="tab-content">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Applicant</th>
                            <th>Details</th>
                            <th>Experience / Availability</th>
                            <th>Resume</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $row): ?>
                        <tr id="application-<?php echo $row['id']; ?>">
                            <td><?php echo date('d M, Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['email']); ?><br>
                                <small><?php echo htmlspecialchars($row['phone']); ?></small>
                            </td>
                            <td>
                                Exp: <?php echo htmlspecialchars($row['experience']); ?><br>
                                Avail: <?php echo htmlspecialchars($row['availability']); ?>
                            </td>
                            <td>
                                <a href="../<?php echo htmlspecialchars($row['resume_path']); ?>" target="_blank" class="btn-view-resume">
                                    <i data-lucide="file-text"></i> View
                                </a>
                            </td>
                            <td>
                                <button class="btn-delete" onclick="deleteItem('application', <?php echo $row['id']; ?>)">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="application-letter-<?php echo $row['id']; ?>">
                            <td colspan="6" class="row-cover-letter">
                                <strong>Cover Letter:</strong><br>
                                <?php echo nl2br(htmlspecialchars($row['cover_letter'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applications)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No applications found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <input type="hidden" id="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    </main>

    <script>
        lucide.createIcons();

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        async function deleteItem(type, id) {
            if (!confirm(`Are you sure you want to delete this ${type}?`)) return;

            const csrfToken = document.getElementById('csrf_token').value;
            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);

            const endpoint = type === 'inquiry' ? 'delete_inquiry.php' : 'delete_application.php';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    const row = document.getElementById(`${type}-${id}`);
                    if (row) row.remove();
                    if (type === 'application') {
                        const letterRow = document.getElementById(`application-letter-${id}`);
                        if (letterRow) letterRow.remove();
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting.');
            }
        }
    </script>
</body>
</html>
