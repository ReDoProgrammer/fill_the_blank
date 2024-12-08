<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap 5 Layout with Navbar and Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #4CAF50;
            /* Nền Navbar */
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link,
        .navbar-custom .dropdown-item {
            color: white;
            /* Màu chữ trong Navbar */
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .dropdown-item:hover {
            color: #f1f1f1;
            /* Màu chữ khi hover */
        }

        .navbar-custom .dropdown-menu {
            background-color: #f8f9fa;
            /* Màu nền của dropdown */
            border-radius: 5px;
            /* Bo góc cho dropdown */
            border: 1px solid #ddd;
            /* Border xám nhẹ */
        }

        .navbar-custom .dropdown-item {
            color: #333;
            /* Màu chữ trong item */
        }

        .navbar-custom .dropdown-item:hover {
            background-color: #28a745;
            /* Màu nền khi hover vào item - xanh tươi */
            color: white;
            /* Màu chữ khi hover vào item */
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 15px 0;
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .sidebar .dropdown-menu {
            background-color: #343a40;
            /* Màu nền cho dropdown trong Sidebar */
            border-radius: 5px;
            border: 1px solid #444;
            /* Border xám nhạt */
        }

        .sidebar .dropdown-item {
            color: #fff;
        }

        .sidebar .dropdown-item:hover {
            background-color: #ffb74d;
            /* Màu nền khi hover vào item trong Sidebar - cam sáng */
            color: #343a40;
            /* Màu chữ khi hover vào item trong Sidebar */
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>


</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MyApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Features
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Feature 1</a></li>
                            <li><a class="dropdown-item" href="#">Feature 2</a></li>
                            <li><a class="dropdown-item" href="#">Feature 3</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Resources
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Documentation</a></li>
                            <li><a class="dropdown-item" href="#">API</a></li>
                            <li><a class="dropdown-item" href="#">Tutorials</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://via.placeholder.com/30" alt="Profile"
                                class="rounded-circle me-2"> John Doe
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Sidebar</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="#">Dashboard</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="classesDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Classes
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Class 1</a></li>
                    <li><a class="dropdown-item" href="#">Class 2</a></li>
                    <li><a class="dropdown-item" href="#">Class 3</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="examsDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Exams
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Exam 1</a></li>
                    <li><a class="dropdown-item" href="#">Exam 2</a></li>
                    <li><a class="dropdown-item" href="#">Exam 3</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Statistics</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to the Dashboard</h1>
        <p>This is the main content area.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>