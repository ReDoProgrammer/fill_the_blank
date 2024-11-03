<div class="card text-white bg-success mb-3">
    <h5 class="card-header mb-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fa fa-book text-warning" aria-hidden="true"></i>
            <?php echo $subject . " / <i class='fa fa-graduation-cap text-warning'></i> " . $lession; ?>
        </span>
        <span class="float-end">
            <i class="fa fa-question"></i>
            Câu thứ: <span id="orderQuestion">1</span>/<span id="totalQuestion"></span>

        </span>
    </h5>

    <div class="card-body bg-white text-dark">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/jquery-ui/jquery-ui.css">
        <script src="<?php echo BASE_URL; ?>/public/assets/plugins/jquery-ui/jquery-ui.min.js"></script>

        <div class="container mb-5 mt-3" id="question-container" data-subject="<?php echo $subjectId; ?>"
            data-lession="<?php echo $lessionId; ?>" data-id="">

        </div>


        <button class="btn btn-secondary border-info mb-3" id="check">
            <i class="fa fa-check" aria-hidden="true"></i> Trả lời
        </button>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true" id="btnPrev">Câu trước</a>
                </li>
                <li class="page-item <?php echo $question['hasNext'] ? '' : 'disabled'; ?>">
                    <a class="page-link" href="#"
                        data-id="<?php echo $question['hasNext'] ? $question['nextQuestionId'] ?? -1 : -1; ?>"
                        id="btnNext">Câu tiếp theo</a>
                </li>
            </ul>
        </nav>

        <div class="d-flex justify-content-between">
            <button class="btn btn-success" id="btnSaveAnswers">
                <i class="fa fa-save"></i> Lưu kết quả
            </button>

            <div>
                <button class="btn btn-secondary" id="btnPrevLesson" data-prev="">
                    <i class="fa fa-arrow-left"></i> Bài trước
                </button>

                <button class="btn btn-primary" id="btnNextLesson" data-next="">
                    <i class="fa fa-arrow-right"></i> Bài tiếp theo
                </button>
            </div>
        </div>




    </div>
</div>

<div class="modal" tabindex="-1" id="answerModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài thi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:550px; overflow-y:auto">
                <p class="lh-hight mb-3">Nếu câu trả lời của bạn đúng thì sẽ có màu <span
                        style="background-color:lightgreen" class="fw-bold p-2">XANH</span>. Ngược lại, sẽ có dạng <span
                        style="background-color:lightcoral" class="fw-bold p-2">CÂU TRẢ LỜI CỦA BẠN</span> [<span
                        style="background-color:lightgreen" class="fw-bold p-2">ĐÁP ÁN ĐÚNG</span>]</p>
                <div class="containter p-5" id="modalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    const $check = $('#check'),
        $next = $('#btnNext'),
        $prev = $('#btnPrev');
    const $save = $('#btnSaveAnswers');
    const $order = $('#orderQuestion');
    const $nextLession = $('#btnNextLesson'),
        $prevLession = $('#btnPrevLesson');
    var answers_tmp = [];
    var blanks_number = [];
    $(document).ready(function() {
        // Hàm tự động điều chỉnh kích thước input
        function autoResizeInput(input) {
            // Tạo một thẻ span ẩn để đo kích thước văn bản
            var tempSpan = $('<span>').hide().appendTo(document.body);

            // Sao chép kiểu chữ và kích thước chữ từ input
            tempSpan.css({
                'font-family': input.css('font-family'),
                'font-size': input.css('font-size'),
                'letter-spacing': input.css('letter-spacing'),
                'padding': input.css('padding')
            });

            // Gán nội dung của input vào tempSpan để đo chiều rộng
            tempSpan.text(input.val() || input.attr('placeholder') || '');

            // Đặt chiều rộng của input theo chiều rộng của tempSpan
            input.width(tempSpan.width() + 10); // Thêm một khoảng đệm 10px để thoải mái
            tempSpan.remove(); // Xóa tempSpan sau khi đo
        }

        // Áp dụng hàm autoResizeInput cho tất cả các input.blank
        $('.blank').each(function() {
            autoResizeInput($(this));
        });

        // Thực hiện lại khi nội dung input thay đổi
        $(document).on('input', '.blank', function() {
            autoResizeInput($(this));
        });

        getFirstQuestion();

    });


    $check.on("click", function() {
        var idQuestion = parseInt($("#question-container").attr('data-id'));
        var answers = {};

        let filled = 0;
        $(".blank").each(function() {
            var blankId = $(this).data("answer-id");
            var userAnswer = $(this).val().trim();
            if (userAnswer.length > 0) {
                filled++;
            }
            answers[blankId] = userAnswer;
        });
        // Tính toán số lượng chỗ trống
        var element = blanks_number.find(item => item.id === idQuestion);
        if (element) {
            element.filled = filled;
        }


        $.ajax({
            url: '<?php echo BASE_URL; ?>/question/answer',
            type: 'POST',
            dataType: 'json',
            data: {
                question_id: idQuestion,
                answers: answers
            },
            success: function(response) {
                const {
                    code,
                    msg,
                    detail
                } = response;
                if (code === 200) {
                    $(".blank").each(function() {
                        var blankId = parseInt($(this).data("answer-id"));
                        var userAnswer = $(this).val().trim();
                        var answerDetail = detail.find(item => item.id == blankId);
                        if (answerDetail) {
                            var isCorrect = answerDetail.is_correct;
                            $(this).css("background-color", isCorrect ? "lightgreen" : "lightcoral");
                            var existingEntry = answers_tmp.find(entry => entry.question_id === idQuestion && entry.question_blank_id === blankId);
                            if (existingEntry) {
                                existingEntry.answer = userAnswer;
                                existingEntry.is_correct = isCorrect;
                            } else {
                                answers_tmp.push({
                                    question_id: idQuestion,
                                    question_blank_id: blankId,
                                    answer: userAnswer,
                                    is_correct: isCorrect
                                });
                            }
                        } else {
                            console.log(`Không tìm thấy blankId: ${blankId} trong detail`);
                        }
                    });
                } else {
                    console.log({
                        response
                    });
                }
            },
            error: function(err) {
                console.log(`Lỗi 2: ${err}`);
            }
        });
    });

    $save.click(async function() {
        const totalQuestion = parseInt($('#totalQuestion').text());
        const totalBlanks = blanks_number.reduce((total, item) => total + item.blanks, 0);
        const totalFilled = blanks_number.reduce((total, item) => total + item.filled, 0);
        const countByQuestionId = new Set(answers_tmp.map(item => item.question_id));

        if (countByQuestionId.size < totalQuestion) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Bạn cần trả lời tất cả các câu hỏi!"
            });
            return;
        }

        const subject_id = $("#question-container").data("subject");
        const lession_id = $("#question-container").data("lession");
        const corrects = answers_tmp.filter(x => x.is_correct == true);
        const hasNextLession = $nextLession.attr('data-next') ? true : false;

        $.ajax({
            url: '<?php echo BASE_URL; ?>/question/saveResult',
            type: 'POST',
            dataType: 'json',
            data: {
                subject_id,
                lession_id,
                answers: answers_tmp
            },
            success: function(response) {
                const {
                    code,
                    msg,
                    id
                } = response;
                $save.hide();
                if (code == 201) {
                    Swal.fire({
                        title: "<strong>LƯU KẾT QUẢ THÀNH CÔNG!</strong>",
                        icon: "success",
                        html: `
                        <h6 class = "text-danger">Lưu ý: Phần không điền câu trả lời sẽ bị tính là trả lời sai!</h6>
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th scope="col">Loại</th>
                                    <th scope="col">SL</th>      
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold text-secondary">Số câu hỏi:</td>
                                    <td>${totalQuestion}</td>                                    
                                </tr>                                                                     
                                <tr>
                                    <td class="text-start fw-bold text-secondary">Số chỗ trống cần điền:</td>
                                    <td>${totalBlanks}</td>                                    
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold text-secondary">Số chỗ trống không điền:</td>
                                    <td>${totalBlanks-totalFilled}</td>                                    
                                </tr>
                                <tr>
                                    <td class="text-start">Trả lời đúng:</td>
                                    <td>${corrects.length}/${totalBlanks}</td>                                    
                                </tr>
                                <tr>
                                    <td class="text-start">Trả lời sai:</td>
                                    <td>${totalBlanks - corrects.length}/${totalBlanks}</td>                                    
                                </tr>
                            </tbody>
                        </table>`,
                        showCloseButton: true,
                        showConfirmButton: hasNextLession ? true : false,
                        showCancelButton: true,
                        showDenyButton: true,
                        focusConfirm: false,
                        confirmButtonText: `Bài tiếp theo`,
                        denyButtonText: `Xem đáp án`,
                        cancelButtonText: `<i class="fa fa-thumbs-up"></i> Great!`
                    }).then(result => {
                        if (result.isConfirmed) {
                            $nextLession.click();
                        }
                        if (result.isDenied) {
                            fetchDetail(id);
                        }
                    });
                } else {
                    Swal.fire({
                        title: "LƯU KẾT QUẢ THẤT BẠI",
                        text: msg,
                        icon: "error"
                    });
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    $next.click(async function(e) {
        e.preventDefault();
        let id = parseInt($(this).attr('data-id')); // Sử dụng .attr() để lấy giá trị thuộc tính
        if (id !== -1) {
            await getQuestion(id);
        }
    });

    $prev.click(function(e) {
        e.preventDefault();
        let id = parseInt($(this).attr('data-id'));
        if (id !== -1) {
            getQuestion(id);
        }
    });



    function getQuestion(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/question/getQuestion',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function(data) {
                    const {
                        code,
                        msg,
                        detail
                    } = data;
                    const {
                        blanks,
                        hasNext,
                        nextQuestionId,
                        hasPrev,
                        prevQuestionId,
                        question,
                        order,
                        totalQuestion
                    } = detail;

                    if (code === 200 && question) {
                        let questionText = question.question_text;
                        $('#totalQuestion').text(totalQuestion);
                        $('#question-container').attr('data-id', question.id);
                        $('#orderQuestion').text(order);

                        // Xử lý câu trả lời tạm thời nếu đã trả lời
                        let qs = answers_tmp.filter(x => x.question_id == parseInt(question.id));

                        // Tính toán số lượng chỗ trống
                        var element = blanks_number.find(item => item.id === id);
                        if (!element) {
                            blanks_number.push({
                                id: id,
                                blanks: blanks.length
                            });
                        } else {
                            element.blanks = blanks.length; // Cập nhật số lượng nếu đã tồn tại
                        }

                        // Tạo các input thay thế cho các chỗ trống
                        blanks.forEach((blank) => {
                            let aw = qs?.find(x => x.question_blank_id == blank.id);
                            let inputHTML = `<input type="text" 
                                        value="${aw?.answer || ''}" 
                                        ${aw ? `style="background-color: ${aw.is_correct ? 'lightgreen' : 'lightcoral'}"` : ''} 
                                        name="blank_${blank.id}" 
                                        id="blank_${blank.id}" 
                                        data-answer-id="${blank.id}" 
                                        class="blank" 
                                        placeholder="Câu trả lời ${blank.position}">
                                    `;
                            // Thay thế phần `___` đầu tiên tìm thấy trong câu hỏi
                            questionText = questionText.replace('___', inputHTML);
                        });

                        // Thay thế ký tự tab bằng khoảng trắng trong HTML
                        questionText = questionText.replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

                        // Thay thế các khoảng trắng đầu dòng sau thẻ <p> bằng &nbsp;
                        questionText = questionText.replace(/<p>\s+/g, function(match) {
                            return '<p>' + '&nbsp;'.repeat(match.length - 3); // 3 là độ dài của <p> và 1 khoảng trắng mặc định
                        });

                        // Chèn nội dung vào phần tử
                        $('#question-container').html(questionText);

                        if (!hasNext) {
                            $next.closest('li').addClass('disabled');
                        } else {
                            $next.closest('li').removeClass('disabled');
                            $next.attr('data-id', nextQuestionId);
                        }

                        if (!hasPrev) {
                            $prev.closest('li').addClass('disabled');
                        } else {
                            $prev.closest('li').removeClass('disabled');
                            $prev.attr('data-id', prevQuestionId);
                        }

                        return resolve();
                    } else {
                        return reject(msg);
                    }
                },
                error: function(err) {
                    return reject(`Lỗi lấy câu hỏi: ${err}`);
                }
            });
        });
    }



    $content = $('#modalContent');

    function fetchDetail(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/history/detail',
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
                var inputStyle = 'border: none; border-bottom: 1px solid #000;'; // Không có border nhưng có gạch chân
                var replacement = `<input type="text" class="blank ${inputClass} fw-bold" value="${userAnswer}" style="${inputStyle}" readonly />`;

                if (userAnswer !== correctAnswer) {
                    replacement += ` [<span class="text-success fw-bold">${formatDisplay(correctAnswer.replace(/</g, '&lt;').replace(/>/g, '&gt;'))}</span>]`;
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
                                        <div class="mt-2 p-3">${questionText}</div>
                                    </div>
                                </div>`;
            $content.append(questionHtml);
        });
        $('#answerModal').modal('show');
    }
    $nextLession.click(function() {
        const nextLessionId = $nextLession.attr('data-next');
        if (nextLessionId && isIntegerString(nextLessionId)) {
            redirectToOtherLession(nextLessionId);
        }

    })
    $prevLession.click(function() {
        const prevLessionId = $prevLession.attr('data-prev');
        if (prevLessionId && isIntegerString(prevLessionId)) {
            redirectToOtherLession(prevLessionId);
        }

    })
    const getFirstQuestion = function() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/question/getMinQuestion',
            type: 'GET',
            dataType: 'json',
            data: {
                'lession_id': parseInt($('#question-container').attr('data-lession'))
            },
            success: function(response) {
                if (response.code == 200) {
                    const {
                        minQuestionId,
                        prevLessionId,
                        nextLessionId
                    } = response;
                    getQuestion(minQuestionId);
                    if (nextLessionId) {
                        $nextLession.prop('disabled', false);
                        $nextLession.attr('data-next', nextLessionId)
                    } else {
                        $nextLession.prop('disabled', true);
                    }
                    if (prevLessionId) {
                        $prevLession.prop('disabled', false);
                        $prevLession.attr('data-prev', prevLessionId)
                    } else {
                        $prevLession.prop('disabled', true);
                    }
                }
            },
            error: function(err) {
                console.log(err);

            }
        })
    }
    const getNext = function(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/',
            type: 'get',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {

            },
            error: function(err) {
                console.log(err);

            }
        })
    }

    const redirectToOtherLession = function(nextLessionId) {
        nextLessionId = `id-${nextLessionId}`;
        // Lấy URL hiện tại
        let url = new URL(window.location.href);

        // Thay đổi giá trị của tham số 'l'
        url.searchParams.set('l', nextLessionId);

        // Cập nhật lại URL và tải lại trang
        window.location.href = url;
    }

    function isIntegerString(str) {
        // Kiểm tra nếu chuỗi là số nguyên
        const parsed = parseInt(str, 10);
        return !isNaN(parsed) && parsed.toString() === str;
    }
    const formatDisplay = function(inputHTML) {
        return inputHTML
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
            .replace(/<p>\s+/g, function(match) {
                return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
            });
    }
</script>

<style>
    .blank {
        border: none;
        /* Loại bỏ viền */
        border-bottom: 1px solid #ccc;
        /* Thêm một đường viền dưới để phân cách */
        outline: none;
        /* Loại bỏ outline khi trường được chọn */
        padding: 5px;
        /* Thêm khoảng cách bên trong trường */
        font-size: 16px;
        /* Kích thước chữ */
        background-color: #f9f9f9;
        /* Màu nền */
    }

    .blank:focus {
        border-bottom: 1px solid #007bff;
        /* Đổi màu viền dưới khi trường được chọn */
        background-color: #fff;
        /* Đổi màu nền khi trường được chọn */
    }
</style>