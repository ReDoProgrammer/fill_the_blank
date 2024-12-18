<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] == null) {
        header("Location: " . BASE_URL . "/teacher/auth/login");
        exit();
    }
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/sweetalert2/sweetalert2@11.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.js"></script>
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
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://via.placeholder.com/30" alt="Profile" class="rounded-circle me-2">
                            <?php echo $_SESSION['teacher_logged_in']['fullname'] ?? $_SESSION['teacher_logged_in']['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/teacherauth/profile">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/teacher/teacherauth/logout">Logout</a></li>
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
        $(document).ready(function() {
            RenderOwnClasses();
        })



        function RenderOwnClasses() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/teaching/ownclasses',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    const $classes = $('#mn-Classes');
                    const $questions = $('#mn-Questions');
                    const $exams = $('#mn-Exams');
                    $classes.empty();
                    $questions.empty();
                    $exams.empty();
                    const {
                        classese
                    } = response;
                    // Mảng để lưu các môn học đã được thêm vào $questions
                    let addedSubjects = [];
                    classese.forEach(c => {
                        let subjects = JSON.parse(c.subjects);
                        // Nối tất cả subject_name lại với nhau, cách nhau bằng dấu phẩy
                        var subjectNames = subjects.map(function(item) {
                            return item.subject_name;
                        }).join(', ');

                        // Thêm class vào $classes
                        $classes.append(`<li id="${c.teaching_id}"><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/classroom/index?r=${c.teaching_id}" target="_self">${c.class_name} - ${subjectNames} - ${c.school_year}</a></li>`);

                        // Thêm exam vào $exams
                        $exams.append(`<li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/exam/index?r=${c.teaching_id}" target="_self">${c.class_name} - ${subjectNames} - ${c.school_year}</a></li>`);

                        // Kiểm tra và thêm mỗi môn học vào $questions chỉ một lần
                        subjects.forEach(function(subject) {
                            if (!addedSubjects.includes(subject.subject_name)) {
                                // Thêm môn học vào $questions nếu chưa có trong addedSubjects
                                $questions.append(`<li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/teacher/quiz/index?s=${subject.subject_id}&meta=${subject.meta}" target="_self">${subject.subject_name}</a></li>`);
                                // Đánh dấu môn học này là đã thêm
                                addedSubjects.push(subject.subject_name);
                            }
                        });
                    });

                    $(document).trigger('menuRendered'); // Kích hoạt sự kiện tùy chỉnh sau khi render xong

                },
                error: function(err) {
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