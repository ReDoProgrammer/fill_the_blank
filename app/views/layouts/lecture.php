<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill the blanks - Teacher zone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/jquery.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/font-awesome/font-awesome.min.css">

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
            opacity: 0;
            /* Để dropdown ban đầu không hiển thị */
            visibility: hidden;
            /* Ẩn dropdown khi chưa hover */
            transition: opacity 0.3s ease, visibility 0s linear 0.3s;
            /* Thêm hiệu ứng mượt mà */
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

        .navbar-custom .dropdown:hover .dropdown-menu {
            opacity: 1;
            /* Khi hover vào dropdown, hiển thị dropdown */
            visibility: visible;
            /* Đảm bảo dropdown hiển thị */
            transition: opacity 0.3s ease, visibility 0s linear 0s;
            /* Mượt mà khi mở dropdown */
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
            opacity: 0;
            /* Để dropdown ban đầu không hiển thị */
            visibility: hidden;
            /* Ẩn dropdown khi chưa hover */
            transition: opacity 0.3s ease, visibility 0s linear 0.3s;
            /* Thêm hiệu ứng mượt mà */
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

        .sidebar .dropdown:hover .dropdown-menu {
            opacity: 1;
            /* Khi hover vào dropdown, hiển thị dropdown */
            visibility: visible;
            /* Đảm bảo dropdown hiển thị */
            transition: opacity 0.3s ease, visibility 0s linear 0s;
            /* Mượt mà khi mở dropdown */
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
            <a class="navbar-brand" href="#">
                <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" style="height:30px; width:auto;"
                    alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="ownClasses" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Danh sách lớp
                        </a>
                        <ul class="dropdown-menu" id="mn-Classes">
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
                            <img src="https://via.placeholder.com/30" alt="Profile" class="rounded-circle me-2">
                            <?php echo $_SESSION['teacher_logged_in']['fullname'] ?? $_SESSION['teacher_logged_in']['username']; ?>
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
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/teacher"><i class="fa fa-home" aria-hidden="true"></i>
                    Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="classesDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Ngân hàng câu hỏi
                </a>
                <ul class="dropdown-menu" id="mn-Questions"></ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="examsDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Đề thi
                </a>
                <ul class="dropdown-menu" id="mn-Exams">
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
        <?php echo $content; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            RenderOwnClasses();
        })
        function RenderOwnClasses() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/teaching/ownclasses',
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    const $classes = $('#mn-Classes');
                    const $questions = $('#mn-Questions');
                    const $exams = $('#mn-Exams');
                    $classes.empty();
                    $questions.empty();
                    $exams.empty();
                    const {
                        classes
                    } = response;
                    console.log(classes);

                    classes.forEach(c => {
                        $classes.append(`<li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/classroom/index?s=${c.subject_meta}-${c.subject_id}" target="_self">${c.class_name} - ${c.subject_name} - ${c.school_year}</a></li>`);
                        $questions.append(`<li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/quiz/index?s=${c.subject_meta}-${c.subject_id}" target="_self">${c.subject_name}</a></li>`);
                        $exams.append(`<li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/exam/index?s=${c.subject_meta}-${c.subject_id}" target="_self">${c.subject_name}</a></li>`);
                    })

                },
                error: function (err) {
                    console.log(err.responseText);

                }
            })
        }
        const RenderExams = function () {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/teaching/ownclasses',
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    const $exams = $('#sidebar-exams');
                    $exams.empty();
                    const {
                        classes
                    } = response;
                    classes.forEach(c => {
                        $exams.append(`<a href="<?php echo BASE_URL; ?>/teacher/exam/index?s=${c.subject_meta}-${c.subject_id}" target="_self">${c.subject_name} (${c.school_year})</a>`);
                    })

                },
                error: function (err) {
                    console.log(err.responseText);

                }
            })
        }


        function GetQuestionsBySubject(subject_id, url) {
            window.location.replace(url); // Chuyển hướng khi click vào một subject
        }
    </script>
</body>

</html>