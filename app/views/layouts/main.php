<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (!isset($_SESSION['user_logged_in'])) {
        header("Location: " . BASE_URL . "/user/auth/login");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill the blanks - User zone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/jquery.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/font-awesome/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.css">
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/sweetalert2/sweetalert2@11.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/assets/plugins/toast/jquery.toast.js"></script>
    <style>
        .navbar-custom {
            background-color: #4CAF50;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link,
        .navbar-custom .dropdown-item {
            color: white;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .dropdown-item:hover {
            color: #f1f1f1;
        }

        .main-content {
            width: 100%;
            padding: 10px;
        }

        .sidebar {
            width: 350px;
            background-color: #2c3e50;
            color: #ecf0f1;
            height: 100vh;
            padding-top: 20px;
            position: relative;
            overflow-y: auto;
        }

        .sidebar .group {
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .sidebar .group-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            color: #ecf0f1;
        }

        .sidebar .group-title i {
            margin-right: 10px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 10px 20px;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
        }

        .sidebar ul li:hover {
            background-color: #34495e;
        }

        .sidebar ul li i {
            margin-right: 10px;
            font-size: 14px;
        }

        /* Dropdown style */
        .sidebar ul li .dropdown {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #34495e;
            width: 100%;
            z-index: 1000;
            padding-left: 15px;
        }

        .sidebar ul li .dropdown li {
            padding: 8px 20px;
        }

        .sidebar ul li .dropdown li:hover {
            background-color: #3b5998;
        }

        .sidebar ul li.active .dropdown {
            display: flex;
        }

        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s, background-color 0.3s;
            display: inline-block;
            width: 100%;
            padding: 8px 20px;
            border-radius: 4px;
        }

       
        .sidebar ul li a:hover {
            color: #ffffff;
            background-color: #3b5998;
        }

        .sidebar ul li i {
            font-size: 16px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .sidebar ul li .dropdown li a {
            color: #bdc3c7;
            padding-left: 30px;
        }

        .sidebar ul li .dropdown li a:hover {
            color: #ffffff;
            background-color: #3b5998;
        }

        .footer {
            background-color: #2c3e50;
            color: #ddd;
            padding: 15px 5px;
            text-align: right;
            font-weight: bold;

        }
        .dropdown-menu{
            background-color: #2c3e50;
        }

        .dropdown-menu li a:hover{
            color:red !important;
        }
       

        
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
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
                            Lịch sử
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class ="dropdown-item" href="<?php echo BASE_URL; ?>/history/index">Lịch sử ôn bài</a>
                            </li>
                            <li>
                                <a class ="dropdown-item" href="<?php echo BASE_URL; ?>/history/quiz">Lịch sử thi</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://via.placeholder.com/30" alt="Profile" class="rounded-circle me-2">
                            <?php echo $_SESSION['user_logged_in']['fullname'] ?? $_SESSION['user_logged_in']['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- <li><a class="dropdown-item" href="#">Settings</a></li> -->
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/userauth/profile">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/userauth/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="group">
                <div class="group-title">
                    <i class="fas fa-layer-group"></i> ÔN TẬP
                </div>
                <ul id="ulPractice">
                    <li onclick="toggleDropdown(this)">
                        <i class="fas fa-bars"></i> Menu 1
                        <ul class="dropdown">
                            <li><i class="fas fa-chevron-right"></i> Submenu 1</li>
                            <li><i class="fas fa-chevron-right"></i> Submenu 2</li>
                            <li><i class="fas fa-chevron-right"></i> Submenu 3</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="group">
                <div class="group-title">
                    <i class="fas fa-layer-group"></i> BÀI THI
                </div>
                <ul id="ulExams">
                    <li onclick="toggleDropdown(this)">
                        <i class="fas fa-bars"></i> Menu 1
                        <ul class="dropdown">
                            <li><i class="fas fa-chevron-right"></i> Submenu 1</li>
                            <li><i class="fas fa-chevron-right"></i> Submenu 2</li>
                            <li><i class="fas fa-chevron-right"></i> Submenu 3</li>
                        </ul>
                    </li>
                </ul>
            </div>



        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php echo $content; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const $ulExams = $('#ulExams'),
            $ulPractice = $('#ulPractice');
        $(document).ready(function () {
            RenderOwnExams();
            RenderOwnSubjects();
        });



        function toggleDropdown(element) {
            const dropdown = element.querySelector('.dropdown');
            if (dropdown) {
                element.classList.toggle('active');
            }
        }

        function RenderOwnSubjects() {
            $ulPractice.empty();
            $.ajax({
                url: '<?php echo BASE_URL; ?>/home/OwnSubjects',
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    const {
                        code,
                        msg,
                        subjects
                    } = response;
                    if (code == 200 && subjects != null && subjects.length > 0) {
                        subjects.forEach(s => {
                            let li = ` <li onclick="toggleDropdown(this)">
                                            <i class="fas fa-bars"></i> ${s.subject.name}
                                                    <ul class="dropdown">`;
                            s.lessions.forEach(l => {
                                li += ` <li><a href="javascript:void(0)" onClick="CheckCanDoPractice(${s.subject.subject_id}, ${l.lession_id}, '<?php echo BASE_URL; ?>/question/index?s=${s.subject.meta}-${s.subject.subject_id}&l=${l.lession_meta}-${l.lession_id}')" ><i class="fas fa-chevron-right"></i> ${l.lession_name}</a></li>`;
                            })
                            li += `</ul></li>`;
                            $ulPractice.append(li);
                        })
                    }

                },
                error: function (err) {
                    console.log(err.responseText);

                }
            })
        }

        function RenderOwnExams() {
            $ulExams.empty();
            $.ajax({
                url: '<?php echo BASE_URL; ?>/home/OwnExams',
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    const $exams = $('#mn-Exams');
                    $exams.empty();
                    console.log(response);

                    const {
                        code,
                        result
                    } = response;

                    if (code == 200 && result != null && result.length > 0) {

                        result.forEach(l => {
                            let li = `<li onclick="toggleDropdown(this)">
                                        <i class="fas fa-bars"></i> ${l.subject.name}
                                            <ul class="dropdown">`;
                            l.exams.forEach(e => {
                                li += `<li><i class="fas fa-chevron-right"></i> <a href="<?php echo BASE_URL; ?>/exam/index?id=${e.exam_id}&s=${l.subject.meta}-${l.subject.id}">${e.title}</a></li>`;
                            })
                            li += `</ul></li>`;
                            $ulExams.append(li);

                        })
                    }
                },
                error: function (err) {
                    console.error(err.responseText);
                }
            });
        }

        //hàm kiểm tra khả năng luyện ôn tập hay không?
        function CheckCanDoPractice(subject_id, lession_id, url) {

            // console.log({subject_id,lession_id,url});
            // return;

            $.ajax({
                url: '<?php echo BASE_URL; ?>/question/canTakeTest',
                type: 'get',
                dataType: 'json',
                data: {
                    subject_id,
                    lession_id
                },
                success: function (response) {
                    const {
                        code,
                        msg
                    } = response;
                    // console.log(response);

                    if (code == 401) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            html: `<p class = "text-danger fw-bold">${msg}</p>`
                        }).then(_ => {
                            window.location.replace(`<?php echo BASE_URL; ?>/userauth/login`);
                        });
                    } else {
                        if (code != 200) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                html: `<p class = "text-danger fw-bold">${msg}</p>`
                            }).then(_ => {
                                // window.location.replace('<?php echo BASE_URL; ?>');
                                // return;
                            })
                        } else {
                            window.location.replace(url);
                        }
                    }

                },
                error: function (err) {
                    console.log(err);
                }
            })
        }
    </script>
</body>
<div class="footer">
    <script src="<?php echo BASE_URL; ?>/public/assets/js/script.js"></script>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> HuyDao's production. All rights reserved.</p>
    </div>
</div>

</html>