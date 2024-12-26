<div class="row mb-3">
    <div class="col-md-3 form-group">
        <select name="" id="slOwnClassese" class="form-control"></select>
    </div>
    <div class="col-md-5 form-group">
        <select name="" id="slSubjects" class="form-control"></select>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm kiếm...">
            <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                <i class="fa fa-search fw-bold"></i> Search
            </button>
            <button class="btn btn-outline-warning btn-success text-white" type="button" id="btnExport">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-3 pt-1">
                        Thống kê ôn tập
                    </div>
                    <div class="col-md-9">
                        <select name="" id="slLessions" class="form-control"></select>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <!-- statistic Table -->
                <table class="table table-bordered table-striped table-hover mt-3" id="tableFTB">

                </table>
            </div>
            <div class="card-footer">
                <!-- Pagination -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination pagination-sm" id="paginationFTB">
                        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <hr>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-3 pt-1">
                        Thống kê thi
                    </div>
                    <div class="col-md-9">
                        <select name="" id="slExams" class="form-control"></select>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <!-- statistic Table -->
                <table class="table table-bordered table-striped table-hover">
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
                    <tbody id="tblExamStatistic"></tbody>
                </table>
            </div>
            <div class="card-footer">
                <!-- Pagination -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination pagination-sm" id="pagination">
                        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</div>


<div class="modal" tabindex="-1" id="modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                <div class="containter p-5" id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
    const $slOwnClasses = $('#slOwnClassese'),
        $slSubjects = $('#slSubjects'),
        $slLessions = $('#slLessions'),
        $slExams = $('#slExams'),
        $txtKeyword = $('#txtKeyword'),
        $search = $('#btnSearch'),
        $modal = $('#modal'),
        $content = $('#modalContent'),
        $table = $('#tableFTB'),
        $export = $('#btnExport'),
        pageSize = 10,
        $tblExamStatistic = $('#tblExamStatistic'),
        $pagination = $('#pagination'),
        $paginationFTB = $('#paginationFTB');

    let page = 1;

    // menuRendered đảm bảo sự kiện này được chạy sau khi menu đã được render trong layout xong
    $(document).on('menuRendered', function () {
        $slOwnClasses.empty();
        $('#mn-Classes li').each(function (index, element) {
            // Lấy nội dung của từng phần tử <li>
            $slOwnClasses.append(`<option value = "${$(this).attr('id')}">${$(this).text()}</option>`);
        });
        $slOwnClasses.trigger('change');
    })



    function StatisticByExam(examId) {
        $tblExamStatistic.empty();
        $pagination.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/statistic/get_quiz_statistic',
            type: 'get',
            dataType: 'json',
            data: {
                page,
                pageSize,
                exam_id: examId,
                keyword: $txtKeyword.val().trim()
            },
            success: function (response) {
                const {
                    currentPage,
                    totalPages,
                    history,
                    hasNext,
                    hasPrev
                } = response;
                let idx = (page - 1) * pageSize;

                history.forEach(h => {
                    $tblExamStatistic.append(`
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
            error: function (err) {
                console.log(err);

            }
        })
    }
    function LoadSubjectStatistic(subjectId) {
        if (subjectId !== undefined) { // kiểm tra biến lessionId đã có giá trị chưa
            $.ajax({
                url: `<?php echo BASE_URL; ?>/teacher/statistic/StatisticBySubject`,
                method: 'get',
                dataType: 'json',
                data: {
                    classId: parseInt($slOwnClasses.val()),
                    subjectId: parseInt($slSubjects.val()),
                    keyword: $txtKeyword.val().trim(),
                    page,
                    pageSize
                },
                success: function (response) {
                    const {
                        code,
                        msg,
                        result
                    } = response;


                    if (code == 200) {
                        $table.append(`<thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">STT</th>
                                    <th rowspan="2" class="align-middle">Mã thành viên</th>
                                    <th rowspan="2" class="align-middle">Tài khoản</th>
                                    <th rowspan="2" class="align-middle">Họ tên</th>
                                    <th rowspan="2" class="align-middle">Số lần làm bài</th>
                                    <th rowspan="2" class="align-middle">Tỉ lệ làm đúng</th>
                                    <th colspan="3" class="text-center">Bài làm nhiều nhất</th>
                                </tr>
                                <tr>
                                    <th>Tên bài</th>
                                    <th>Số lần</th>
                                    <th>Tỉ lệ (%)</th>               
                                </tr>
                            </thead><tbody></tbody>`);

                        let idx = (page - 1) * pageSize;
                        result.data.forEach(l => {
                            let row = `
                                        <tr>
                                            <td>${++idx < 10 ? '0' + idx : idx}</td>
                                            <td class = "fw-bold text-warning">${l.user_code}</td>
                                            <td class = "text-info">${l.username}</td>
                                            <td class = "fw-bold">${l.fullname}</td>
                                            <td class = "text-center fw-bold text-danger">${l.total_attempts}</td>
                                            <td class = "text-center">${l.avg_correct_percentage} %</td>
                                            <td class = "text-warning fw-bold">${l.most_attempted_lession_name}</td>
                                            <td class = "text-center">${l.most_attempted_lession_attempts}</td>
                                            <td class = "text-end">${l.most_attempted_lession_correct_percentage} %</td>                                                              
                                        </tr>
                                    `;
                            $table.find('tbody').append(row);
                        })


                        $paginationFTB.append(
                            `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#">Previous</a></li>`
                        );
                        for (i = 1; i <= result.totalPage; i++) {
                            $paginationFTB.append(
                                ` <li class="page-item ${i == page ? 'active' : ''}"><a class="page-link " href="#">${i}</a></li>`
                            )
                        }
                        $paginationFTB.append(
                            `<li class="page-item  ${page === result.totalPage ? 'disabled' : ''}"><a class="page-link" href="#">Next</a></li>`
                        );


                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            console.log("Lession ID is not defined.");
        }
    }
    function StatisticByLession(lessionId) {
        $table.empty();
        $table.append(`
            <thead>
                <tr>
                    <th rowspan="2" class="align-middle">STT</th>
                    <th rowspan="2" class="align-middle">Mã thành viên</th>
                    <th rowspan="2" class="align-middle">Tài khoản</th>
                    <th rowspan="2" class="align-middle">Họ tên</th>
                    <th rowspan="2" class="align-middle">Số lần làm bài</th>
                    <th colspan="3" class="text-center">Chỗ trống</th>
                    <th colspan="3" class="text-center">Câu hỏi</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <th>Tổng số</th>
                    <th>Điền đúng</th>
                    <th>Tỉ lệ (%)</th>
                    <th>Tổng số</th>
                    <th>Trả lời đúng</th>
                    <th>Tỉ lệ (%)</th>
                </tr>
            </thead><tbody></tbody>
        `);

        $.ajax({
            url: `<?php echo BASE_URL; ?>/teacher/statistic/StatisticByLession`,
            method: 'get',
            dataType: 'json',
            data: {
                classId: parseInt($slOwnClasses.val()),
                lessionId: lessionId,
                keyword: $txtKeyword.val().trim(),
                page,
                pageSize
            },
            success: function (response) {
                const {
                    code,
                    msg,
                    result
                } = response;

                if (code == 200) {
                    let idx = (page - 1) * pageSize;

                    result.data.forEach(l => {
                        let row = `
                                    <tr>
                                        <td>${++idx < 10 ? '0' + idx : idx}</td>
                                        <td class="fw-bold text-warning">${l.user_code}</td>
                                        <td class="text-info">${l.username}</td>
                                        <td>${l.fullname}</td>
                                        <td>${l.attempts_count}</td>
                                        <td>${l.total_blanks}</td>
                                        <td>${l.correct_blanks}</td>
                                        <td>${l.correct_blanks_percent}</td>
                                        <td>${l.total_questions}</td>
                                        <td>${l.max_correct_questions}</td>
                                        <td>${l.highest_score_percentage}</td>
                                        <td>
                                            <a href="#" onclick="fetchDetail(${l.result_id}); return false;">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                `;
                        // Thêm dòng vào tbody của bảng
                        $table.find('tbody').append(row);
                    });

                    $paginationFTB.append(
                        `<li class="page-item ${page === 1 ? 'disabled' : ''}"><a class="page-link" href="#">Previous</a></li>`
                    );
                    for (i = 1; i <= result.totalPages; i++) {
                        $paginationFTB.append(
                            ` <li class="page-item ${i == page ? 'active' : ''}"><a class="page-link " href="#">${i}</a></li>`
                        )
                    }
                    $paginationFTB.append(
                        `<li class="page-item  ${page === result.totalPages ? 'disabled' : ''}"><a class="page-link" href="#">Next</a></li>`
                    );
                }
            },
            error: function (err) {
                console.log(err);
            }
        });


    }

    function LoadExamsBaseonClassAndSubject(classId, subjectId) {
        $slExams.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/exam/ListByClassAndSubject',
            type: 'GET',
            dataType: 'json',
            data: { classId, subjectId },
            success: function (response) {
                const { code, msg, exams } = response;
                if (code === 200) {
                    exams.forEach(e => {
                        $slExams.append(`<option value = "${e.exam_id}">${e.exam_title} - Diễn ra từ: ${e.begin_date} tới ${e.end_date}</option>`);
                    })
                    $slExams.trigger('change');
                }
            },
            error: function (err) {
                console.log(err.responseText);

            }
        })
    }

    // Tải danh sách bài học dựa trên môn học đã chọn
    function Loadlessions(subject_id) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/teacher/lession/getBySubject`,
            type: 'GET',
            data: {
                subject_id
            },
            dataType: 'json',
            success: function (response) {
                $slLessions.empty().append('<option value="">Tất cả bài học</option>');
                $.each(response.lessions, function (index, lession) {
                    $slLessions.append(`<option value="${lession.id}">${lession.name}</option>`);
                });
                $slLessions.trigger('change');
            },
            error: function (err) {
                console.log(err);
            }
        });
    }


    function LoadSubjectsByClass(roomId) {
        $slSubjects.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/teacher/subject/listbyroom',
            type: 'get',
            dataType: 'json',
            data: { roomId },
            success: function (response) {
                const { code, msg, subjects } = response;

                if (code == 200) {
                    subjects.forEach(s => {
                        $slSubjects.append(`<option value = "${s.id}">${s.name}</option>`)
                    })
                    $slSubjects.trigger('change');
                }

            },
            error: function (err) {
                console.log(err.responseText);

            }
        })
    }
    $slOwnClasses.on('change', function () {
        LoadSubjectsByClass($(this).val());
    })
    // Xử lý khi chọn môn học
    $slSubjects.change(function () {
        if ($slOwnClasses.val() && $slSubjects.val()) {
            Loadlessions(parseInt($slSubjects.val()));
            LoadExamsBaseonClassAndSubject(parseInt($slOwnClasses.val()), parseInt($slSubjects.val()));
        }
    });

    $slLessions.on('change', function () {
        $table.empty();
        $paginationFTB.empty();
        if ($(this).val()) {
            StatisticByLession(parseInt($(this).val()))
        } else {
            LoadSubjectStatistic(parseInt($slSubjects.val()));
        }
    })
    $slExams.on('change', function () {
        if ($(this).val()) {
            StatisticByExam(parseInt($(this).val()));
        }
    })

    const convertSecondsToHMS = function (seconds) {
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