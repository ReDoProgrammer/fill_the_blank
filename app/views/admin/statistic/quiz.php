<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <select name="" id="slSubjects" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <select name="" id="slExams" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search exam...">
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
                <button class="btn btn-outline-warning btn-success text-white" type="button" id="btnExport">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export
                </button>
            </div>
        </div>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h5>Top 3 bài thi có điểm cao nhất</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover mt-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Mã học viên</th>
                        <th scope="col">Tài khoản</th>
                        <th scope="col">Họ tên</th>
                        <th scope="col">Ngày tham gia thi</th>
                        <th scope="col" class="text-center">Thời gian thi</th>
                        <th scope="col" class="text-center">Trả lời đúng (câu)</th>
                        <th scope="col" class="text-center">Điểm</th>
                    </tr>
                </thead>
                <tbody id="tblTop"></tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Lịch sử tham gia thi</h5>
        </div>
        <div class="card-body">
            <!-- statistic Table -->
            <table class="table table-bordered table-striped table-hover mt-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Mã học viên</th>
                        <th scope="col">Tài khoản</th>
                        <th scope="col">Họ tên</th>
                        <th scope="col">Ngày tham gia thi</th>
                        <th scope="col" class="text-center">Thời gian thi</th>
                        <th scope="col" class="text-center">Trả lời đúng (câu)</th>
                        <th scope="col" class="text-center">Điểm</th>
                    </tr>
                </thead>
                <tbody id="tblStatistic"></tbody>
            </table>
        </div>
        <div class="card-footer">
            <!-- Pagination -->
            <nav aria-label="Page navigation example">
                <ul class="pagination pagination-sm">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>


</div>

<div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Chi tiết bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                <div class="containter" id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/public/assets/plugins/xlsx/xlsx.full.min.js"></script>



<script>
    const $slSubjects = $('#slSubjects'),
        $slExams = $('#slExams'),
        $txtKeyword = $('#txtKeyword'),
        pageSize = 10,
        $tblStatistic = $('#tblStatistic'),
        $modal = $('#modal'),
        $modalContent = $('#modalContent'),
        $pagination = $('.pagination');
    let page = 1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    $(document).ready(function() {
        LoadSubjectsHaveExams();
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
                console.log({
                    page
                });

            }
        });
   
    })

    $('#btnSearch').click(function() {
        $('#tblTop').empty();
        $tblStatistic.empty();
        GetTop();
        LoadData();
    })

    $slExams.on('change', function() {
        LoadData();
        GetTop();
    })

    $('#btnExport').click(function() {
        // if ($('#tblStatistic tr').length)
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/statistic/export_quiz_statistic',
            type: 'get',
            dataType: 'json',
            data: {
                exam_id: $slExams.val(),
                keyword: $txtKeyword.val().trim()
            },
            success: function(response) {
                console.log(response);
                exportToExcel(response.history);
            },
            error: function(err) {
                console.log(err);

            }
        })

    })

    $slSubjects.on('change', function() {
        $slExams.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/exam/list_by_subject',
            type: 'get',
            dataType: 'json',
            data: {
                subject_id: $(this).val()
            },
            success: function(exams) {
                exams.forEach(e => {
                    $slExams.append(
                        `<option value="${e.id}">${e.title} - ( ${e.number_of_questions} câu hỏi, ${e.duration} phút)</option>`
                    )
                })
                $slExams.trigger('change');
            },
            error: function(err) {
                console.log(err);

            }
        })
    })

    const GetTop = function() {
        $('#tblTop').empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/statistic/top',
            type: 'get',
            dataType: 'json',
            data: {
                exam_id: $slExams.val()
            },
            success: function(response) {
                const {
                    code,
                    history
                } = response;
                if (code === 200) {
                    let idx = 0;
                    history.forEach(h => {
                        $('#tblTop').append(`
                            <tr>
                                <td>${++idx}</td>
                                <td class = "text-secondary">${h.username}</td>
                                <td class = "fw-bold text-secondary">${h.user_code}</td>
                                <td class = "fw-bold">${h.fullname}</td>
                                <td>${h.quiz_date}</td>
                                <td class = "text-center">${convertSecondsToHMS(h.spent_time)}</td>
                                <td class = "text-center"><span class = "fw-bold text-success">${h.correct_answers}</span>/${h.number_of_questions}</td>
                                <td class = "text-center"><span class = "fw-bold text-danger">${h.got_marks}</span>/${h.marks}</td>
                                <td class = "text-center"><a href="javascript:void(0)" onClick="ViewResult(${h.quiz_result_id},'${h.username}','${h.fullname}')"><i class="fa fa-eye text-info"></i></a></td>
                            </tr>
                        `)
                    })
                }



            },
            error: function(err) {
                console.log(err);

            }
        })
    }
    const LoadData = function() {
        $tblStatistic.empty();
        $pagination.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/statistic/get_quiz_statistic',
            type: 'get',
            dataType: 'json',
            data: {
                page,
                pageSize,
                exam_id: $slExams.val(),
                keyword: $txtKeyword.val().trim()
            },
            success: function(response) {
                const {
                    currentPage,
                    totalPages,
                    history,
                    hasNext,
                    hasPrev
                } = response;
                let idx = (page - 1) * pageSize;

                history.forEach(h => {
                    $tblStatistic.append(`
                        <tr>
                            <td>${++idx}</td>
                            <td class = "text-secondary">${h.username}</td>
                             <td class = "fw-bold text-secondary">${h.user_code}</td>
                            <td class = "fw-bold">${h.fullname}</td>
                            <td>${h.quiz_date}</td>
                            <td class = "text-center">${convertSecondsToHMS(h.spent_time)}</td>
                            <td class = "text-center"><span class = "fw-bold text-success">${h.correct_answers}</span>/${h.number_of_questions}</td>
                            <td class = "text-center"><span class = "fw-bold text-danger">${h.got_marks}</span>/${h.marks}</td>
                            <td class = "text-center"><a href="javascript:void(0)" onClick="ViewResult(${h.quiz_result_id},'${h.username}','${h.fullname}')"><i class="fa fa-eye text-info"></i></a></td>
                        </tr>
                    `)
                })

                $pagination.append(
                    `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#">Previous</a></li>`
                );
                for (i = 1; i <= totalPages; i++) {
                    $pagination.append(
                        ` <li class="page-item ${i == page ? 'active' : ''}"><a class="page-link " href="#">${i}</a></li>`
                    )
                }
                $pagination.append(
                    `<li class="page-item  ${page === totalPages ? 'disabled' : ''}"><a class="page-link" href="#">Next</a></li>`
                );


            },
            error: function(err) {
                console.log(err);

            }
        })
    }

    const ViewResult = function(id, username, fullname) {
        $modalContent.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/ statistic/quiz_detail',
            type: 'get',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {
                const {
                    code,
                    msg,
                    data
                } = response;
                $modal.modal('show');
                $('#modalTitle').html(
                    `Bài thi <span class ="fw-bold text-warning">${$slExams.text()}</span> của <span class ="text-info">${fullname}</span> (<span class = "text-secondary">${username}</span>)`
                )
                let idx = 1;
                data.forEach(q => {
                    let options = q.options;
                    $modalContent.append(`
                            <div class="container mt-3">
                                <div class="question-card p-4 shadow-sm bg-light rounded">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="text-secondary me-3">Câu số ${idx++}:</span>
                                        <h5 class="card-title mb-0">${q.question_text}</h5>
                                    </div>
                                    <p class="card-text">
                                        Điểm: <span class="fw-bold text-danger">${q.mark}</span>
                                        ${q.correct_option == q.user_answer ? '<i class="fa fa-check text-success" aria-hidden="true"></i> Đúng' : '<i class="fa fa-times text-danger" aria-hidden="true"></i> Sai'}
                                    </p>
                                    <ul class="list-group">
                                        <li class="list-group-item answer ${options.correct_option == 1 ? 'correct' : ''} ${q.user_answer == 1 ? 'text-warning fw-bold' : ''}">${options.option_1}</li>
                                        <li class="list-group-item answer ${options.correct_option == 2 ? 'correct' : ''} ${q.user_answer == 2 ? 'text-warning fw-bold' : ''}">${options.option_2}</li>
                                        <li class="list-group-item answer ${options.correct_option == 3 ? 'correct' : ''} ${q.user_answer == 3 ? 'text-warning fw-bold' : ''}">${options.option_3}</li>
                                        <li class="list-group-item answer ${options.correct_option == 4 ? 'correct' : ''} ${q.user_answer == 4 ? 'text-warning fw-bold' : ''}">${options.option_4}</li>
                                    </ul>
                                </div>
                            </div>
                        `);
                })
            },
            error: function(err) {
                console.log(err);

            }
        })
    }



    const LoadSubjectsHaveExams = function() {
        $slSubjects.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/getSubjectsWithQuizzes',
            type: 'get',
            dataType: 'json',
            success: function(response) {
                const {
                    subjects
                } = response;
                subjects.forEach(s => {
                    $slSubjects.append(`<option value="${s.subject_id}">${s.subject_name}</option>`)
                })
                $slSubjects.trigger('change');
            },
            error: function(err) {
                console.log(err);

            }
        })
    }

    const convertSecondsToHMS = function(seconds) {
        // Tính toán giờ, phút và giây
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var secs = seconds % 60;

        // Định dạng theo định dạng giờ:phút:giây
        return (
            (hours < 10 ? "0" : "") + hours + ":" +
            (minutes < 10 ? "0" : "") + minutes + ":" +
            (secs < 10 ? "0" : "") + secs
        );
    }


    function exportToExcel(data) {
        // Nếu không có dữ liệu, không làm gì cả
        if (data.length === 0) return;

        // Hàm chuyển đổi giây thành định dạng hh:mm:ss
        const formatTime = (seconds) => {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        };

        // Lấy thông tin tổng quan từ phần tử đầu tiên (giả sử tất cả đều giống nhau)
        const overview = [{
                "Bài thi": data[0].exam_title
            },
            {
                "Thời gian": `${data[0].duration} phút`
            },
            {
                "Ngày bắt đầu": data[0].begin_date
            },
            {
                "Ngày kết thúc": data[0].end_date
            },
            {
                "Số câu hỏi": data[0].number_of_questions
            },
            {
                "Điểm": data[0].marks
            },
        ];

        // Chuyển đổi dữ liệu chi tiết thành mảng và thêm cột "STT"
        const detailedData = data.map((item, index) => ({
            "STT": index + 1, // Tạo cột STT bắt đầu từ 1
            "Tài khoản": item.username,
            "Mã học viên": item.user_code,
            "Họ và tên": item.fullname,
            "Ngày làm bài": item.quiz_date,
            "Thời gian làm bài": formatTime(item.spent_time), // Chuyển đổi số giây thành hh:mm:ss
            "Điểm": item.got_marks,
            "Trả lời đúng": item.correct_answers
        }));

        // Tạo workbook và worksheet
        const workbook = XLSX.utils.book_new();

        // Tạo worksheet cho thông tin tổng quan
        const overviewWorksheet = XLSX.utils.aoa_to_sheet([
            ["Bài thi", data[0].exam_title],
            ["Thời gian", `${data[0].duration} phút`],
            ["Ngày bắt đầu", data[0].begin_date],
            ["Ngày kết thúc", data[0].end_date],
            ["Số câu hỏi", data[0].number_of_questions],
            ["Điểm", data[0].marks],
        ]);

        // Định dạng cho phần "Bài thi"
        const cellAddress = 'B2'; // Ô chứa giá trị "Bài thi"

        // Kiểm tra và tạo định dạng cho ô "Bài thi"
        if (overviewWorksheet[cellAddress]) {
            overviewWorksheet[cellAddress].s = {
                font: {
                    bold: true,
                    sz: 14, // Kích thước chữ lớn hơn
                    color: {
                        rgb: "FF0000"
                    } // Thay đổi màu sắc nếu cần
                },
                alignment: {
                    horizontal: "center"
                }, // Căn giữa nếu cần
            };
        }

        // Thêm worksheet thông tin tổng quan
        XLSX.utils.book_append_sheet(workbook, overviewWorksheet, "Thông tin tổng quan");

        // Thêm worksheet cho thông tin chi tiết
        const detailWorksheet = XLSX.utils.json_to_sheet(detailedData);
        XLSX.utils.book_append_sheet(workbook, detailWorksheet, "Chi tiết kết quả");

        // Xuất file Excel
        XLSX.writeFile(workbook, `${data[0].exam_title}.xlsx`);
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