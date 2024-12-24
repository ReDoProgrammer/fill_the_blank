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
                <table class="table table-bordered table-striped table-hover mt-3" id="table">

                </table>
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
        $keyword = $('#txtKeyword'),
        $search = $('#btnSearch'),
        $modal = $('#modal'),
        $content = $('#modalContent'),
        $table = $('#table'),
        $export = $('#btnExport'),
        pageSize = 10;

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
        console.log({ examId });

    }

    function StatisticByLession(lessionId) {
        console.log({ lessionId });

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
                console.log(exams);

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
                $slLessions.empty().append('<option value="">Chọn bài học</option>');
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

    function LoadSubjectStatistic(subjectId) {
        $table.empty();
        if (subjectId !== undefined) { // kiểm tra biến lessionId đã có giá trị chưa
            $.ajax({
                url: `<?php echo BASE_URL; ?>/teacher/statistic/StatisticByClassAndSubject`,
                method: 'get',
                dataType: 'json',
                data: {
                    classId: parseInt($slOwnClasses.val()),
                    subjectId: parseInt($slSubjects.val()),
                    keyword: $keyword.val().trim(),
                    page,
                    pageSize
                },
                success: function (response) {
                    // const {
                    //     code,
                    //     msg,
                    //     result
                    // } = response;
                    console.log(response);

                    // if (code == 200) {
                    //     console.log(result);

                    //     let idx = (page - 1) * pageSize;
                    //     result.forEach(l => {
                    //         let row = `
                    //                     <tr>
                    //                         <td>${++idx < 10 ? '0' + idx : idx}</td>
                    //                         <td class = "fw-bold text-warning">${l.user_code}</td>
                    //                         <td class = "text-info">${l.username}</td>
                    //                         <td class = "fw-bold">${l.fullname}</td>
                    //                         <td class = "text-center fw-bold text-danger">${l.total_attempts}</td>
                    //                         <td class = "text-center">${l.avg_correct_percentage} %</td>
                    //                         <td class = "text-warning fw-bold">${l.most_attempted_lession_name}</td>
                    //                         <td class = "text-center">${l.most_attempted_lession_attempts}</td>
                    //                         <td class = "text-end">${l.most_attempted_lession_correct_percentage} %</td>                                                              
                    //                     </tr>
                    //                 `;
                    //         $table.find('tbody').append(row);
                    //     })

                    // }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        } else {
            console.log("Lession ID is not defined.");
        }
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
            LoadSubjectStatistic(parseInt($slSubjects.val()));
            LoadExamsBaseonClassAndSubject(parseInt($slOwnClasses.val()), parseInt($slSubjects.val()));
        }

    });

    $slLessions.on('change', function () {
        if ($(this).val()) {
            StatisticByLession(parseInt($(this).val()));
        } else {

        }
    })
    $slExams.on('change', function () {
        if ($(this).val()) {
            StatisticByExam(parseInt($(this).val()));
        }
    })


</script>