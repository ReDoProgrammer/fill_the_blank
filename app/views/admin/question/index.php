<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.snow.css">
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.js"></script>

<div class="container">
    <!-- Subject Dropdown and Search/Buttons Section -->
    <div class="row justify-content-end mb-4">
        <div class="col-md-4 form-group">
            <label for="selSubject">Môn học:</label>
            <select id="selSubject" class="form-control">
                <!-- Các môn học sẽ được tải từ server và hiển thị ở đây -->
            </select>
        </div>
        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search question..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
        <div class="col col-md-4 align-self-end text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa fa-plus text-white"></i> Add New
            </button>
            <button class="btn btn-danger text-white" id="deleteMany">
                <i class="fa fa-times" aria-hidden="true"></i> Delete (<span id="deleteCount" class="fw-bold">0</span>)
            </button>
        </div>
    </div>

    <!-- Questions Table -->
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th scope="col" class="text-center" style="width:100px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="checkAll">
                        <label class="form-check-label" for="checkAll">
                            All
                        </label>
                    </div>
                </th>
                <th scope="col">#</th>
                <th scope="col">Bài học</th>
                <th scope="col">Câu hỏi</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody id="tblData"></tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-end pagination-sm">
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
        </ul>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm mới câu hỏi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="selLession">Bài học:</label>
                                <select id="selLession" class="form-control">
                                    <!-- lessions will be dynamically loaded based on selected subject -->
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="txtQuestion">Câu hỏi:</label>
                                <div id="editor" style="height: 200px;"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label for="blankContainer">Đáp án:</label>
                                <div id="blankContainer">
                                    <div class="blank-element row mb-2" data-position="1">
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" placeholder="Position" value="1"
                                                readonly>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" placeholder="Nội dung đáp án">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-warning text-white" id="btnAddElement">
                                    <i class="fa fa-plus text-white"></i> Thêm đáp án
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSubmit">Submit changes</button>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    var keyword = '', page = 1, pageSize = 10, id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    const $btnSearch = $('#btnSearch'), $btnSubmit = $('#btnSubmit'), $keyword = $('#txtKeyword'), $table = $('#tblData'), $pagination = $('.pagination'), $modal = $('#modal'), $modalTittle = $('#modalTittle');
    const $deleteMany = $('#deleteMany');
    var quill = new Quill('#editor', {
       
        theme: 'snow',
        placeholder: 'Nội dung câu hỏi. Phần chỗ trống để điền vui lòng để 3 dấu _ (___)'
    });


    const $subjects = $('#selSubject'), $lessions = $('#selLession'),
        $question = $('#txtQuestion');
    $(document).ready(function () {
        LoadSubjects(); // Tải danh sách môn học

        // sự kiện xử lý phím tab để thụt vào đầu dòng
        quill.root.addEventListener('keydown', function (event) {
            if (event.key === 'Tab') {
                event.preventDefault();
                quill.insertText(quill.getSelection(), '\t');
            }
        });

        $('.pagination').on('click', '.page-link', function (e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).data('page'); // Lấy giá trị data-page
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

        $deleteMany.prop('disabled', true);
        // Khi checkAll được chọn hoặc bỏ chọn
        $('#checkAll').on('change', function () {
            // Lấy trạng thái của checkbox checkAll
            var isChecked = $(this).prop('checked');

            // Chọn hoặc bỏ chọn tất cả các checkbox trong bảng dựa trên trạng thái của checkAll
            $('#tblData input.form-check-input').prop('checked', isChecked);

            // Cập nhật số lượng checkbox được chọn
            updateDeleteCount();
        });

        // Khi bất kỳ checkbox nào trong bảng được chọn hoặc bỏ chọn
        $('#tblData').on('change', 'input.form-check-input', function () {
            // Kiểm tra xem tất cả các checkbox trong bảng có được chọn không
            var allChecked = $('#tblData input.form-check-input').length === $('#tblData input.form-check-input:checked').length;

            // Cập nhật trạng thái của checkbox checkAll
            $('#checkAll').prop('checked', allChecked);

            // Cập nhật số lượng checkbox được chọn
            updateDeleteCount();
        });

    });

    // Thêm một phần tử blank mới
    $('#btnAddElement').click(function () {
        let position = $('#blankContainer .blank-element').length + 1;
        $('#blankContainer').append(`
                <div class="blank-element row mb-2" data-position="${position}">
                    <div class="col-md-2">
                        <input type="number" class="form-control" placeholder="Position" value="${position}" readonly>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Nội dung đáp án">
                    </div>
                </div>
            `);
    });

    // Xử lý sự kiện nhấn nút Submit
    $('#btnSubmit').click(function () {
        if ($lessions.val() === undefined || $lessions.val().trim().length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng chọn bài học cho câu hỏi',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        const questionText = quill.root.innerHTML; // Lấy nội dung từ Quill
        const subjectId = $('#selSubject').val();
        const lessonId = $('#selLession').val();
        const blanks = [];



        if (!subjectId) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng chọn môn học của câu hỏi để tạo mới câu hỏi',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        if (!lessonId) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng chọn bài học của câu hỏi để tạo mới câu hỏi',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        $('#blankContainer .blank-element').each(function () {
            const position = $(this).data('position');
            const blankText = $(this).find('input[type="text"]').val().trim();
            if (blankText) {
                blanks.push({ position, blank_text: blankText });
            }
        });

        if (questionText.length === 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Nội dung câu hỏi không thể để trống!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        const blankOccurrences = (questionText.match(/___/g) || []).length;
        const hasConsecutiveBlanks = /___{2,}/.test(questionText);
        if (hasConsecutiveBlanks) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Nội dung câu hỏi không được chứa các dấu gạch chân liên tiếp như \'______\'. Vui lòng kiểm tra lại. Mỗi lần chỉ có 3 kí tự _',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        if (blankOccurrences < 1) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Nội dung câu hỏi phải chứa ít nhất một lần xuất hiện của \'___\'.',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        if (blankOccurrences !== blanks.length) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: `Số đáp án (${blanks.length}) không trùng khớp với số lần xuất hiện của '___' (${blankOccurrences}) trong nội dung của câu hỏi.`,
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }


        const url = `<?php echo BASE_URL; ?>/admin/question/${id <= 0 ? 'add' : 'update'}`;
        const data = { question: questionText, lession_id: parseInt($lessions.val()), blanks: JSON.stringify(blanks) };
        if (id > 0) data.id = id;
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                const { code, msg } = response;
                $.toast({
                    heading: code == 201 || code == 200 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                    text: msg,
                    icon: code == 201 || code == 200 ? 'success' : 'error',
                    loader: true,
                    loaderBg: '#9EC600'
                });
                if (code == 201 || code == 200) {
                    $('#modal').modal('hide');
                    LoadData(); // Tải lại dữ liệu câu hỏi
                }
            },
            error: function (xhr, status, error) {
                console.log({ xhr, status, error });
                $.toast({
                    heading: 'Lỗi hệ thống',
                    text: 'Đã xảy ra lỗi khi gửi dữ liệu.',
                    icon: 'error',
                    loader: true,
                    loaderBg: '#9EC600'
                });
            }
        });
    });


    // Tải danh sách môn học
    function LoadSubjects() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/allSubjects',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                const { subjects } = response;
                subjects.forEach(s => {
                    $subjects.append(`<option value="${s.id}">${s.name}</option>`);
                });
                $subjects.trigger('change');
            },
            error: function (err) {
                console.log(err.responseText);
            }
        });
    }

    // Tải danh sách bài học dựa trên môn học đã chọn
    function Loadlessions(subject_id) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/lession/getBySubject`,
            type: 'GET',
            data: { subject_id },
            dataType: 'json',
            success: function (response) {
                const $lessions = $('#selLession');
                $lessions.empty().append('<option value="">Chọn bài học</option>');
                $.each(response.lessions, function (index, lession) {
                    $lessions.append(`<option value="${lession.id}">${lession.name}</option>`);
                });
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    // Tải danh sách câu hỏi và phân trang
    function LoadData() {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/question/search`,
            type: 'GET',
            data: { page, pageSize, keyword, subjectId: $subjects.val() },
            dataType: 'json',
            success: function (response) {
                const { questions, totalPages, currentPage, pageSize, totalRecords } = response;
                $table.empty();
                $pagination.empty();
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $.each(questions, function (index, question) {

                    // Thay thế ký tự tab bằng khoảng trắng trong HTML
                    var formattedQuestionText = question.question_text
                        .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng

                        // Thay thế khoảng trắng đầu dòng sau thẻ <p>
                        .replace(/<p>\s+/g, function (match) {
                            return '<p>' + '&nbsp;'.repeat(match.length - 3); // 3 là độ dài của <p> và 1 khoảng trắng mặc định
                        });

                    // Tạo một phần tử tạm thời để xử lý nội dung HTML
                    var $tempDiv = $('<div>').html(formattedQuestionText);
                    $table.append(`
                        <tr class = "align-middle" id = "${question.id}">
                            <td class="text-center">                           
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="check_${question.id}">
                                </div>                           
                            </td>
                            <td>${++idx}</td>
                            <td class="fw-bold text-info">${question.lession_name}</td>
                            <td>${$tempDiv.html()}</td>
                            <td class="text-end">
                                <a href="javascript:void(0)" onClick="UpdateQuestion(${question.id})"><i class="fa fa-edit text-warning"></i></a>
                                <a href="javascript:void(0)" onClick="DeleteQuestion(${question.id})"><i class="fa fa-trash-o text-danger"></i></a>
                            </td>
                        </tr>
                    `);
                });

                $pagination.append(`<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true" data-page="0">Previous</a>
                </li>`);
                for (let i = 1; i <= totalPages; i++) {
                    $pagination.append(`<li class="page-item ${currentPage === i ? "active" : ""}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`);
                }
                $pagination.append(`<li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
                    <a class="page-link" href="#" data-page="${totalPages}">Next</a>
                </li>`);
            }, error: function (err) {
                console.log(err);
            }
        });
    }

    // Xử lý khi chọn môn học
    $subjects.change(function () {
        Loadlessions(parseInt($subjects.val()));
        LoadData(); // Tải câu hỏi cho môn học đã chọn
    });

    // Xử lý khi nhấn nút tìm kiếm
    $('#btnSearch').click(function () {
        keyword = $('#txtKeyword').val().trim();
        LoadData();
    });

    $deleteMany.on('click', function () {
        // Lấy mảng các ID của các hàng có checkbox được chọn
        var selectedIds = $('#tblData input.form-check-input:checked').map(function () {
            return parseInt($(this).closest('tr').attr('id'));
        }).get();

        Swal.fire({
            title: `Bạn thực sự muốn xoá <span class = "fw-bold text-danger">${$('#deleteCount').text()}</span> câu hỏi này?`,
            html: "<p class = 'text-danger'>Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!</p>",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                selectedIds.forEach(id => {
                    DeleteSingle(id);
                })
                $('#deleteCount').text('0');
                $("#checkAll").prop("checked", false);
            }
        });
    });


    function UpdateQuestion(id) {
        this.id = id;
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/question/detail',
            type: 'GET',
            data: { id },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                const { code, msg, detail } = response;
                if (code == 200) {
                    $modal.modal('show');
                    $modalTittle.text('Cập nhật thông tin câu hỏi');
                    $lessions.val(detail.question.lession_id);
                    quill.root.innerHTML = detail.question.question_text;
                    $('#blankContainer').empty();

                    detail.blanks.forEach(b => {
                        $('#blankContainer').append(`
                            <div class="blank-element row mb-2" data-position="${b.position}">
                                <div class="col-md-2">
                                    <input type="number" class="form-control" placeholder="Position" value="${b.position}" readonly>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" placeholder="Nội dung đáp án" value="${b.blank_text}">
                                </div>
                            </div>
                        `);
                    })
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function DeleteQuestion(id, name) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá câu hỏi này?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                DeleteSingle(id);
            }
        });
    }

    const DeleteSingle = function (id) {

        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/question/delete',
            type: 'POST',
            data: { id },
            dataType: 'json',
            success: function (response) {

                const { code, msg } = response;
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
            error: function (err) {
                console.log(err);

                console.log(err.responseText);
            }
        });
    }

    $modal.on('hide.bs.modal', function (e) {
        $modalTittle.text('Thêm mới câu hỏi');
        quill.root.innerHTML = '';
        id = -1;
        $('#blankContainer').empty();
        $('#blankContainer').append(`
                            <div class="blank-element row mb-2" data-position="1">
                                <div class="col-md-2">
                                    <input type="number" class="form-control" placeholder="Position" value="1" readonly>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" placeholder="Nội dung đáp án" value="">
                                </div>
                            </div>
                        `);
    });

    // Hàm cập nhật số lượng checkbox được chọn
    function updateDeleteCount() {
        var checkedCount = $('#tblData input.form-check-input:checked').length;
        $deleteMany.prop('disabled', checkedCount > 0 ? false : true);

        $('#deleteCount').text(checkedCount);
    }

    const formatDisplay = function (inputHTML) {
        // Giữ nguyên nội dung HTML của q.question mà không render
        var formattedQuestionText = $('<div>').text(inputHTML).html();
        return formattedQuestionText
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
            .replace(/<p>\s+/g, function (match) {
                return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
            });
    }
</script>