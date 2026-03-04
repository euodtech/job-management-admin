<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-FMS User Manual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
        .doc-sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100vh; overflow-y: auto; }
        .doc-content { margin-left: 280px; }
        .doc-section { scroll-margin-top: 20px; }
        .nav-link { transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-link:hover, .nav-link.active { background: rgba(99, 102, 241, 0.08); border-left-color: #6366f1; color: #6366f1; }
        .screenshot-container { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .screenshot-container img { width: 100%; display: block; }
        .step-number { width: 28px; height: 28px; min-width: 28px; border-radius: 50%; background: #6366f1; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; }
        .badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        @media (max-width: 1024px) {
            .doc-sidebar { transform: translateX(-100%); z-index: 50; transition: transform 0.3s; }
            .doc-sidebar.open { transform: translateX(0); }
            .doc-content { margin-left: 0; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Mobile Toggle -->
<button onclick="document.getElementById('sidebar').classList.toggle('open')" class="lg:hidden fixed top-4 left-4 z-50 bg-white shadow-md rounded-lg p-2.5">
    <i class="fas fa-bars text-gray-600"></i>
</button>

<!-- Sidebar Navigation -->
<aside id="sidebar" class="doc-sidebar bg-white border-r border-gray-200 z-40">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-white text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-gray-900 text-lg leading-tight">E-FMS</h1>
                <p class="text-xs text-gray-400">User Manual v1.0</p>
            </div>
        </div>
    </div>
    <nav class="p-4 space-y-1">
        <p class="px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Getting Started</p>
        <a href="#overview" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Overview</a>
        <a href="#login" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Login & Authentication</a>

        <p class="px-3 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Master Modules</p>
        <a href="#dashboard" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Dashboard</a>
        <a href="#map" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Live Map</a>
        <a href="#rider" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Rider Management</a>
        <a href="#vehicle" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Vehicle Tracking</a>

        <p class="px-3 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Job Management</p>
        <a href="#job-list" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Job List</a>
        <a href="#reschedule" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Reschedule Job</a>
        <a href="#job-summary" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Job Summary</a>

        <p class="px-3 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reports</p>
        <a href="#report-rider" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Rider Report</a>
        <a href="#report-job" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Job Report</a>
        <a href="#report-customer" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Customer Report</a>

        <p class="px-3 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Account</p>
        <a href="#profile" class="nav-link block px-3 py-2 text-sm text-gray-600 rounded-r-lg">Profile & Password</a>
    </nav>
    <div class="p-4 border-t border-gray-100 mt-auto">
        <p class="text-[11px] text-gray-400 text-center">&copy; <?= date('Y') ?> E-FMS. All rights reserved.</p>
    </div>
</aside>

<!-- Main Content -->
<main class="doc-content">
    <div class="max-w-4xl mx-auto px-6 py-10 lg:px-10">

        <!-- ==================== OVERVIEW ==================== -->
        <section id="overview" class="doc-section mb-16">
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-8 text-white mb-8">
                <h1 class="text-3xl font-bold mb-3">E-FMS User Manual</h1>
                <p class="text-indigo-100 text-lg leading-relaxed">Electronic Fleet Management System &mdash; A comprehensive platform for managing jobs, riders, vehicles, and customers for field service operations.</p>
                <div class="flex flex-wrap gap-3 mt-5">
                    <span class="bg-white/20 backdrop-blur text-sm px-3 py-1 rounded-full">Job Management</span>
                    <span class="bg-white/20 backdrop-blur text-sm px-3 py-1 rounded-full">GPS Tracking</span>
                    <span class="bg-white/20 backdrop-blur text-sm px-3 py-1 rounded-full">Rider Monitoring</span>
                    <span class="bg-white/20 backdrop-blur text-sm px-3 py-1 rounded-full">Reports & Analytics</span>
                </div>
            </div>

            <h2 class="text-xl font-semibold mb-4">System Modules</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-tachometer-alt text-blue-600 text-sm"></i></div>
                        <h3 class="font-semibold">Dashboard</h3>
                    </div>
                    <p class="text-sm text-gray-500">Real-time overview of drivers, jobs, and fleet status at a glance.</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-briefcase text-green-600 text-sm"></i></div>
                        <h3 class="font-semibold">Job Management</h3>
                    </div>
                    <p class="text-sm text-gray-500">Create, assign, track, and manage all field service jobs by type.</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-users text-purple-600 text-sm"></i></div>
                        <h3 class="font-semibold">Rider Management</h3>
                    </div>
                    <p class="text-sm text-gray-500">Add, edit, and manage field riders/drivers with bulk import support.</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center"><i class="fas fa-map-marked-alt text-orange-600 text-sm"></i></div>
                        <h3 class="font-semibold">Live Map & Vehicles</h3>
                    </div>
                    <p class="text-sm text-gray-500">Track vehicles in real-time via GPS with geozones on an interactive map.</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center"><i class="fas fa-chart-bar text-red-600 text-sm"></i></div>
                        <h3 class="font-semibold">Reports</h3>
                    </div>
                    <p class="text-sm text-gray-500">Generate rider, job, and customer reports with date filters and Excel export.</p>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-check text-teal-600 text-sm"></i></div>
                        <h3 class="font-semibold">Reschedule & Summary</h3>
                    </div>
                    <p class="text-sm text-gray-500">Approve or reject reschedule requests and view consolidated job summaries.</p>
                </div>
            </div>
        </section>

        <!-- ==================== LOGIN ==================== -->
        <section id="login" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Login & Authentication</h2>
            <p class="text-gray-500 mb-6">Access the system through the administrator sign-in page.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/01-login.png') ?>" alt="Login Page">
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Log In</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Open the Application</p>
                        <p class="text-sm text-gray-500">Navigate to the application URL in your web browser. You will see the Administrator Sign In page.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Enter Your Credentials</p>
                        <p class="text-sm text-gray-500">Type your registered <strong>Email</strong> address and <strong>Password</strong> in the respective fields.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Click "Sign In"</p>
                        <p class="text-sm text-gray-500">Press the <strong>Sign In</strong> button to access the system. You will be redirected to the Dashboard.</p>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-amber-800">Forgot Password?</p>
                        <p class="text-sm text-amber-700">Contact your system administrator to reset your password, or use the Forgot Password link if available on the login page.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== DASHBOARD ==================== -->
        <section id="dashboard" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Dashboard</h2>
            <p class="text-gray-500 mb-6">The main landing page after login, providing a real-time overview of your fleet operations.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/02-dashboard.png') ?>" alt="Dashboard">
            </div>

            <h3 class="font-semibold text-lg mb-3">Dashboard Components</h3>

            <div class="space-y-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-gray-100">
                    <h4 class="font-semibold text-indigo-600 mb-2"><i class="fas fa-id-badge mr-2"></i>Driver Statistics Cards</h4>
                    <p class="text-sm text-gray-600">The top row displays three key metrics:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                        <li><strong>Total Drivers</strong> &mdash; Total number of registered riders/drivers</li>
                        <li><strong>Drivers Online</strong> <span class="badge bg-green-100 text-green-700">Online</span> &mdash; Drivers active within the last hour</li>
                        <li><strong>Drivers Offline</strong> <span class="badge bg-red-100 text-red-700">Offline</span> &mdash; Drivers with no recent activity</li>
                    </ul>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100">
                    <h4 class="font-semibold text-indigo-600 mb-2"><i class="fas fa-tasks mr-2"></i>Job Statistics Cards</h4>
                    <p class="text-sm text-gray-600">The second row shows job metrics:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                        <li><strong>Total Jobs</strong> &mdash; All jobs created for today</li>
                        <li><strong>Ongoing Job</strong> &mdash; Jobs currently in progress</li>
                        <li><strong>Finished Job</strong> &mdash; Jobs completed today</li>
                    </ul>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100">
                    <h4 class="font-semibold text-indigo-600 mb-2"><i class="fas fa-table mr-2"></i>Data Tables</h4>
                    <p class="text-sm text-gray-600">Four tables show detailed breakdowns:</p>
                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                        <li><strong>Drivers Online</strong> &mdash; List of currently active drivers with last activity time</li>
                        <li><strong>Drivers Offline</strong> &mdash; List of inactive drivers</li>
                        <li><strong>Ongoing Job</strong> &mdash; Jobs currently being worked on with assigned driver and customer</li>
                        <li><strong>Finished Job</strong> &mdash; Recently completed jobs</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- ==================== MAP ==================== -->
        <section id="map" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Live Map</h2>
            <p class="text-gray-500 mb-6">An interactive map that displays real-time vehicle positions and geozones using GPS tracking data from the Traxroot integration.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/06-map.png') ?>" alt="Live Map">
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Use the Live Map</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Navigate to Map</p>
                        <p class="text-sm text-gray-500">Click <strong>Map</strong> in the sidebar under the Master section.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">View Vehicle Locations</p>
                        <p class="text-sm text-gray-500">Vehicles with active GPS trackers will appear as markers on the map. The map auto-centers on your fleet's location.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Search for Vehicles</p>
                        <p class="text-sm text-gray-500">Use the <strong>Search for Vehicles</strong> text box in the top-left corner to find specific vehicles by name.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">4</div>
                    <div>
                        <p class="font-medium">Zoom & Navigate</p>
                        <p class="text-sm text-gray-500">Use the <strong>+</strong> / <strong>-</strong> buttons (top-right) to zoom in/out. Click and drag to pan the map. The current zoom level is shown below the zoom controls.</p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-blue-800">Map Data Source</p>
                        <p class="text-sm text-blue-700">Map data is provided by Traxroot and OpenStreetMap. Vehicle positions update based on GPS tracker intervals configured in the Traxroot platform.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== RIDER ==================== -->
        <section id="rider" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Rider Management</h2>
            <p class="text-gray-500 mb-6">Manage your field riders (drivers) who carry out jobs. Add, edit, delete, or bulk-import riders.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/03-rider.png') ?>" alt="Rider Management">
            </div>

            <h3 class="font-semibold text-lg mb-3">Rider List Overview</h3>
            <p class="text-sm text-gray-600 mb-4">The rider list displays all registered riders with their Company, Username, Role, Email, and Phone Number. Each row has <span class="badge bg-indigo-100 text-indigo-700">Edit</span> and <span class="badge bg-red-100 text-red-700">Delete</span> action buttons.</p>

            <h3 class="font-semibold text-lg mb-3">How to Add a New Rider</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Click "+ Add Rider"</p>
                        <p class="text-sm text-gray-500">Click the <strong>+ Add Rider</strong> button in the top-right corner. A modal form will appear.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Fill in Rider Details</p>
                        <p class="text-sm text-gray-500">Enter the rider's full name, email address, phone number, and assign a role. All fields are required.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Save the Rider</p>
                        <p class="text-sm text-gray-500">Click <strong>Save</strong> to create the rider. They will appear in the list and can log in via the mobile app.</p>
                    </div>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Bulk Import Riders</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Download the Template</p>
                        <p class="text-sm text-gray-500">Click the <strong>Template</strong> button to download the Excel template file with the correct format.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Fill in the Template</p>
                        <p class="text-sm text-gray-500">Open the downloaded Excel file, fill in rider details according to the column headers, then save it.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Import the File</p>
                        <p class="text-sm text-gray-500">Click the <strong>Import</strong> button, select your filled Excel file, and upload. All riders will be created in bulk.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== VEHICLE ==================== -->
        <section id="vehicle" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Vehicle Tracking</h2>
            <p class="text-gray-500 mb-6">View real-time information about your fleet vehicles including GPS coordinates, speed, and satellite connection status.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/04-vehicle.png') ?>" alt="Vehicle Tracking">
            </div>

            <h3 class="font-semibold text-lg mb-3">Vehicle Table Columns</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Column</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Name</td><td class="px-4 py-2.5 text-gray-600">Vehicle or tracker device name</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Comment</td><td class="px-4 py-2.5 text-gray-600">Additional notes or description for the vehicle</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Speed</td><td class="px-4 py-2.5 text-gray-600">Current speed of the vehicle (km/h)</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Satellite</td><td class="px-4 py-2.5 text-gray-600">Number of GPS satellites connected to the tracker</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Coordinate</td><td class="px-4 py-2.5 text-gray-600">Current GPS latitude and longitude of the vehicle</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-sm text-blue-700">Vehicle data is fetched live from the <strong>Traxroot</strong> GPS tracking platform. Ensure your company's Traxroot API credentials are configured correctly for data to load.</p>
                </div>
            </div>
        </section>

        <!-- ==================== JOB LIST ==================== -->
        <section id="job-list" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Job List</h2>
            <p class="text-gray-500 mb-6">The core module for creating, assigning, and tracking field service jobs. Jobs are categorized by type.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/05-job-list.png') ?>" alt="Job List">
            </div>

            <h3 class="font-semibold text-lg mb-3">Job Types</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-bolt text-blue-600"></i></div>
                    <p class="font-semibold text-sm">Line Interrupt</p>
                    <p class="text-xs text-gray-400">Type 1</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-plug text-green-600"></i></div>
                    <p class="font-semibold text-sm">Reconnection</p>
                    <p class="text-xs text-gray-400">Type 2</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-exclamation-triangle text-orange-600"></i></div>
                    <p class="font-semibold text-sm">Short Circuit</p>
                    <p class="text-xs text-gray-400">Type 3</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2"><i class="fas fa-unlink text-red-600"></i></div>
                    <p class="font-semibold text-sm">Disconnection</p>
                    <p class="text-xs text-gray-400">Type 4</p>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Job Status Cards</h3>
            <p class="text-sm text-gray-600 mb-4">At the top of each job list page, four summary cards display:</p>
            <ul class="list-disc list-inside text-sm text-gray-600 mb-6 space-y-1">
                <li><strong>Today's Job</strong> &mdash; Number of jobs created for the current date</li>
                <li><strong>Ongoing Job</strong> &mdash; Jobs currently being worked on by a driver</li>
                <li><strong>Upcoming Job</strong> &mdash; Jobs scheduled for future dates</li>
                <li><strong>Reschedule Job</strong> &mdash; Jobs that have been rescheduled</li>
            </ul>

            <h3 class="font-semibold text-lg mb-3">How to Create a New Job</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Select Job Type</p>
                        <p class="text-sm text-gray-500">In the sidebar, expand <strong>Job List</strong> and click the desired job type (e.g., Line Interruption, Reconnection, etc.).</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Click "Add Job"</p>
                        <p class="text-sm text-gray-500">Click the <strong>Add Job</strong> button above the table. A creation form/modal will appear.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Fill in Job Details</p>
                        <p class="text-sm text-gray-500">Enter the job name, select a customer, set the job date, and provide any additional details.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">4</div>
                    <div>
                        <p class="font-medium">Save the Job</p>
                        <p class="text-sm text-gray-500">Click <strong>Save</strong>. The job will appear in the list with <span class="badge bg-gray-100 text-gray-600">Awaiting</span> status until a driver is assigned.</p>
                    </div>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Job Action Buttons</h3>
            <p class="text-sm text-gray-600 mb-3">Each job row contains action buttons (colored circles):</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Icon</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Action</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b"><td class="px-4 py-2.5"><span class="inline-block w-6 h-6 bg-green-500 rounded-full"></span></td><td class="px-4 py-2.5 font-medium">Activate</td><td class="px-4 py-2.5 text-gray-600">Activate/start the job</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5"><span class="inline-block w-6 h-6 bg-indigo-500 rounded-full"></span></td><td class="px-4 py-2.5 font-medium">Assign Driver</td><td class="px-4 py-2.5 text-gray-600">Assign a rider to this job</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5"><span class="inline-block w-6 h-6 bg-yellow-600 rounded-full"></span></td><td class="px-4 py-2.5 font-medium">View Details</td><td class="px-4 py-2.5 text-gray-600">View complete job information</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5"><span class="inline-block w-6 h-6 bg-blue-500 rounded-full"></span></td><td class="px-4 py-2.5 font-medium">Edit</td><td class="px-4 py-2.5 text-gray-600">Modify job details</td></tr>
                        <tr><td class="px-4 py-2.5"><span class="inline-block w-6 h-6 bg-red-500 rounded-full"></span></td><td class="px-4 py-2.5 font-medium">Delete</td><td class="px-4 py-2.5 text-gray-600">Remove the job permanently</td></tr>
                    </tbody>
                </table>
            </div>

            <h3 class="font-semibold text-lg mb-3">Job Status Flow</h3>
            <div class="bg-white rounded-xl p-5 border border-gray-100">
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <span class="badge bg-gray-100 text-gray-700 py-1.5 px-4">Awaiting</span>
                    <i class="fas fa-arrow-right text-gray-300"></i>
                    <span class="badge bg-blue-100 text-blue-700 py-1.5 px-4">Assigned</span>
                    <i class="fas fa-arrow-right text-gray-300"></i>
                    <span class="badge bg-yellow-100 text-yellow-700 py-1.5 px-4">Ongoing</span>
                    <i class="fas fa-arrow-right text-gray-300"></i>
                    <span class="badge bg-green-100 text-green-700 py-1.5 px-4">Finished</span>
                </div>
                <p class="text-xs text-gray-400 mt-3">A driver may also request to reschedule, moving the job to <strong>Reschedule</strong> status pending admin approval.</p>
            </div>
        </section>

        <!-- ==================== RESCHEDULE ==================== -->
        <section id="reschedule" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Reschedule Job</h2>
            <p class="text-gray-500 mb-6">Review and manage job reschedule requests submitted by riders from the mobile app.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/07-reschedule.png') ?>" alt="Reschedule Job">
            </div>

            <h3 class="font-semibold text-lg mb-3">Reschedule Tabs</h3>
            <p class="text-sm text-gray-600 mb-4">The page has three tabs to filter reschedule requests:</p>
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                    <p class="font-semibold text-yellow-800">Pending Approval</p>
                    <p class="text-xs text-yellow-600 mt-1">Awaiting admin decision</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                    <p class="font-semibold text-green-800">Approved</p>
                    <p class="text-xs text-green-600 mt-1">Reschedule accepted</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                    <p class="font-semibold text-red-800">Rejected</p>
                    <p class="text-xs text-red-600 mt-1">Reschedule denied</p>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Process a Reschedule Request</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Go to Reschedule Job</p>
                        <p class="text-sm text-gray-500">Click <strong>Reschedule Job</strong> in the sidebar. The badge number next to "Job List" indicates pending requests.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Review the Request</p>
                        <p class="text-sm text-gray-500">Check the <strong>Pending Approval</strong> tab. Each entry shows the original date, requested reschedule date, driver name, job name, and the reason.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Approve or Reject</p>
                        <p class="text-sm text-gray-500">Use the <strong>Action</strong> buttons on each row to approve or reject the reschedule request. The driver will be notified on the mobile app.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== JOB SUMMARY ==================== -->
        <section id="job-summary" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Job Summary</h2>
            <p class="text-gray-500 mb-6">A consolidated view of all jobs across all types with filtering by period and customer.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/08-job-summary.png') ?>" alt="Job Summary">
            </div>

            <h3 class="font-semibold text-lg mb-3">Filtering Options</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Filter</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Today</td><td class="px-4 py-2.5 text-gray-600">Show jobs from today only</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">7 Days</td><td class="px-4 py-2.5 text-gray-600">Show jobs from the past 7 days (default)</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">1 Month</td><td class="px-4 py-2.5 text-gray-600">Show jobs from the past 30 days</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Custom</td><td class="px-4 py-2.5 text-gray-600">Select a custom From/Until date range</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Customer</td><td class="px-4 py-2.5 text-gray-600">Filter by a specific customer or show all</td></tr>
                    </tbody>
                </table>
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Use</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Select a Period</p>
                        <p class="text-sm text-gray-500">Click one of the period buttons (<strong>Today</strong>, <strong>7 Days</strong>, <strong>1 Month</strong>, or <strong>Custom</strong>).</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Optionally Filter by Customer</p>
                        <p class="text-sm text-gray-500">Select a specific customer from the dropdown, or leave it as <strong>All Customer</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">View Job Details</p>
                        <p class="text-sm text-gray-500">Click the <span class="badge bg-green-100 text-green-700">View</span> button on any row to see the full job details including completion photos.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== REPORT: RIDER ==================== -->
        <section id="report-rider" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Rider Report</h2>
            <p class="text-gray-500 mb-6">Track rider performance including total jobs, completed jobs, ongoing jobs, and cancellations over a selected time period.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/09-report-rider.png') ?>" alt="Rider Report">
            </div>

            <h3 class="font-semibold text-lg mb-3">Report Columns</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Column</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Fullname</td><td class="px-4 py-2.5 text-gray-600">Rider's full name</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Email</td><td class="px-4 py-2.5 text-gray-600">Rider's registered email address</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Cancel Job</td><td class="px-4 py-2.5 text-gray-600">Number of jobs cancelled by this rider <span class="badge bg-red-100 text-red-700">count</span></td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Total Job</td><td class="px-4 py-2.5 text-gray-600">Total number of jobs assigned to the rider</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Complete Job</td><td class="px-4 py-2.5 text-gray-600">Number of successfully completed jobs <span class="badge bg-green-100 text-green-700">count</span></td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Ongoing Job</td><td class="px-4 py-2.5 text-gray-600">Number of jobs currently in progress <span class="badge bg-blue-100 text-blue-700">count</span></td></tr>
                    </tbody>
                </table>
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Use</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Navigate to Report > Rider Report</p>
                        <p class="text-sm text-gray-500">Expand <strong>Report</strong> in the sidebar and click <strong>Rider Report</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Select a Date Range</p>
                        <p class="text-sm text-gray-500">Use the period buttons or set a custom date range, then click <strong>Apply</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Export to Excel</p>
                        <p class="text-sm text-gray-500">Click the <strong>Export Excel</strong> button (top-right) to download the report as an Excel spreadsheet.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== REPORT: JOB ==================== -->
        <section id="report-job" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Job Report</h2>
            <p class="text-gray-500 mb-6">A detailed report of all jobs with filtering by date range and job status. View detailed job information including completion photos.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/10-report-job.png') ?>" alt="Job Report">
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Use</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Navigate to Report > Job Report</p>
                        <p class="text-sm text-gray-500">Expand <strong>Report</strong> in the sidebar and click <strong>Job Report</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Apply Filters</p>
                        <p class="text-sm text-gray-500">Select a period (<strong>Today</strong>, <strong>7 Days</strong>, <strong>1 Month</strong>, or <strong>Custom</strong>) and optionally filter by status (<strong>Awaiting Driver</strong>, <strong>Ongoing Job</strong>, <strong>Finished Job</strong>). Click <strong>Apply</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">View Job Details</p>
                        <p class="text-sm text-gray-500">Click the <span class="badge bg-blue-100 text-blue-700"><i class="fas fa-eye"></i> Detail</span> button on a row to see full information: cancellation history, reschedule history, and job completion photos taken by the rider.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">4</div>
                    <div>
                        <p class="font-medium">Export to Excel</p>
                        <p class="text-sm text-gray-500">Click <strong>Export Excel</strong> to download the filtered results as a spreadsheet file.</p>
                    </div>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Job Status Legend</h3>
            <div class="flex flex-wrap gap-3">
                <span class="badge bg-red-50 text-red-600 border border-red-200 py-1.5 px-4">Awaiting Driver</span>
                <span class="badge bg-yellow-50 text-yellow-700 border border-yellow-200 py-1.5 px-4">Ongoing Job</span>
                <span class="badge bg-green-50 text-green-700 border border-green-200 py-1.5 px-4">Finished Job</span>
            </div>
        </section>

        <!-- ==================== REPORT: CUSTOMER ==================== -->
        <section id="report-customer" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Customer Report</h2>
            <p class="text-gray-500 mb-6">Analyze customer engagement and retention. View total jobs per customer, activity status, and retention metrics.</p>

            <div class="screenshot-container mb-6">
                <img src="<?= base_url('assets/docs/screenshots/11-report-customer.png') ?>" alt="Customer Report">
            </div>

            <h3 class="font-semibold text-lg mb-3">Filter Options</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Filter</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-700 border-b">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Customer</td><td class="px-4 py-2.5 text-gray-600">Select a specific customer or all</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Total Job (Min)</td><td class="px-4 py-2.5 text-gray-600">Filter customers with a minimum number of total jobs</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">First Job / Last Job</td><td class="px-4 py-2.5 text-gray-600">Filter by the date range of their first/last job</td></tr>
                        <tr class="border-b"><td class="px-4 py-2.5 font-medium">Retention Days (Min)</td><td class="px-4 py-2.5 text-gray-600">Minimum days between first and last job</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Status Customer</td><td class="px-4 py-2.5 text-gray-600">Filter by Active or Inactive status</td></tr>
                    </tbody>
                </table>
            </div>

            <h3 class="font-semibold text-lg mb-3">How to Use</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Navigate to Report > Customer Report</p>
                        <p class="text-sm text-gray-500">Expand <strong>Report</strong> in the sidebar and click <strong>Customer Report</strong>.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Set Your Filters</p>
                        <p class="text-sm text-gray-500">Use the filter panel at the top to narrow down customers by name, job count, date range, retention days, or activity status.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Click "Filter"</p>
                        <p class="text-sm text-gray-500">Click the <strong>Filter</strong> button to apply your criteria. Use <strong>Reset</strong> to clear all filters.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">4</div>
                    <div>
                        <p class="font-medium">View Details & Export</p>
                        <p class="text-sm text-gray-500">Click <span class="badge bg-gray-800 text-white"><i class="fas fa-eye"></i> Detail</span> on a row for full customer info. Use <strong>Export Excel</strong> to download the report.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== PROFILE ==================== -->
        <section id="profile" class="doc-section mb-16">
            <h2 class="text-2xl font-bold mb-2">Profile & Password</h2>
            <p class="text-gray-500 mb-6">Manage your account settings, update personal information, and change your password.</p>

            <h3 class="font-semibold text-lg mb-3">View & Update Profile</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Open Profile Modal</p>
                        <p class="text-sm text-gray-500">Click your <strong>name and avatar</strong> in the top-left area of the sidebar (below the logo). A profile modal will appear.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Edit Your Information</p>
                        <p class="text-sm text-gray-500">Update your full name, email, or other profile fields as needed.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Save Changes</p>
                        <p class="text-sm text-gray-500">Click the <strong>Save</strong> or <strong>Update</strong> button to apply your changes.</p>
                    </div>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Change Password</h3>
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Open Profile Modal</p>
                        <p class="text-sm text-gray-500">Click your name/avatar in the sidebar to open the profile modal.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Go to Change Password</p>
                        <p class="text-sm text-gray-500">Look for the <strong>Change Password</strong> section or tab within the profile modal.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="step-number mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Enter New Password</p>
                        <p class="text-sm text-gray-500">Enter your current password, then your new password (and confirm it). Click <strong>Save</strong> to update.</p>
                    </div>
                </div>
            </div>

            <h3 class="font-semibold text-lg mb-3">Sign Out</h3>
            <div class="bg-white rounded-xl p-5 border border-gray-100">
                <p class="text-sm text-gray-600">To sign out, click the <strong><i class="fas fa-sign-out-alt"></i> Sign Out</strong> button in the top-right corner of the navigation bar. You will be redirected to the login page.</p>
            </div>
        </section>

        <!-- Footer -->
        <div class="border-t border-gray-200 pt-8 mt-8 text-center">
            <p class="text-sm text-gray-400">E-FMS User Manual v1.0 &mdash; Last updated: <?= date('F d, Y') ?></p>
            <p class="text-xs text-gray-300 mt-1">Electronic Fleet Management System</p>
        </div>

    </div>
</main>

<script>
// Sidebar active link tracking
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.doc-section');
    const navLinks = document.querySelectorAll('.nav-link');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
                if (activeLink) activeLink.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(section => observer.observe(section));

    // Close mobile sidebar on link click
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                document.getElementById('sidebar').classList.remove('open');
            }
        });
    });
});
</script>

</body>
</html>
