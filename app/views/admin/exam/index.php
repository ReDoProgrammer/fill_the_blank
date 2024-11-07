<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.snow.css">
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.js"></script>


<link href="<?php echo BASE_URL; ?>/public/assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.css"
    rel="stylesheet">
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/datetimepicker/moment.min.js"></script>
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/datetimepicker/tempusdominus-bootstrap-4.min.js"></script>



<div class="container">

    <!-- Subject Dropdown and Search/Buttons Section -->
    <div class="row justify-content-end mb-4">
        <div class="col-md-2 form-group">
            <label for="selSubject">Môn học:</label>
            <select id="selSubject" class="form-control">
                <!-- Các môn học sẽ được tải từ server và hiển thị ở đây -->
            </select>
        </div>

        <div class="col-md-2">
            <label for="">Từ ngày</label>
            <div class="input-group date" id="from_date">
                <input type="text" class="form-control" />
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <label for="">Tới ngày</label>
            <div class="input-group date" id="to_date">
                <input type="text" class="form-control" />
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search exam..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
        <div class="col-md-2 align-self-end text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                    <i class="fa fa-plus text-white"></i> Add New
                </button>
            </div>
        </div>

    </div>

    <!--  Table -->
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Banner</th>
                <th scope="col">Đề thi</th>
                <th scope="col">Chế độ đề</th>
                <th scope="col">Thời gian</th>
                <th scope="col">Số câu hỏi</th>
                <th scope="col">Điểm</th>
                <th scope="col">Ngày bắt đầu</th>
                <th scope="col">Ngày kết thúc</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody id="tblData"></tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" id="pagination">
        <ul class="pagination justify-content-end pagination-sm"></ul>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm mới cuộc thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:550px; overflow-y:auto;">
                    <form class="container">
                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="">Tiêu đề cuộc thi</label>
                                <input type="text" placeholder="Tiêu đề cuộc thi" id="txtTitle" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="txtQuestion" class="fw-bold mb-3">Thể lệ cuộc thi</label>
                                <div id="editor" style="height: 150px;"></div>
                            </div>
                        </div>
                        <hr />
                        <div class="container mt-5">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="end_date_input">Ngày bắt đầu</label>
                                    <div class="input-group date" id="begin_date" data-target-input="nearest">
                                        <input type="text" id="begin_date_input"
                                            class="form-control datetimepicker-input" data-target="#begin_date" />
                                        <div class="input-group-append" data-target="#begin_date"
                                            data-toggle="datetimepicker">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date_input">Ngày kết thúc</label>
                                    <div class="input-group date" id="end_date" data-target-input="nearest">
                                        <input type="text" id="end_date_input" class="form-control datetimepicker-input"
                                            data-target="#end_date" />
                                        <div class="input-group-append" data-target="#end_date"
                                            data-toggle="datetimepicker">
                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 form-group">
                                <label for="">Số câu hỏi:</label>
                                <input type="number" value="10" id="txtNumberOfQuestions" class="form-control text-end"
                                    min="0" max="100">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Thời lượng (phút)</label>
                                <input type="number" min="10" max="500" value="15" id="txtDuration"
                                    class="form-control text-end">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Chế độ ra đề</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input rbtExamMode" type="radio"
                                                    name="rbtExamMode" id="rbtRandom" checked>
                                                <label class="form-check-label" for="ExamMode">
                                                    Đề ngẫu nhiên
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input rbtExamMode" type="radio"
                                                    name="rbtExamMode" id="rbtUsingConfig">
                                                <label class="form-check-label" for="ExamMode">
                                                    Sử dụng cấu hình đề
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="row mb-3">
                            <input type="file" id="imageInput" accept="image/*"><br><br>
                            <img id="imagePreview" src="" alt="Image Preview" style="max-width: 200px; display: none;"
                                class="img-fluid img-thumbnail">
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSubmit">Submit changes</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalUsingConfig" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thiết lập cấu hình đề thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <select class="form-select" id="slConfigs"></select>
                        <button class="btn btn-outline-secondary btn-warning text-white" type="button"
                            id="btnUsingConfig">
                            <i class="fa fa-check"></i>
                            Sử dụng cấu hình
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modalQuestions" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Chi tiết đề thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="height:550px; overflow-y:auto;">
                    <div class="container" id="divQuestions"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>







    <script>
    var page = 1,
        pageSize = 10,
        id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    var questions = [];
    const $btnSearch = $('#btnSearch'),
        $btnSubmit = $('#btnSubmit'),
        $keyword = $('#txtKeyword'),
        $table = $('#tblData'),
        $pagination = $('.pagination'),
        $modal = $('#modal'),
        $modalTittle = $('#modalTitle'),
        $questionsList = $('#questions');
    const $subjects = $('#selSubject'),
        $slConfigs = $('#slConfigs'),
        $slQuestions = $('#slQuestions');

    var modeValue;
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Mô tả thể lệ cuộc thi'
    });

    const $from_date = $('#from_date'),
        $to_date = $('#to_date');

    $(document).ready(function() {
        var today = new Date();
        var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        var formattedDate = ('0' + firstDayOfMonth.getDate()).slice(-2) + '/' + ('0' + (firstDayOfMonth
            .getMonth() + 1)).slice(-2) + '/' + firstDayOfMonth.getFullYear();
        $('#from_date input').val(formattedDate);

        formattedDate = ('0' + today.getDate()).slice(-2) + '/' + ('0' + (today.getMonth() + 1)).slice(-2) +
            '/' + today.getFullYear();
        $('#to_date input').val(formattedDate);



        // thiết lập cho ngày bắt đầu và kết thúc khi tạo cuộc thi
        var now = moment(); // Thời gian hiện tại
        // Khởi tạo datetimepicker cho begin_date
        $('#begin_date').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            defaultDate: now, // Đặt giá trị mặc định là thời gian hiện tại
            minDate: now // Không cho phép chọn ngày quá khứ
        });

        // Khởi tạo datetimepicker cho end_date
        $('#end_date').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            defaultDate: now.add(1, 'hours'), // Đặt giá trị mặc định là 1 giờ sau thời gian hiện tại
            useCurrent: false // Để end_date không tự động lấy giá trị của begin_date
        });

        // Ràng buộc end_date phải lớn hơn begin_date
        $('#begin_date').on('change.datetimepicker', function(e) {
            $('#end_date').datetimepicker('minDate', e.date);
        });

        $('#end_date').on('change.datetimepicker', function(e) {
            $('#begin_date').datetimepicker('maxDate', e.date);
        });



        LoadSubjects(); // Tải danh sách môn học


        $('#imageInput').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });

        $pagination.on('click', '.page-link', function(e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).text(); // Lấy giá trị data-page

            if (currentPage !== undefined) {
                if (currentPage !== 0 && currentPage !== pageNumber) {
                    page = currentPage;
                    LoadData();
                } else {
                    if (currentPage === 0 && page > 1) {
                        page--;
                        LoadData();
                    } else if (currentPage == pageNumber && page < pageNumber) {
                        page++;
                        LoadData();
                    }
                }
            }
        });

    });

    $('#btnUsingConfig').click(function() {
        if (!$slConfigs.val()) {
            Swal.fire({
                icon: "error",
                title: `Không có cấu hình phù hợp với đề thi có ${$('#txtNumberOfQuestions').val()} câu hỏi, ${$mark.val()} điểm`,
                html: '<p class = "text-danger">Vui lòng thay đổi điểm số hoặc liên hệ admin để bổ sung cấu hình hoặc chọn chế độ ra đề khác</p>',
            });
            $('#rbtRandom').prop('checked', true);

        } else {
            modeValue = parseInt($slConfigs.val());
            $.toast({
                heading: 'Successfully',
                text: 'Áp dụng cấu hình cho đề thi thành công!',
                icon: 'success',
                loader: true,
                loaderBg: '#9EC600'
            });
        }
        $('#modalUsingConfig').modal('hide');
    })
    $('#modalCustomize').on('shown.bs.modal', function() {
        $('#slQuestions').select2('open');
    });


    $(document).on('input', '#txtNumberOfQuestions', function() {
        LoadConfigs();
    })
    const LoadConfigs = function() {
        $slConfigs.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/config/listBySubject',
            type: 'get',
            dataType: 'json',
            data: {
                subject_id: $subjects.val(),
                number_of_questions: parseInt($('#txtNumberOfQuestions').val())
            },
            success: function(response) {
                const {
                    code,
                    configs
                } = response;

                configs.forEach(cf => {
                    $slConfigs.append(
                        `<option value="${cf.id}">${cf.title} - [${cf.number_of_questions} câu, ${cf.marks} điểm ]</option>`
                    );
                })
            },
            error: function(err) {
                console.log(err);

            }
        })
    }

    const ViewQuestions = function(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/exam/getQuestionsList',
            type: 'get',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {
                $('#modalQuestions').modal('show');
                $('#divQuestions').empty();
                const {
                    questions
                } = response;
                let idx = 1;
                questions.forEach(q => {
                    console.log(q);

                    let options = JSON.parse(q.options);
                    console.log(options);


                    $('#divQuestions').append(`
                                <div class="container mt-5">
                                    <div class="question-card p-4 shadow-sm bg-light rounded">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="text-secondary me-3">Câu số ${idx++}:</span>
                                            <h5 class="card-title mb-0">${formatDisplay(q.question)}</h5>
                                        </div>
                                        <p class="card-text">Điểm: <span class="fw-bold text-danger">${q.mark}</span></p>
                                        <ul class="list-group">
                                            <li class="list-group-item answer ${options.correct_option == 1 ? 'correct' : ''}">${formatDisplay(options.option_1)}</li>
                                            <li class="list-group-item answer ${options.correct_option == 2 ? 'correct' : ''}">${formatDisplay(options.option_2)}</li>
                                            <li class="list-group-item answer ${options.correct_option == 3 ? 'correct' : ''}">${formatDisplay(options.option_3)}</li>
                                            <li class="list-group-item answer ${options.correct_option == 4 ? 'correct' : ''}">${formatDisplay(options.option_4)}</li>
                                        </ul>
                                    </div>
                                </div>
                            `);
                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    $('input[type=radio][name=rbtExamMode]').change(function() {
        if (this.id == 'rbtUsingConfig') {
            $('#modalUsingConfig').modal('show');
        } else {
            modeValue = 0;
        }
    });


    $btnSubmit.click(function() {

        // Lấy dữ liệu từ các trường trong form
        var title = $('#txtTitle').val();
        var description = quill.root.innerHTML; // Quill.js editor content
        var begin_date = $('#begin_date_input').val();
        var end_date = $('#end_date_input').val();
        var number_of_questions = parseInt($('#txtNumberOfQuestions').val());
        var duration = parseInt($('#txtDuration').val());
        var mark = $('#txtMarks').val();


        const mode = modeValue > 0 ? modeValue : 0;

        var random_questions = $('#random_questions').is(':checked') ? 1 : 0;
        var image = $('#imageInput')[0].files[0]; // Chọn file hình ảnh

        //ràng buộc  dữ liệu
        if (title.trim().length === 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng nhập tiêu đề cuộc thi!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }
        // Tạo FormData để gửi dữ liệu
        var formData = new FormData();
        if (id > 0) formData.append('id', id);
        formData.append('subject_id', $subjects.val());
        formData.append('title', title);
        formData.append('description', description);
        formData.append('begin_date', begin_date);
        formData.append('end_date', end_date);
        formData.append('number_of_questions', number_of_questions);
        formData.append('duration', duration);
        formData.append('random_questions', random_questions);
        formData.append('mode', mode);
        if (image) {
            formData.append('image', image);
        }

        let url = `<?php echo BASE_URL; ?>/admin/exam/${id > 0 ? 'update' : 'create'}`;

        // Gửi dữ liệu bằng AJAX
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log(response);

                const {
                    code,
                    msg
                } = response;
                LoadData();
                if (code === 200 || code === 201) {
                    $.toast({
                        heading: 'SUCCESSFULLY',
                        text: msg,
                        icon: 'success',
                        loader: true,
                        loaderBg: '#9EC600'
                    });
                    $('#modal').modal('hide');
                } else {
                    Swal.fire({
                        title: "BAD REQUEST",
                        html: `<p class = "fw-bold text-danger">${msg}</p>`,
                        icon: "error"
                    });
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown); // Hiển thị lỗi trong console
            }

        });
    });

    // Tải danh sách môn học
    function LoadSubjects() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/allSubjects',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const {
                    subjects
                } = response;
                subjects.forEach(s => {
                    $subjects.append(`<option value="${s.id}">${s.name}</option>`);
                });
                $subjects.trigger('change');
            },
            error: function(err) {
                console.log(err.responseText);
            }
        });
    }

    // Xử lý khi chọn môn học
    $subjects.change(function() {
        LoadData();
        LoadConfigs();
    });

    // Xử lý khi nhấn nút tìm kiếm
    $('#btnSearch').click(function() {
        page = 1;
        LoadData();
    });



    function LoadData() {
        $table.empty();
        $pagination.empty();

        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/exam/search',
            type: 'get',
            dataType: 'json',
            data: {
                page,
                pageSize,
                subject_id: $subjects.val(),
                keyword: $keyword.val().trim()
            },
            success: function(response) {
                const {
                    exams,
                    currentPage,
                    totalPages
                } = response;
                console.log(exams);

                let idx = (page - 1) * pageSize;
                for (i = 1; i <= totalPages; i++) {
                    if (i == currentPage) {
                        $pagination.append(`
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">${i}</span>
                            </li>
                        `)
                    } else {
                        $pagination.append(`
                                                <li class="page-item"><a class="page-link" href="#">${i}</a></li>           
                                            `)
                    }
                }

                exams.forEach(e => {
                    $table.append(`
                        <tr class="align-middle">
                            <td>${++idx}</td>\
                            <td class="text-info fw-bold">
                                <img src = "<?php echo BASE_URL; ?>${e.thumbnail.trim().length > 0 ? e.thumbnail : '/public/assets/images/no_image.jpg'}" class = "img-thumbnail" width="200" height="100"/>
                            </td>
                            <td class="text-info fw-bold">${e.title}</td>
                            <td class="text-center">${e.mode > 0 ? 'Sử dụng cấu hình' : 'Ngẫu nhiên'}</td>
                            <td>${e.duration}</td>
                            <td class="text-center">${e.number_of_questions}</td>
                            <td class="text-center">${e.total_marks}</td>
                            <td>${e.begin_date}</td>
                            <td>${e.end_date}</td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onClick="UpdateExam(${e.exam_id})"><i class="fa fa-edit text-warning"></i></a>
                                ${(e.random_of_questions == 0 && e.available) ? `<a href="<?php echo BASE_URL; ?>/admin/exam/customize?id=${e.id}"><i class="fa fa-list text-success" aria-hidden="true"></i></a>` : ''}
                                <a href="javascript:void(0)" onClick="DeleteExam(${e.exam_id})"><i class="fa fa-trash-o text-danger"></i></a>
                                <a href="javascript:void(0)" onClick="ViewExamDetail(${e.exam_id})"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a href="javascript:void(0)" onClick="ViewQuestions(${e.exam_id})"><i class="fa fa-list text-white bg-success"></i></a>
                            </td>
                        <tr/>
                    `);


                })
            },
            error: function(err) {
                console.log(err);

            }
        })

    }



    function UpdateExam(id) {
        this.id = id;
        GetDetail(id)
            .then(detail => {
                const {
                    exam
                } = detail;

                $('#txtTitle').val(exam.title);
                quill.clipboard.dangerouslyPasteHTML(exam.description);
                $('#begin_date_input').val(exam.begin_date);
                $('#end_date_input').val(exam.end_date);
                $('#txtNumberOfQuestions').val(exam.number_of_questions);
                $('#txtDuration').val(exam.duration);
                if (exam.thumbnail && exam.thumbnail.trim().length > 0) {
                    $('#imagePreview').attr('src', `<?php echo BASE_URL; ?>${exam.thumbnail}`);
                    $('#imagePreview').show();
                }
                if (exam.mode > 0) {
                    $('#rbtUsingConfig').attr('checked', true);
                } else {
                    $('#rbtRandom').attr('checked', true);
                }
                $modalTittle.text('Cập nhật thông tin cuộc thi');
                $modal.modal('show');
            })
            .catch(err => {
                console.log(err);

            })
    }

    function DeleteExam(id) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá đề thi này?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/admin/exam/delete',
                    type: 'POST',
                    data: {
                        id
                    },
                    dataType: 'json',
                    success: function(response) {
                        const {
                            code,
                            msg
                        } = response;
                        $.toast({
                            heading: code == 200 ? 'THÀNH CÔNG' : `LỖI: ${code}`,
                            text: msg,
                            icon: code == 200 ? 'success' : 'error',
                            loader: true,
                            loaderBg: '#9EC600'
                        });
                        if (code == 200) {
                            LoadData(); // Tải lại dữ liệu sau khi xóa
                        }
                    },
                    error: function(err) {
                        console.log(err.responseText);
                    }
                });
            }
        });
    }

    function ViewExamDetail(id) {
        GetDetail(id)
            .then(detail => {
                const {
                    exam
                } = detail;
                $('#txtTitle').val(exam.title);
                quill.clipboard.dangerouslyPasteHTML(exam.description);
                $('#begin_date_input').val(exam.begin_date);
                $('#end_date_input').val(exam.end_date);
                $('#txtNumberOfQuestions').val(exam.number_of_questions);
                $('#txtDuration').val(exam.duration);
                if (exam.thumbnail && exam.thumbnail.trim().length > 0) {
                    $('#imagePreview').attr('src', `<?php echo BASE_URL; ?>${exam.thumbnail}`);
                    $('#imagePreview').show();
                }
                if (exam.mode > 0) {
                    $('#rbtUsingConfig').attr('checked', true);
                } else {
                    $('#rbtRandom').attr('checked', true);
                }
                $modalTittle.text('Cập nhật thông tin cuộc thi');
                $modal.modal('show');
                $btnSubmit.hide();
            })
            .catch(err => {
                console.log(err);

            })
    }

    function GetDetail(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/admin/exam/detail',
                type: 'get',
                dataType: 'json',
                data: {
                    id
                },
                success: function(response) {
                    return resolve(response)
                },
                error: function(err) {
                    return reject(err);
                }
            })
        })
    }

    $modal.on('hide.bs.modal', function(e) {
        id = -1;
        $modalTittle.text('Thêm mới cuộc thi');
        $btnSubmit.show();
        quill.root.innerHTML = '';
        $('#txtTitle').val('');
        $('#txtNumberOfQuestions').val(10);
        $('#txtDuration').val(10);
        $('#imagePreview').attr('src', null);
        $('#imagePreview').hide();
        $('#rbtRandom').attr('checked', true);
        modeValue = 0;

    });
    $('#modalUsingConfig').on('hide.bs.modal', function(e) {
        if (!modeValue > 0) {
            $('#rbtRandom').prop('checked', true);
        }
    });

    const formatDisplay = function(inputHTML) {
        // Giữ nguyên nội dung HTML của q.question mà không render
        var formattedQuestionText = $('<div>').text(inputHTML).html();
        return formattedQuestionText
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
            .replace(/<p>\s+/g, function(match) {
                return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
            });
    }
    </script>


    <style>
    .question-card {
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        margin-bottom: 1rem;
        padding: 1.25rem;
    }

    .answer {
        cursor: pointer;
    }

    .answer.correct {
        background-color: #d4edda;
        /* bg-success */
        color: #155724;
    }

    .answer.incorrect {
        background-color: #f8d7da;
        /* bg-danger */
        color: #721c24;
    }
    </style>