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
        <a href="<?php echo BASE_URL; ?>" class="logo"><img src="<?php echo BASE_URL; ?>/public/assets/images/logo.png"
                style="height:30px; width:auto;" alt="Logo"></a>
        <div class="navbar-links">
            <a href="<?php echo BASE_URL; ?>">Home</a>
            <?php if (isset($_SESSION['user_logged_in'])) { ?>
                <a href="<?php echo BASE_URL; ?>/history/index">Lịch sử ôn bài</a>
                <a href="<?php echo BASE_URL; ?>/history/quiz">Lịch sử thi</a>
                <a href="<?php echo BASE_URL; ?>/userauth/profile">Tài khoản</a>
                <a href="<?php echo BASE_URL; ?>/userauth/logout">Đăng xuất</a>
            <?php } else { ?>
                <a href="<?php echo BASE_URL; ?>/userauth/login">Đăng nhập</a>
            <?php } ?>
        </div>
        <?php if (isset($_SESSION['user_logged_in'])) { ?>
            <div class="profile-menu">
                <span
                    class="profile-name"><?php echo $_SESSION['user_logged_in']['fullname'] ?? $_SESSION['user_logged_in']['username']; ?></span>
            </div>
        <?php } ?>
        <button class="sidebar-toggle"><i class="fa fa-bars" aria-hidden="true"></i></button>
    </div>

    <div class="sidebar" id="sidebar">
        <!-- Section luyện tập -->
        <h6 class="text-secondary fw-bold p-3">LUYỆN TẬP</h6>
        <div class="sidebar-section" id="sidebar-practice">

            <!-- Subject and lession will be appended here -->
        </div>
        <!-- Section thi -->
        <h6 class="text-secondary fw-bold p-3">THI TRẮC NGHIỆM</h6>
        <div class="sidebar-section" id="sidebar-exam">
            <!-- Subject will be appended here -->
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
        const $practiceMenu = $('#sidebar-practice');
        const $examMenu = $('#sidebar-exam');
       
        $(document).ready(async function () {
            try {
                await RenderFillTheBlank();
            } catch (error) {
                console.error('Error during initial loading:', error);
            }
        });



        const RenderSubjectsHaveExams = function (retries = 3) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/home/SubjectsHaveExams',
                type: 'get',
                dataType: 'json',
                success: async function (data) {
                    $examMenu.empty();
                    const { sidebar } = data;
                    if (!sidebar || sidebar.length === 0) {
                        console.warn('No subjects available for exams.');
                        return;
                    }

                    const appendExamSubjects = async (subject) => {
                        const $subjectLink = $(`<a style="padding-left:25px;" href="<?php echo BASE_URL; ?>/exam/index?s=${subject.meta}-${subject.id}">${subject.name}</a>`);
                        $examMenu.append($subjectLink);
                    };

                    for (const s of sidebar) {
                        await appendExamSubjects(s);
                    }
                },
                error: function (err) {
                    console.error('Error loading subjects:', err);
                    if (retries > 0) {
                        console.log(`Retrying... (${3 - retries + 1}/3)`);
                        setTimeout(() => {  // Thêm delay trước khi retry
                            RenderSubjectsHaveExams(retries - 1);
                        }, 2000);  // Retry sau 2 giây
                    } else {
                        console.error('Failed to load subjects after 3 attempts.');
                    }
                }
            });
        };

        const RenderFillTheBlank = function () {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/home/SubjectsHaveQuestions',
                type: 'get',
                dataType: 'json',
                success: async function (data) {
                    $practiceMenu.empty();
                    $examMenu.empty();
                    const { sidebar } = data;

                    const appendPracticeSubjectsAndLessions = async (subject) => {
                        return new Promise((resolve) => {
                            const $subjectButton = $(`
                        <button class="dropdown-btn">${subject.name}
                            <i class="fa fa-caret-down"></i>
                        </button>
                    `);
                            const $dropdownContainer = $('<div class="dropdown-container"></div>');

                            subject.lessions.forEach((lession) => {
                                const $lessionLink = $(`<a href="javascript:void(0)" onClick="DoFillTheBlank(${subject.id}, ${lession.id}, '<?php echo BASE_URL; ?>/question/index?s=${subject.meta}-${subject.id}&l=${lession.meta}-${lession.id}')">${lession.name}</a>`);
                                $dropdownContainer.append($lessionLink);
                            });

                            $practiceMenu.append($subjectButton);
                            $practiceMenu.append($dropdownContainer);

                            resolve();
                        });
                    };

                    for (const s of sidebar) {
                        await appendPracticeSubjectsAndLessions(s);
                    }

                    var sidebarToggle = document.querySelector('.sidebar-toggle');
                    var sidebarMenu = document.querySelector('.sidebar');
                    var mainContent = document.querySelector('.main-content');
                    var dropdowns = document.getElementsByClassName("dropdown-btn");

                    sidebarToggle.addEventListener('click', function () {
                        sidebarMenu.classList.toggle('collapsed');
                        if (sidebar.classList.contains('collapsed')) {
                            sidebarMenu.style.width = '0';
                            Array.from(dropdowns).forEach(function (dropdown) {
                                var dropdownContent = dropdown.nextElementSibling;
                                dropdownContent.style.maxHeight = null;
                            });
                            mainContent.style.marginLeft = '0';
                        } else {
                            sidebarMenu.style.width = '220px';
                            mainContent.style.marginLeft = '240px';
                        }
                    });

                    // Xử lý mở rộng khi nhấn vào thẻ a
                    $(document).on('click', '#sidebar-practice a', function () {
                        var $dropdownContainer = $(this).closest('.dropdown-container');
                        var $dropdownButton = $dropdownContainer.prev('.dropdown-btn');

                        // Mở rộng phần dropdown container nếu chưa được mở
                        if ($dropdownContainer.css('max-height') === '0px' || !$dropdownContainer.css('max-height')) {
                            // Mở rộng container và gán class "active" cho button tương ứng
                            $dropdownButton.addClass('active');
                            $dropdownContainer.css('max-height', $dropdownContainer.prop('scrollHeight') + 'px');
                        }
                    });

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

                    await RenderSubjectsHaveExams();
                },
                error: function (err) {
                    console.log(err);
                }
            });
        };

        function DoFillTheBlank(subject_id, lession_id, url) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/question/canTakeTest',
                type: 'get',
                dataType: 'json',
                data: { subject_id, lession_id },
                success: function (response) {
                    const { code, msg } = response;

                    if (code == 401) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            html: `<p class = "text-danger fw-bold">${msg}</p>`
                        }).then(_ => {
                            window.location.replace(`<?php echo BASE_URL; ?>/userauth/login`);
                        });
                    } else {
                        window.location.replace(url);
                    }

                },
                error: function (err) {
                    console.log(err);
                }
            })
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
        /* Chiếm toàn bộ không gian trống */
    }

    .footer {
        text-align: center;
        padding: 10px 0;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
    }
</style>