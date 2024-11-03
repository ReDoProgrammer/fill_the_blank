<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Fill the blank - Administrator zone'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/jquery.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/sweetalert2/sweetalert2@11.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/xlsx/xlsx.full.min.js"></script>

    <script src="<?php echo BASE_URL; ?>/public/assets/js/common.js"></script>
</head>

<body>

    <div class="top-navbar">
        <a href="#" class="logo"><img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png" style="height:30px; width:auto;"
                alt="Logo"></a>

        <a href="#home">Home</a>
        <a href="#news">News</a>
        <a href="#contact">Contact</a>
        <a href="#about">About</a>
        <!-- Thêm nút toggle cho sidebar -->
        <button class="sidebar-toggle"><i class="fa fa-bars" aria-hidden="true"></i></button>
    </div>

    <div class="sidebar">
        <a href="<?php echo BASE_URL; ?>/admin/user"><i class="fa fa-users" aria-hidden="true"></i> Tài khoản</a>
        <a href="<?php echo BASE_URL; ?>/admin/subject"><i class="fa fa-book" aria-hidden="true"></i> Bộ môn</a>
        <a href="<?php echo BASE_URL; ?>/admin/lession"><i class="fa fa-book" aria-hidden="true"></i> Bài học</a>
        <a href="<?php echo BASE_URL; ?>/admin/question"><i class="fa fa-question" aria-hidden="true"></i> Câu hỏi ôn tập</a>
        <a href="<?php echo BASE_URL; ?>/admin/quiz"><i class="fa fa-question" aria-hidden="true"></i> Câu hỏi trắc nghiệm</a>
        <a href="<?php echo BASE_URL; ?>/admin/config"><i class="fa fa-gear"></i> Cấu hình bài thi</a>
        <a href="<?php echo BASE_URL; ?>/admin/exam"><i class="fa fa-list" aria-hidden="true"></i> Bài thi trắc nghiệm</a>
        <hr/>
        <button class="dropdown-btn"><i class="fa fa-bar-chart" aria-hidden="true"></i> Thống kê
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="<?php echo BASE_URL; ?>/admin/statistic/user_statistic">Thống kê tài khoản</a>
            <a href="<?php echo BASE_URL; ?>/admin/statistic/subject_statistic">Thống kê môn học</a>
            <a href="<?php echo BASE_URL; ?>/admin/statistic/lession_statistic">Thống kê bài học</a>
            <a href="<?php echo BASE_URL; ?>/admin/statistic/question_statistic">Thống kê câu hỏi</a>
            <a href="<?php echo BASE_URL; ?>/admin/statistic/quiz_statistic">Thống kê trắc nghiệm</a>
          
        </div>

    </div>

    <div class="main-content" style="height: 630px; overflow-y: auto;">
        <?php echo $content; ?>
    </div>

    <div class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> HuyDao's production. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Xử lý sự kiện click cho toggle sidebar (chỉ dành cho điện thoại)
        var sidebarToggle = document.querySelector('.sidebar-toggle');
        var sidebar = document.querySelector('.sidebar');
        var mainContent = document.querySelector('.main-content');
        var dropdowns = document.getElementsByClassName("dropdown-btn");

        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                // Sidebar được thu gọn
                sidebar.style.width = '0';
                // Ẩn các dropdown khi sidebar thu gọn
                Array.from(dropdowns).forEach(function (dropdown) {
                    var dropdownContent = dropdown.nextElementSibling;
                    dropdownContent.style.maxHeight = null;
                });
                // Mở rộng phần content để bù vào phần trống
                mainContent.style.marginLeft = '0';
            } else {
                // Hiển thị lại sidebar
                sidebar.style.width = '220px'; // Kích thước gốc của sidebar
                // Thu gọn phần content để nhường chỗ cho sidebar
                mainContent.style.marginLeft = '240px';
            }
        });

        var dropdowns = document.getElementsByClassName("dropdown-btn");

        for (var i = 0; i < dropdowns.length; i++) {
            dropdowns[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.maxHeight) {
                    dropdownContent.style.maxHeight = null;
                } else {
                    dropdownContent.style.maxHeight = dropdownContent.scrollHeight + "px";
                }
            });
        }
    </script>
</body>

</html>