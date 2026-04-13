<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/landing.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar (Same as Dashboard) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand brand-link" href="?url=home/index">
                <img src="img/ims.png" alt="IMS Logo" class="brand-logo"> IMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?url=auth/login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="page-wrapper">
        <div class="hero-section">
            <!-- Icon / Logo -->
            <div class="hero-icon">
                <img src="img/ims.png" alt="IMS Logo" class="hero-logo">
            </div>

            <!-- Title -->
            <h1>Inventory Management System</h1>

            <!-- Subtitle -->
            <p>Efficiently manage your assets, track inventory, and streamline operations</p>

            <!-- Features -->
            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-cube"></i>
                    <h5>Asset Management</h5>
                    <p>Track and manage all your inventory assets in real-time</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-exchange-alt"></i>
                    <h5>Quick Assignments</h5>
                    <p>Assign equipment to employees effortlessly</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-tools"></i>
                    <h5>Maintenance Tracking</h5>
                    <p>Monitor maintenance schedules and records</p>
                </div>
            </div>

            <!-- Login Button -->
            <div class="hero-cta">
                <a href="?url=dashboard/index" class="btn-login">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2026 Inventory Management System. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/landing.js"></script>
</body>
</html>
