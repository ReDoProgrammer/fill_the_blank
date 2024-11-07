<div class="card text-white bg-success mb-3">
    <h5 class="card-header mb-3 d-flex justify-content-between align-items-center">
        <div id="timer" class="text-white">
            <!-- Thay thế nội dung của timer theo nhu cầu -->
            <i class="fa fa-clock-o text-warning" aria-hidden="true"></i>
            <span id="remaining">00:00:00</span>
        </div>

        <span>
            <i class="fa fa-book text-warning"></i>
            <span id="examTitle"></span>
        </span>
    </h5>


    <div class="card-body bg-white text-dark" style="padding:0 !important;">
        <div class="text-center mt-3">
            <button class="btn btn-warning border-danger pl-2" id="btnStart">Bắt đầu</button>
        </div>
        <div class="container mb-5 mt-3" id="questions"></div>
    </div>

</div>


<script>
    const $questions = $('#questions');
    const $start = $('#btnStart');
    const $timer = $('#timer');
    const $remaining = $('#remaining');
    const $fixedCountdown = $('#fixedCountdown');
    const $fixedRemaining = $('#fixedRemaining');
    var examObj, examId;
    var countdownInterval;
    var noq;
    var answers = [];
    var timeount = false;
    var spent_time = 0;
    $(document).ready(function() {
        $questions.empty();
        $timer.hide();
        examId = getUrlParameter('id');
        if (examId && isNumeric(examId)) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/exam/check_available',
                type: 'get',
                dataType: 'json',
                data: {
                    exam_id: examId
                },
                success: function(response) {
                    const {
                        code,
                        msg,
                        header
                    } = response;
                    if (code != 200) {
                        window.location.replace(`<?php echo BASE_URL; ?>`);
                    } else {
                        LoadQuestions(examId);
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            })
        }
    });

    $start.click(function() {
        spent_time = 0;
        const {
            questions,
            duration
        } = examObj;

        let idx = 1;
        questions.forEach(q => {
            let options = JSON.parse(q.options);
            $questions.append(`
            <div class="question-container" id="${q.id}">
                <h6 class="mb-2">Câu hỏi <span class="question_index">${idx++}</span>: <span>${formatDisplay(q.question)}</span> - (<span class = "fw-bold text-danger"> ${q.mark}</span> điểm )</h6>
                <div class="options">
                    <div class="form-check">
                        <input class="form-check-input option" type="radio" name="question_${q.id}_Options" id="${q.id}_option1" value="1">
                        <label class="form-check-label" for="${q.id}_option1">
                            ${formatDisplay(options.option_1)}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input option" type="radio" name="question_${q.id}_Options" id="${q.id}_option2" value="2">
                        <label class="form-check-label" for="${q.id}_option2">
                             ${formatDisplay(options.option_2)}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input option" type="radio" name="question_${q.id}_Options" id="${q.id}_option3" value="3">
                        <label class="form-check-label" for="${q.id}_option3">
                             ${formatDisplay(options.option_3)}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input option" type="radio" name="question_${q.id}_Options" id="${q.id}_option4" value="4">
                        <label class="form-check-label" for="${q.id}_option4">
                             ${formatDisplay(options.option_4)}
                        </label>
                    </div>
                </div>
            </div>
        `);
            // Gán sự kiện sau khi phần tử được thêm vào DOM
            $('.option').change(function() {

                var questionContainerId = parseInt($(this).closest('.question-container').attr(
                    'id'));
                var choice = parseInt($(this).val());

                // Kiểm tra xem ID câu hỏi đã tồn tại trong mảng answers chưa
                var answerIndex = answers.findIndex(function(answer) {
                    return answer.id === questionContainerId;
                });


                if (answerIndex !== -1) {
                    // Nếu ID đã tồn tại, cập nhật choice và đánh dấu là đã cập nhật
                    answers[answerIndex].choice = choice;


                } else {
                    // Nếu ID chưa tồn tại, thêm mới vào mảng answers và đánh dấu là đã cập nhật
                    answers.push({
                        id: questionContainerId,
                        choice: choice
                    });
                }
                // Kiểm tra nếu thẻ h5 có class 'text-danger' và xóa class này
                $(this).closest('.question-container').find('h6').removeClass('text-danger');
            });
        });
        $questions.append(`
                            <div class="text-center mt-3">
                            <button class="btn btn-success pl-2" id="btnFinish">
                                <i class="fa fa-save"></i>
                                Nộp bài
                            </button>
                        </div>
                        `);

        // Hàm xử lý khi nút "Nộp bài" được nhấn
        $('#btnFinish').click(function() {

            //nếu chưa hết giờ và chưa trả lời hết các câu hỏi
            if (!timeount && answers.length < noq) {
                Swal.fire({
                    icon: "error",
                    title: "Nộp bài thi thất bại!!!",
                    html: "<strong>Bạn cần trả lời hết các câu hỏi.</strong><br/><b><u>Lưu ý:</u> Câu hỏi chưa trả lời sẽ có <span class = 'text-danger'>màu đỏ</span></b>"
                });


                //lấy danh sách các câu hỏi chưa tick câu trả lời
                const missingQuestions = examObj.questions.filter(q =>
                    !answers.some(a => a.id === parseInt(q.id))
                );

                // lấy id của các câu hỏi chưa được tick đáp án ( mảng số nguyên, sử dụng hàm parseInt để convert từ chuỗi sang số nguyên)
                const missingIds = missingQuestions.map(q => parseInt(q.id));




                if (missingIds.length > 0) {

                    // Duyệt qua từng phần tử có class "question-container"
                    $('.question-container').each(function() {
                        // Lấy id của phần tử hiện tại

                        const questionId = parseInt($(this).attr('id'));


                        // Kiểm tra xem id này có nằm trong mảng missingIds không
                        if (missingIds.includes(questionId)) {
                            // Nếu có, thêm class 'danger' cho thẻ h5 trong phần tử hiện tại
                            $(this).find('h6').addClass('text-danger');
                        }
                    });
                }


                // dừng chương trình ngang đây để người dùng tiếp tục trả lời các câu hỏi chưa tick hoặc thay đổi đáp án
                return;
            }

            /**
             * Trường hợp hết giờ nhưng người dùng chưa trả lời hết các câu hỏi
             * Thì sẽ tự động chọn đáp án cho câu hỏi ( mặc định là -1 <=> luôn luôn sai)
             */
            if (timeount && answers.length < noq) {
                questions.forEach(function(question) {
                    // Kiểm tra xem id của câu hỏi có tồn tại trong answers hay chưa
                    var isAnswered = answers.some(function(answer) {
                        return answer.id === question.id;
                    });

                    // Nếu không tồn tại, thêm phần tử mới vào answers với choice = -1
                    if (!isAnswered) {
                        answers.push({
                            id: question.id,
                            choice: -1
                        });
                    }
                });
            }


            $.ajax({
                url: '<?php echo BASE_URL; ?>/exam/save',
                type: 'post',
                dataType: 'json',
                data: {
                    exam_id: examId,
                    result: answers,
                    spent_time
                },
                success: function(response) {
                    console.log(response);

                    // Dừng countdownInterval
                    clearInterval(countdownInterval);
                    $remaining.text('00:00:00');
                    $timer.hide();

                    let correctNumbers = 0;
                    let marks = 0,
                        totalMarks = 0;

                    // Đánh dấu đáp án đúng và sai
                    examObj.questions.forEach(question => {
                        let options = JSON.parse(question.options);
                        let userAnswer = answers.find(answer => answer.id === parseInt(
                            question
                            .id));
                        let correctOption = options.correct_option;
                        totalMarks += parseFloat(question.mark);
                        console.log(question.mark, userAnswer, correctOption);


                        if (userAnswer && correctOption === userAnswer.choice) {
                            correctNumbers++;
                            marks += parseFloat(question.mark);
                        }


                        // Set màu cho đáp án đúng
                        $(`#${question.id}_option${correctOption}`).parent().css({
                            'background-color': '#50c7c7',
                            'color': 'white',
                            'font-weight': 'bold'
                        });
                    });

                    Swal.fire({
                        title: timeount ? `Thời gian làm bài đã hết.${response.msg}` : response.msg,
                        text: timeount ? `Thời gian làm bài đã hết.${response.msg}` : response.msg,
                        icon: "success",
                        html: `
                        <table class="table table-bordered">
                            <tbody>
                                <tr class = "fw-bold">
                                    <td>Số câu trả lời đúng:</td>
                                    <td><span class = "text-success">${correctNumbers}</span>/${examObj.questions.length}</td>
                                </tr>
                                <tr class = "fw-bold">
                                    <td>Số điểm đạt được:</td>
                                    <td><span class = "text-success">${marks}</span>/${totalMarks}</td>
                                </tr>
                            </tbody>
                        </table>
                    `,
                        confirmButtonColor: "#3085d6"
                    });
                },
                error: function(err) {

                    console.log(err);

                }
            });

            $('#btnFinish').hide();
        });

        $start.hide();
        $timer.show();

        // Bắt đầu countdown với thời gian từ examObj
        startCountdown(examObj.duration);
    });

    function LoadQuestions(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/exam/detail',
            type: 'get',
            dataType: 'json',
            data: {
                id
            },
            success: function(response) {
                const {
                    code,
                    exam
                } = response;
                examObj = exam;
                noq = exam.number_of_questions;
                $('#examTitle').text(exam.title);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function startCountdown(durationMinutes) {
        let totalSeconds = durationMinutes * 60;

        // Clear any existing intervals
        clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            spent_time++;
            // Cập nhật phần remaining
            $remaining.text(
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
            );
            $fixedRemaining.text(
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
            );

            // Kiểm tra nếu countdown đã hết
            if (totalSeconds <= 0) {
                clearInterval(countdownInterval);
                // Thực hiện hành động khi countdown đếm hết
                countdownEnded();
            } else {
                totalSeconds--;
            }
        }, 1000); // Cập nhật mỗi giây
    }

    function countdownEnded() {
        timeount = true;
        $('#btnFinish').click();
    }

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };

    function isNumeric(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    }

    const formatDisplay = function(inputHTML) {
        var formattedQuestionText = $('<div>').text(inputHTML).html();
        return formattedQuestionText
            .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // Thay thế tab bằng không gian trắng
            .replace(/<p>\s+/g, function(match) {
                return '<p>' + '&nbsp;'.repeat(match.length - 3); // Thay thế khoảng trắng đầu dòng sau <p>
            });
    }
</script>

<style>
    .question-container {
        border: 1px solid #ddd;
        padding: 5px;
        margin-bottom: 5px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .question-title {
        margin-bottom: 10px;
        font-weight: bold;
    }

    .options .form-check {
        margin-bottom: 3px;
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-right: 10px;
        width: 1em;
        height: 1em;
        border: 2px solid #888;
        /* Viền màu xám cho radio button */
        border-radius: 50%;
        /* Đảm bảo radio button có dạng tròn */
        background-color: #fff;
        /* Màu nền của radio button */
        -webkit-appearance: none;
        /* Xóa kiểu dáng mặc định của radio button */
        appearance: none;
        /* Xóa kiểu dáng mặc định của radio button */
        position: relative;
        /* Để có thể thêm dấu tích khi chọn */
    }

    .form-check-input:checked {
        background-color: #4CAF50;
        /* Màu nền khi radio button được chọn */
        border-color: #4CAF50;
        /* Đảm bảo viền cùng màu xanh khi chọn */
    }

    .form-check-input:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0.5em;
        height: 0.5em;
        border-radius: 50%;
        background-color: #fff;
        /* Màu của dấu tích bên trong radio button */
        transform: translate(-50%, -50%);
    }

    .form-check-label {
        font-size: 1rem;
    }
</style>