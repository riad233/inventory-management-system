<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Page Layout */
        .page-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        /* Hero Section */
        .hero-section {
            text-align: center;
            color: white;
            max-width: 800px;
        }
        
        .hero-section h1 {
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: fadeInDown 0.8s ease;
        }
        
        .hero-section p {
            font-size: 1.3em;
            margin-bottom: 30px;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .hero-icon {
            font-size: 5em;
            margin-bottom: 20px;
            animation: bounceIn 0.8s ease 0.1s both;
        }
        
        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 40px 0;
            animation: fadeInUp 0.8s ease 0.3s both;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .feature-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #ffd700;
        }
        
        .feature-card h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .feature-card p {
            font-size: 0.95em;
            opacity: 0.9;
        }
        
        /* Buttons */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 40px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            display: inline-block;
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }
        
        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 0.95em;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar (Same as Dashboard) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="?url=home/index">
                <i class="fas fa-boxes"></i> IMS
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
            <!-- Icon -->
            <div class="hero-icon">
                <i class="fas fa-cube"></i>
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
            <div style="margin-top: 30px;">
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
</body>
</html>
