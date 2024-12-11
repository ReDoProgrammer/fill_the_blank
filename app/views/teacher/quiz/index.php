<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.snow.css">
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/quill/quill.js"></script>
<script src="<?php echo BASE_URL; ?>/public/assets/plugins/xlsx/xlsx.full.min.js"></script>

<div class="container">
    <!-- Subject Dropdown and Search/Buttons Section -->
    <div class="row justify-content-end mb-4 mt-3">
        <div class="col col-md-4 offset-md-4 align-self-end text-end">
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
            <button id="btnImportExcel" class="btn btn-success text-white fw-bold">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Import
            </button>
            <input type="file" id="excelFile" accept=".xlsx, .xls" style="display: none;">

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
                <th scope="col">Câu hỏi</th>
                <th scope="col" class="text-center">Điểm</th>
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
                <div class="modal-body" style="height:550px; overflow-y:auto;">
                    <div class="container">

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">

                                <div class="row mb-3 align-items-center">
                                    <div class="col-auto">
                                        <span class="text-info">
                                            Câu hỏi
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" id="txtMark" style="width:100px;" class="form-control"
                                            placeholder="Điểm" />
                                    </div>
                                    <div class="col-auto">
                                        <span>điểm</span>
                                    </div>
                                </div>




                                <div id="editor" style="height: 150px;"></div>
                            </div>
                        </div>

                        <hr />
                        <div class="row mb-3 mt-3">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="">Đáp án A:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="correctoptions" id="AIzCorrect"> Đáp án đúng
                                    </div>
                                </div>
                                <div id="optiona" style="height: 100px;"></div>
                            </div>

                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="">Đáp án B:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="correctoptions" id="BIzCorrect"> Đáp án đúng
                                    </div>
                                </div>
                                <div id="optionb" style="height: 100px;"></div>
                            </div>

                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="">Đáp án C:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="correctoptions" id="CIzCorrect"> Đáp án đúng
                                    </div>
                                </div>
                                <div id="optionc" style="height: 100px;"></div>
                            </div>

                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="">Đáp án D:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="correctoptions" id="DIzCorrect"> Đáp án đúng
                                    </div>
                                </div>
                                <div id="optiond" style="height: 100px;"></div>
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
    var page = 1,
        pageSize = 10,
        id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    const $btnSearch = $('#btnSearch'),
        $btnSubmit = $('#btnSubmit'),
        $keyword = $('#txtKeyword'),
        $table = $('#tblData'),
        $pagination = $('.pagination'),
        $modal = $('#modal'),
        $modalTittle = $('#modalTitle');
    const $subjects = $('#selSubject');
    const $btnImportExcel = $('#btnImportExcel');
    const $mark = $('#txtMark');

    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Nội dung câu hỏi. Vui lòng không để trống'
    });

    var quillA = new Quill('#optiona', {
        theme: 'snow',
        placeholder: 'Nội dung đáp án A. Vui lòng không để trống'
    });

    var quillB = new Quill('#optionb', {
        theme: 'snow',
        placeholder: 'Nội dung đáp án B. Vui lòng không để trống'
    });

    var quillC = new Quill('#optionc', {
        theme: 'snow',
        placeholder: 'Nội dung đáp án C. Vui lòng không để trống'
    });

    var quillD = new Quill('#optiond', {
        theme: 'snow',
        placeholder: 'Nội dung đáp án D. Vui lòng không để trống'
    });

    const $aIsCorrect = $('#AIzCorrect'),
        $bIsCorrect = $('#BIzCorrect'),
        $cIsCorrect = $('#CIzCorrect'),
        $dIsCorrect = $('#DIzCorrect');

    const $deleteMany = $('#deleteMany');
    let subject_id;
    $(document).ready(function () {
        // Lấy URL hiện tại
        var url = window.location.href;

        // Tạo đối tượng URLSearchParams
        var urlParams = new URLSearchParams(window.location.search);

        // Lấy giá trị của tham số "s" và "l"
        var s = urlParams.get('s'); // "html-25"

        // Tách số từ tham số "s" và "l"
        subject_id = s.split('-')[1]; // 25

        LoadData();

        // RenderQuestionsBankList();
        $mark.on('input', function () {
            // Chỉ cho phép số và dấu chấm (.), loại bỏ dấu âm (-)
            var value = $(this).val();
            value = value.replace(/[^0-9.]/g, '');

            // Đảm bảo chỉ có một dấu chấm
            if (value.split('.').length > 2) {
                value = value.substring(0, value.lastIndexOf('.'));
            }

            $(this).val(value);
        });


        $pagination.on('click', '.page-link', function (e) {
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




    $btnImportExcel.on('click', function () {
        $('#excelFile').click(); // Kích hoạt sự kiện click của input file ẩn
    });

    $('#excelFile').on('change', async function (e) {
        var file = e.target.files[0];

        if (!file) return; // Kiểm tra nếu không có file nào được chọn

        var reader = new FileReader();

        reader.onload = async function (e) {
            var data = new Uint8Array(e.target.result);
            var workbook = XLSX.read(data, {
                type: 'array'
            });
            var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            var excelRows = XLSX.utils.sheet_to_json(firstSheet, {
                header: 1
            });

            var arrQuiz = [];

            for (var i = 1; i < excelRows.length; i++) { // Bắt đầu từ i=1 để bỏ qua header
                var quiz = {
                    index: excelRows[i][0] || '',
                    title: excelRows[i][1] || '',
                    mark: parseFloat(excelRows[i][2]) || 0,
                    option_1: excelRows[i][3] || '',
                    option_2: excelRows[i][4] || '',
                    option_3: excelRows[i][5] || '',
                    option_4: excelRows[i][6] || '',
                    answer: parseInt(excelRows[i][7]) || 0
                };
                arrQuiz.push(quiz);
            }

            try {
                await sendQuizData(arrQuiz); // Chờ quá trình gửi dữ liệu hoàn thành
                $.toast({
                    heading: 'Successfully',
                    text: 'Import dữ liệu câu hỏi thành công!',
                    icon: 'success',
                    loader: true,
                    loaderBg: '#9EC600'
                });
                LoadData(); // Gọi LoadData sau khi nhập dữ liệu thành công
            } catch (err) {
                $.toast({
                    heading: 'Error',
                    text: 'Có lỗi xảy ra khi nhập dữ liệu!',
                    icon: 'error',
                    loader: true,
                    loaderBg: '#FF3B30'
                });
                console.log(err);
            } finally {
                // Reset lại input file để có thể import lần tiếp theo
                $('#excelFile').val(''); // Reset lại input file
            }
        };

        reader.readAsArrayBuffer(file);
    });

    async function sendQuizData(arrQuiz, startIndex = 0, chunkSize = 100) {
        const chunk = arrQuiz.slice(startIndex, startIndex + chunkSize);

        try {
            const response = await $.ajax({
                url: '<?php echo BASE_URL; ?>/teacher/quiz/import',
                type: 'post',
                dataType: 'json',
                data: {
                    questions: chunk,
                    subject_id
                }
            });

            // Nếu còn dữ liệu, tiếp tục gửi
            if (startIndex + chunkSize < arrQuiz.length) {
                await sendQuizData(arrQuiz, startIndex + chunkSize, chunkSize);
            }
        } catch (err) {
            console.error('Error sending data:', err);
            throw err; // Đẩy lỗi ra ngoài để catch trong phần gọi
        }
    }


    $btnSubmit.click(function () {
        let question = quill.getText().trim();
        let option_a = quillA.getText().trim();
        let option_b = quillB.getText().trim();
        let option_c = quillC.getText().trim();
        let option_d = quillD.getText().trim();

        const correct_option = $aIsCorrect.is(':checked') ? 1 : $bIsCorrect.is(':checked') ? 2 : $cIsCorrect.is(':checked') ? 3 : $dIsCorrect.is(':checked') ? 4 : 0;
        if ($mark.val().trim().length === 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng nhập điểm cho câu hỏi!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        const mark = parseFloat($mark.val());
        // Kiểm tra xem các trường đã được nhập dữ liệu hay chưa
        if (!question || !option_a || !option_b || !option_c || !option_d) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Nội dung câu hỏi và các đáp án không thể để trống!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }


        // Kiểm tra xem đáp án đúng đã được chọn hay chưa
        if (correct_option === 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng chọn đáp án đúng của câu hỏi',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        question = quill.root.innerHTML.trim();
        option_a = quillA.root.innerHTML.trim();
        option_b = quillB.root.innerHTML.trim();
        option_c = quillC.root.innerHTML.trim();
        option_d = quillD.root.innerHTML.trim();

        const url = `<?php echo BASE_URL; ?>/teacher/quiz/${id <= 0 ? 'create' : 'update'}`;
        const data = {
            subject_id: $subjects.val(),
            question,
            option_a,
            option_b,
            option_c,
            option_d,
            correct_option,
            mark
        }
        if (id > 0) data.id = id;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (response) {
                const {
                    code,
                    msg
                } = response;
                if (code == 200 || code == 201) {
                    $.toast({
                        heading: 'SUCCESSFULLY',
                        text: msg,
                        icon: 'success',
                        loader: true,
                        loaderBg: '#9EC600'
                    });
                    LoadData();
                }
                $modal.modal('hide');
                id = -1;
            },
            error: function (err) {
                console.log(err);
            }
        })

    });


    // Xử lý khi nhấn nút tìm kiếm
    $('#btnSearch').click(function () {
        page = 1;
        LoadData();
    });

    function LoadData() {
        $table.empty();
        $pagination.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/quiz/list',
            type: 'get',
            dataType: 'json',
            data: {
                keyword: $keyword.val(),
                page,
                pageSize,
                subject_id
            },
            success: function (response) {
                const {
                    currentPage,
                    pageSize,
                    quizzes,
                    totalPages,
                    totalRecords
                } = response;
                let idx = (currentPage - 1) * pageSize;
                pageNumber = totalPages;

                // Tạo pagination
                for (i = 1; i <= totalPages; i++) {
                    if (i == currentPage) {
                        $pagination.append(`
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">${i}</span>
                        </li>
                    `);
                    } else {
                        $pagination.append(`
                        <li class="page-item"><a class="page-link" href="#">${i}</a></li>
                    `);
                    }
                }

                // Xử lý và hiển thị các quiz
                quizzes.forEach(q => {
                    // Giữ nguyên nội dung HTML của q.question mà không render
                    var formattedQuestionText = $('<div>').text(q.question).html();

                    // Chèn nội dung đã được định dạng vào bảng
                    $table.append(`
                    <tr class="align-middle" id="${q.id}">
                        <td class="text-center">                           
                            <div class="form-check text-center">
                                <input class="form-check-input text-center" type="checkbox" value="" id="check_${q.id}">
                            </div>                           
                        </td>
                        <td>${++idx}</td>
                        <td>${formattedQuestionText}</td> <!-- Hiển thị nội dung HTML dưới dạng chuỗi văn bản -->
                        <td class="text-danger fw-bold text-center">${q.mark}</td>
                        <td class="text-end">
                            <a href="javascript:void(0)" onClick="ViewDetail(${q.id})"><i class="fa fa-eye text-info" aria-hidden="true"></i></a>
                            <a href="javascript:void(0)" onClick="UpdateQuizz(${q.id})"><i class="fa fa-edit text-warning"></i></a>
                            <a href="javascript:void(0)" onClick="DeleteQuiz(${q.id})"><i class="fa fa-trash-o text-danger"></i></a>
                        </td>
                    </tr>
                `);
                });
            },
            error: function (err) {
                console.log(err);
            }
        });
    }


    function UpdateQuizz(id) {
        this.id = id;
        GetDetail(id)
            .then(detail => {
                const {
                    question,
                    mark,
                    options
                } = detail.quiz;

                const {
                    option_1,
                    option_2,
                    option_3,
                    option_4,
                    correct_option
                } = options;

                // Giữ nguyên nội dung HTML của q.question mà không render
                var formattedQuestionText = $('<div>').text(question).html();
                quill.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_1).html();
                quillA.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_2).html();
                quillB.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_3).html();
                quillC.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_4).html();
                quillD.root.innerHTML = formattedQuestionText;

                correct_option == 1 ? $('#AIzCorrect').attr('checked', true) : correct_option == 2 ? $('#BIzCorrect').attr('checked', true) : correct_option == 3 ? $('#CIzCorrect').attr('checked', true) : $('#DIzCorrect').attr('checked', true);
                $mark.val(mark);
                $modalTittle.text('Cập nhật thông tin quiz');
                $modal.modal('show');
            })
            .catch(err => {
                console.log(err);

            })
    }

    const DeleteSingle = function (id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/quiz/delete',
            type: 'POST',
            data: {
                id
            },
            dataType: 'json',
            success: function (response) {
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
            error: function (err) {
                console.log(err.responseText);
            }
        });
    }

    function DeleteQuiz(id) {
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


    function ViewDetail(id) {
        GetDetail(id)
            .then(detail => {
                const {
                    question,
                    mark,
                    options
                } = detail.quiz;
                console.log({
                    question,
                    mark,
                    options
                });

                const {
                    option_1,
                    option_2,
                    option_3,
                    option_4,
                    correct_option
                } = options;

                // Giữ nguyên nội dung HTML của q.question mà không render
                var formattedQuestionText = $('<div>').text(question).html();


                quill.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_1).html();
                quillA.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_2).html();
                quillB.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_3).html();
                quillC.root.innerHTML = formattedQuestionText;

                formattedQuestionText = $('<div>').text(option_4).html();
                quillD.root.innerHTML = formattedQuestionText;

                correct_option == 1 ? $('#AIzCorrect').attr('checked', true) : correct_option == 2 ? $('#BIzCorrect').attr('checked', true) : correct_option == 3 ? $('#CIzCorrect').attr('checked', true) : $('#DIzCorrect').attr('checked', true);
                $mark.val(mark);
                $modalTittle.text('Thông tin quiz');
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
                url: '<?php echo BASE_URL; ?>/teacher/quiz/detail',
                type: 'get',
                dataType: 'json',
                data: {
                    id
                },
                success: function (response) {
                    // Giả sử trong response có thuộc tính 'question' chứa nội dung HTML của câu hỏi
                    if (response && response.question) {
                        // Giữ nguyên nội dung HTML của question mà không render
                        response.formattedQuestionText = $('<div>').text(response.question).html();
                    }

                    return resolve(response); // Trả về response đã được chỉnh sửa
                },
                error: function (err) {
                    return reject(err);
                }
            });
        });
    }


    $modal.on('hide.bs.modal', function (e) {
        $modalTittle.text('Thêm mới câu hỏi');
        $btnSubmit.show();
        quill.root.innerHTML = '';
        quillA.root.innerHTML = '';
        quillB.root.innerHTML = '';
        quillC.root.innerHTML = '';
        quillD.root.innerHTML = '';
        $mark.val('');
        $('#AIzCorrect').attr('checked', false);
        $('#BIzCorrect').attr('checked', false);
        $('#CIzCorrect').attr('checked', false);
        $('#DIzCorrect').attr('checked', false);
    });

    // Hàm cập nhật số lượng checkbox được chọn
    function updateDeleteCount() {
        var checkedCount = $('#tblData input.form-check-input:checked').length;
        $deleteMany.prop('disabled', checkedCount > 0 ? false : true);

        $('#deleteCount').text(checkedCount);
    }
</script>