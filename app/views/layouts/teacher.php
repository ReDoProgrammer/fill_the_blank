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
    <title><?php echo $title ?? 'Fill the blank'; ?> - <?php echo $subject ?? 'Môn học'; ?> -
        <?php echo $lession ?? 'Bài tập'; ?>
    </title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/styles.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/jquery.js"></script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/sweetalert2/sweetalert2@11.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.js"></script>
</head>

<body>
    <div class="top-navbar">
        <a href="<?php echo BASE_URL; ?>/teacher" class="logo">
            <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" style="height:30px; width:auto;" alt="Logo">
        </a>
        <div class="navbar-links">
            <a href="<?php echo BASE_URL; ?>/teacher">Home</a>

            <!-- Dropdown được thêm trước "Tài khoản" -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </li>

            <?php if (isset($_SESSION['teacher_logged_in'])) { ?>
                <a href="<?php echo BASE_URL; ?>/teacher/teacherauth/profile">Tài khoản</a>
                <a href="<?php echo BASE_URL; ?>/teacher/teacherauth/logout">Đăng xuất</a>
            <?php } else { ?>
                <a href="<?php echo BASE_URL; ?>/teacher/teacherauth/login">Đăng nhập</a>
            <?php } ?>
        </div>

        <!-- Hiển thị tên giáo viên -->
        <?php if (isset($_SESSION['teacher_logged_in'])) { ?>
            <div class="profile-menu">
                <span class="profile-name">
                    <span class="text-warning">GV:</span>
                    <?php echo $_SESSION['teacher_logged_in']['fullname'] ?? $_SESSION['teacher_logged_in']['username']; ?>
                </span>
            </div>
        <?php } ?>

        <button class="sidebar-toggle">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </button>
    </div>



    <div class="sidebar" id="sidebar">
        <h6 class="text-secondary fw-bold p-3">NGÂN HÀNG CÂU HỎI</h6>
        <div class="sidebar-section" id="sidebar-subjects">
        </div>
        <h6 class="text-secondary fw-bold p-3">ĐỀ THI</h6>
        <div class="sidebar-section" id="sidebar-exams">
        </div>
        <h6 class="text-secondary fw-bold p-3">PHÂN TÍCH - THỐNG KÊ</h6>
        <div class="sidebar-section" id="sidebar-statistic">
        </div>
    </div>

    <div class="main-content">
        <?php echo $content; ?>
    </div>

    <div class="footer">
        <script src="<?php echo BASE_URL; ?>/public/assets/js/script.js"></script>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> HuyDao's production. All rights reserved.</p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            RenderQuestionsBankList(); // Gọi hàm render sidebar ngay khi trang tải
            RenderOwnClasses();
            RenderExams();
        });

        const RenderOwnClasses = function() {

        }
        const RenderExams = function() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/teaching/ownclasses',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    const $exams = $('#sidebar-exams');
                    $exams.empty();
                    const {
                        classes
                    } = response;
                    classes.forEach(c => {
                        $exams.append(`<a href="<?php echo BASE_URL; ?>/teacher/exam/index?s=${c.subject_meta}-${c.subject_id}" target="_self">${c.subject_name} (${c.school_year})</a>`);
                    })

                },
                error: function(err) {
                    console.log(err.responseText);

                }
            })
        }

        const RenderQuestionsBankList = function() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/subject/HaveLessions', // Lấy dữ liệu từ backend
                type: 'get',
                dataType: 'json',
                success: async function(data) {
                    const $subjects = $('#sidebar-subjects');
                    $subjects.empty(); // Làm trống sidebar trước khi thêm dữ liệu mới

                    const {
                        subjects
                    } = data;
                    subjects.forEach(s => {
                        $subjects.append(`<a href="javascript:void(0)" onClick="GetQuestionsBySubject(${s.id}, '<?php echo BASE_URL; ?>/teacher/quiz/index?s=${s.meta}-${s.id}')">${s.name}</a>`);
                    });
                },
                error: function(err) {
                    console.log(err); // In lỗi nếu có
                }
            });
        };

        function GetQuestionsBySubject(subject_id, url) {
            window.location.replace(url); // Chuyển hướng khi click vào một subject
        }
    </script>

</body>

</html>

<style>
    html,
    body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        flex: 1;
        /* Bắt buộc chiếm không gian còn lại */
    }

    .footer {
        text-align: center;
        padding: 10px 0;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
    }

    .navbar-links {
        display: flex;
        align-items: center;
        gap: 15px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        display: none;
        background-color: #343a40;
        color: white;
        border: none;
        padding: 0.5rem 0;
        
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .nav-item:hover .dropdown-menu {
        display: block;
    }
</style>