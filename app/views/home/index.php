<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS - Comprehensive Inventory Management System | Track & Control Assets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .feature-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .feature-card:hover::before {
            opacity: 1;
        }
        .feature-card:hover {
            transform: translateY(-8px);
        }
    </style>
</head>
<body class="bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-boxes text-2xl gradient-text"></i>
                    <span class="text-2xl font-bold text-gray-900">IMS</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-gray-900 transition">Features</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition">How it Works</a>
                    <a href="#use-cases" class="text-gray-600 hover:text-gray-900 transition">Use Cases</a>
                    <a href="?url=auth/login" class="btn-primary text-white px-6 py-2 rounded-lg font-medium">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-white via-blue-50 to-purple-50">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Inventory <span class="gradient-text">Management</span> System
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Complete control over your inventory. Track assets, managed equipment assignments, monitor maintenance, and optimize vendor relationships all in one powerful platform.
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-blue-400 rounded-2xl blur-3xl opacity-20"></div>
                        <div class="relative bg-gradient-to-br from-purple-100 to-blue-100 rounded-2xl p-8 h-96 flex items-center justify-center">
                            <i class="fas fa-boxes text-9xl text-purple-300 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Complete Inventory Control</h2>
                <p class="text-xl text-gray-600">All the tools you need for professional inventory and asset management</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-cube text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Inventory Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">Comprehensive real-time tracking of all your inventory items and assets with complete visibility into location, status, and lifecycle management.</p>
                </div>
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-handshake text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Equipment Distribution</h3>
                    <p class="text-gray-600 leading-relaxed">Efficiently allocate inventory and equipment to employees with real-time tracking of who has what and when it needs to be returned.</p>
                </div>
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-tools text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Maintenance Tracking</h3>
                    <p class="text-gray-600 leading-relaxed">Monitor maintenance schedules, track repairs, and ensure optimal equipment performance with alerts and reports.</p>
                </div>
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-phone text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Vendor Management</h3>
                    <p class="text-gray-600 leading-relaxed">Manage vendor relationships, track purchases, and optimize procurement processes in one centralized location.</p>
                </div>
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Real-time Analytics</h3>
                    <p class="text-gray-600 leading-relaxed">Get instant insights into your inventory with comprehensive dashboards and detailed analytics reports.</p>
                </div>
                <div class="feature-card bg-white border border-gray-100 rounded-xl p-8 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Secure Access</h3>
                    <p class="text-gray-600 leading-relaxed">Enterprise-grade security with role-based access control and comprehensive audit trails for compliance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Manage Your Inventory in 4 Steps</h2>
                <p class="text-xl text-gray-600">From setup to daily operations - streamlined inventory management</p>
            </div>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Setup</h3>
                    <p class="text-gray-600">Configure your inventory system, add departments, and set up user accounts for your teams.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Catalog</h3>
                    <p class="text-gray-600">Register all your inventory items and assets with detailed specifications and tracking information.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Distribute</h3>
                    <p class="text-gray-600">Assign inventory and equipment to employees and departments with real-time tracking.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">4</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Optimize</h3>
                    <p class="text-gray-600">Monitor inventory levels, track maintenance, and generate insights to optimize operations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section id="use-cases" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Make Inventory Management Easy</h2>
                <p class="text-xl text-gray-600">Whether you're a small team or large enterprise, we have the right inventory solution</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-8 border border-blue-200">
                    <i class="fas fa-building text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Large Enterprises</h3>
                    <p class="text-gray-700">Manage large-scale multi-departmental inventories with advanced controls, reports, and compliance tracking.</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Enterprise Grade</span>
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Multi-location</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-8 border border-green-200">
                    <i class="fas fa-industry text-4xl text-green-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Manufacturing</h3>
                    <p class="text-gray-700">Optimize production asset inventory, track equipment maintenance schedules, and manage plant operations efficiently.</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Preventive Maintenance</span>
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Plant Assets</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-8 border border-purple-200">
                    <i class="fas fa-laptop text-4xl text-purple-600 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">IT & Tech Teams</h3>
                    <p class="text-gray-700">Manage computer inventory, network equipment, software licenses, and track IT assets across your organization.</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Hardware Tracking</span>
                        <span class="bg-white px-3 py-1 rounded-full text-sm text-gray-700">Deployments</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-purple-600 to-blue-600">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Take Control of Your Inventory Today</h2>
            <p class="text-xl text-purple-100 mb-8">Start managing your complete inventory operations with intelligence and efficiency.</p>
            <a href="?url=dashboard/index" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-bold hover:bg-gray-100 transition inline-block">
                Get Started Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-boxes text-2xl text-purple-400"></i>
                        <span class="text-xl font-bold text-white">IMS</span>
                    </div>
                    <p class="text-gray-500">Modern asset management for enterprises.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition">How It Works</a></li>
                        <li><a href="#use-cases" class="hover:text-white transition">Use Cases</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="?url=auth/login" class="hover:text-white transition">Login</a></li>
                        <li><a href="?url=dashboard/index" class="hover:text-white transition">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
                <p>&copy; 2026 Inventory Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                if (this.getAttribute('href') !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
