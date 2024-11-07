<script src="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.css">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <select name="" id="slSubjects" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <select name="" id="slLessions" class="form-control"></select>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Tìm kiếm...">
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
    </div>

    <!-- statistic Table -->
    <table class="table table-bordered table-striped table-hover mt-3">
        <thead>
            <tr>
                <th rowspan="2" class="align-middle">STT</th>
                <th rowspan="2" class="align-middle">Mã thành viên</th>
                <th rowspan="2" class="align-middle">Tài khoản</th>
                <th rowspan="2" class="align-middle">Họ tên</th>
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
        </thead>
        <tbody id="tblData"></tbody>
    </table>
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
    const $slSubjects = $('#slSubjects'),
        $slLessions = $('#slLessions'),
        $keyword = $('#txtKeyword'),
        $search = $('#btnSearch'),
        $modal = $('#modal'),
        $content = $('#modalContent'),
        $table = $('#tblData'),
        pageSize = 10;

    let page = 1;
    $(document).ready(function() {
        LoadSubjects();
    })
    $slLessions.change(function() {
        $table.empty();
        if ($(this).val()) {
            LoadStatistic($(this).val());
        }

    })

    function LoadStatistic(lessionId) {
        $table.empty();
        if (lessionId !== undefined) { // kiểm tra biến lessionId đã có giá trị chưa
            $.ajax({
                url: `<?php echo BASE_URL; ?>/admin/statistic/get_review_statistic`,
                method: 'get',
                dataType: 'json',
                data: {
                    lession: lessionId, // truyền đúng biến
                    keyword: $keyword.val().trim(),
                    page,
                    pageSize
                },
                success: function(response) {
                    const {
                        code,
                        msg,
                        result
                    } = response;
                    if (code == 200) {
                        console.log(result);

                        let idx = (page - 1) * pageSize;
                        result.forEach(l => {
                            $table.append(`
                            <tr>
                                <td>${++idx}</td>
                                <td class = "fw-bold text-warning">${l.user_code}</td>
                                <td class = "text-info">${l.username}</td>
                                <td>${l.fullname}</td>
                                <td>${l.total_blanks}</td>
                                <td>${l.correct_blanks}</td>
                                <td>${l.correct_blanks_percent}</td>
                                <td>${l.total_questions}</td>
                                <td>${l.max_correct_questions}</td>
                                <td>${l.highest_score_percentage}</td>
                                 <td>
                                    <a href="#" onclick="fetchDetail(${l.result_id}); return false;"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        `);
                        })

                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
        } else {
            console.log("Lession ID is not defined.");
        }
    }


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
                    $slSubjects.append(`<option value="${s.id}">${s.name}</option>`);
                });
                $slSubjects.trigger('change');
            },
            error: function(err) {
                console.log(err.responseText);
            }
        });
    }

    // Xử lý khi chọn môn học
    $slSubjects.change(function() {
        Loadlessions(parseInt($slSubjects.val()));
    });

    // Tải danh sách bài học dựa trên môn học đã chọn
    function Loadlessions(subject_id) {
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/lession/getBySubject`,
            type: 'GET',
            data: {
                subject_id
            },
            dataType: 'json',
            success: function(response) {
                $slLessions.empty().append('<option value="">Chọn bài học</option>');
                $.each(response.lessions, function(index, lession) {
                    $slLessions.append(`<option value="${lession.id}">${lession.name}</option>`);
                });
                $slLessions.trigger('change');
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function fetchDetail(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/history/detail',
            type: 'get',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {
                console.log(response);
                const {
                    code,
                    msg,
                    data
                } = response;
                renderExamDetails(data);
            },
            error: function(err) {
                console.log(err);
            }
        })
    }

    function renderExamDetails(data) {
        let idx = 1;
        $content.empty();
        data.forEach(function(item) {
            var questionText = item.question_text;
            var answers = item.answers;

            // Sort answers by position to ensure correct replacement order
            answers.sort(function(a, b) {
                return a.position - b.position;
            });

            answers.forEach(function(answer) {
                var userAnswer = answer.user_answer;
                var correctAnswer = answer.correct_answer;

                // Tạo input cho user answer với class và style thích hợp
                var inputClass = userAnswer === correctAnswer ? 'text-success' : 'text-danger';
                var inputStyle =
                    'border: none; border-bottom: 1px solid #000;'; // Không có border nhưng có gạch chân
                var replacement = '';
                if (userAnswer.trim().length == 0) {
                    console.log(1);

                    replacement =
                        `<label><span class = "blank fw-bold bg-danger text-secondary" style="${inputStyle}">&nbsp;&nbsp;&nbsp;</span></label>`;
                } else {
                    replacement =
                        `<input type="text" class="blank ${inputClass} fw-bold" value="${userAnswer}" style="${inputStyle}" readonly />`;
                }


                console.log({
                    userAnswer,
                    correctAnswer
                });

                if (userAnswer !== correctAnswer) {
                    replacement +=
                        ` [<span class="text-success fw-bold">${formatDisplay(correctAnswer.replace(/</g, '&lt;').replace(/>/g, '&gt;'))}</span>]`;
                    console.log(correctAnswer);
                }

                // Thay thế chuỗi ___ bằng giá trị của replacement
                var regex = /___/;
                questionText = questionText.replace(regex, replacement);
            });

            // Append the formatted question to the container
            var questionHtml = `<div class="row mb-1">
                                <div class="col-md-12 form-group">
                                    <label class="fw-bold">Câu thứ ${idx++}:</label>
                                    <div class="mt-2 p-3">${formatDisplay(questionText)}</div>
                                </div>
                            </div>`;
            $content.append(questionHtml);
        });
        $modal.modal('show');
    }

    const formatDisplay = function(inputHTML) {

        return inputHTML
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
            .replace(/<p>\s+/g, function(match) {
                return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
            });
    }
</script>