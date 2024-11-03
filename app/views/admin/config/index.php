<div class="container">
    <div class="row justify-content-end mb-4">

        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search config..." />
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
                <th scope="col">Tiêu đề</th>
                <th scope="col">Số câu hỏi</th>
                <th scope="col" class="text-center">Điểm</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Cấu hình bài thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Tên cấu hình</label>
                                <input type="text" placeholder="Tên cấu hình" id="txtName" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Môn học</label>
                                <select name="" id="slSubjects" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="slQuestionsWithMark" class="mr-2">Câu hỏi và điểm</label>
                                <div class="d-flex align-items-center">
                                    <select name="" id="slQuestionsWithMark" class="form-control mr-2"
                                        style="flex: 1;"></select>
                                    <input type="number" min="1" id="txtQuantity" class="form-control"
                                        style="width: 100px; margin-left: 5px;">
                                    <button type="button" id="btnAddOption" class="btn btn-primary ml-2"
                                        style="margin-left: 5px;">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!--  Table -->
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Điểm</th>
                                <th scope="col">Số câu hỏi</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="tblOptions"></tbody>
                    </table>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Tổng số câu hỏi:</label>
                                <label id="lblTotalQuestions" class="text-danger fw-bold"></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Điểm:</label>
                                <label for="" id="lblTotalMarks" class="text-danger fw-bold"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSubmit">Lưu cấu hình</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const $txtName = $('#txtName');
    const $slSubjects = $('#slSubjects');
    const $slQuestionsWithMark = $('#slQuestionsWithMark');
    const $txtQuantity = $('#txtQuantity');
    const $btnAddOption = $('#btnAddOption');
    const $tblOptions = $('#tblOptions'), $table = $('#tblData');
    const $btnSubmit = $('#btnSubmit');
    const $modal = $('#modal');

    let id = 0;

    $(document).ready(function () {
        LoadSubjectsWithQuiz();
        LoadData();
    })


    const ViewConfig = function (cfId) {
        detail(cfId);
        $btnSubmit.hide();
        setTimeout(function () {
            $('a.btnRemoveOption').hide();
        }, 100);
    }
    const UpdateConfig = function (cfId) {
        detail(cfId);
        id = cfId;
        $btnSubmit.show();
        setTimeout(function () {
            $('a.btnRemoveOption').show();
        }, 100);
    }

    const DeleteConfig = function (id) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá cấu hình này?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/admin/config/delete',
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
                        console.log(err.responseText);
                    }
                });
            }
        });
    }

    const detail = function (id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/config/detail',
            type: 'get',
            dataType: 'json',
            data: { id },
            success: function (response) {
                const { code, config } = response;
                console.log(config);

                if (code === 200) {
                    quizArray = config.levels;
                    ShowOptions();
                    $modal.modal('show');
                }
                $slSubjects.val(config.subject_id);
                $txtName.val(config.title);
                $slSubjects.prop('disabled', true);


            },
            error: function (err) {
                console.log(err);

            }

        })
    }

    const LoadData = function () {
        $table.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/config/list',
            type: 'get',
            dataType: 'json',
            data: { keyword: $('#txtKeyword').val().trim() },
            success: function (response) {
                const { configs } = response;
                let idx = 1;
                configs.forEach(c => {
                    $table.append(`
                        <tr>
                            <td>${idx++}</td>
                            <td class = "fw-bold text-info">${c.title}</td>
                            <td>${c.number_of_questions}</td>
                            <td class = "fw-bold text-danger text-center">${c.marks}</td>
                            <td class = "text-center">
                                <a href="javascript:void(0)" onClick = "ViewConfig(${c.id})"><i class="fa fa-info-circle text-info" aria-hidden="true"></i></a>
                                <a href="javascript:void(0)" onClick = "UpdateConfig(${c.id})"><i class="fa fa-edit text-warning ml-2"></i></a>
                                <a href="javascript:void(0)" onClick = "DeleteConfig(${c.id})"><i class="fa fa-trash-o text-danger ml-2"></i></a>
                            </td>
                        </tr>
                    `)
                })
            },
            error: function (err) {
                console.log(err);

            }
        })
    }

    $btnSubmit.click(function () {
        const name = $txtName.val().trim();
        if (name.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng đặt tên cho cấu hình!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }
        if (quizArray.length === 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Vui lòng cấu hình số câu hỏi cho bài thi!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }
        let data = {
            title: name,
            subject_id: $slSubjects.val(),
            levels: JSON.stringify(quizArray) // Chuyển đổi mảng thành chuỗi JSON
        };
        if (id > 0) data.id = id;

        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/config/${id > 0 ? 'update' : 'create'}`,
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                const { code, msg } = response;
                showToast(code, msg);
                $modal.modal('hide');
                LoadData();
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

    let quizArray = [];

    $btnAddOption.click(function () {
        const max = parseInt($txtQuantity.attr('max'));
        const quantity = parseInt($txtQuantity.val());
        const selectedOption = $slQuestionsWithMark.find('option:selected');
        const mark = parseFloat(selectedOption.val());


        // Kiểm tra số lượng hợp lệ
        if (quantity <= 0 || quantity > max) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Số lượng câu hỏi không thể nhỏ hơn 1 hoặc lớn hơn số câu hỏi hiện có!',
                icon: 'error',
                loader: true,
                loaderBg: '#9EC600'
            });
            return;
        }

        // Kiểm tra nếu select chưa bị vô hiệu hóa
        if (!$slSubjects.prop('disabled')) {
            // Nếu chưa bị vô hiệu hóa thì vô hiệu hóa nó
            $slSubjects.prop('disabled', true);
        }

        // Kiểm tra xem đối tượng {count, mark} đã tồn tại trong mảng hay chưa
        const exists = quizArray.some(quiz => quiz.mark === mark);

        // Nếu chưa tồn tại thì thêm vào mảng
        if (!exists) {
            quizArray.push({ quantity, mark });
        }

        // Sắp xếp mảng theo mark tăng dần
        quizArray.sort((a, b) => a.mark - b.mark);

        ShowOptions();
    });

    const RemoveOption = function (mark, quantity) {
        // Tìm vị trí của phần tử cần xóa
        const index = quizArray.findIndex(quiz => quiz.mark === mark && quiz.quantity === quantity);

        // Nếu tìm thấy phần tử
        if (index !== -1) {
            // Loại bỏ phần tử khỏi mảng
            quizArray.splice(index, 1);
        }

        // Cập nhật lại hiển thị
        ShowOptions();
    };

    const ShowOptions = function () {
        $tblOptions.empty();
        let idx = 1;

        let totalQuantity = 0;
        let totalMarks = 0;

        quizArray.forEach(q => {
            totalQuantity += q.quantity;
            totalMarks += q.quantity * q.mark;

            $tblOptions.append(`
            <tr>
                    <td>${idx++}</td>
                    <td class = "fw-bold text-danger">${q.mark}</td>
                    <td class = "text-info">${q.quantity}</td>
                    <td class = "text-center"><a class = "btnRemoveOption" href="javascript:void(0)" onClick="RemoveOption(${q.mark},${q.quantity})"><i class="fa fa-times text-danger" aria-hidden="true"></i></a></td>
                </tr>
            `)

        })
        $('#lblTotalMarks').text(totalMarks);
        $('#lblTotalQuestions').text(totalQuantity);
    }


    $('#btnSearch').click(function () {
        LoadData();
    })
    $slQuestionsWithMark.on('change', function () {
        const selectedOption = $(this).find('option:selected');
        const mark = selectedOption.val();
        const count = selectedOption.data("count");
        $txtQuantity.val(count);
        $txtQuantity.attr('max', count);
    })

    $slSubjects.on('change', function () {
        $slQuestionsWithMark.empty();
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/quiz/listMarkAndQuestions',
            type: 'get',
            dataType: 'json',
            data: { subject_id: $(this).val() },
            success: function (response) {
                const { data } = response;
                data.forEach(el => {
                    $slQuestionsWithMark.append(`<option value = "${el.mark}" data-count = "${el.question_count}" > ${el.question_count} câu ${el.mark} điểm</option > `);
                })
                $slQuestionsWithMark.trigger('change');
            },
            error: function (err) {
                console.log(err);
            }
        })
    })
    const LoadSubjectsWithQuiz = function () {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/getSubjectsWithQuizzes',
            type: 'get',
            dataType: 'json',
            success: function (response) {
                const { subjects } = response;

                subjects.forEach(s => {
                    console.log(s);
                    $slSubjects.append(`<option value = "${s.subject_id}" > ${s.subject_name}</option>`);
                })
                $slSubjects.trigger('change');
            },
            error: function (err) {
                console.log(err);
            }
        })
    }
    $modal.on('hide.bs.modal', function (e) {
        id = 0;
        $btnSubmit.show();
        $txtName.val('');
        quizArray = [];
        $slSubjects.prop('disabled', false);
        $tblOptions.empty();
        $('#lblTotalMarks').text('');
        $('#lblTotalQuestions').text('');
    });
    const showToast = (code, msg) => {
        const isSuccess = code === 200 || code === 201;
        const heading = isSuccess ? 'SUCCESSFULLY' : 'ERROR';
        const icon = isSuccess ? 'success' : 'error';

        $.toast({
            heading: heading,
            text: msg,
            icon: icon,
            loader: true,
            loaderBg: '#9EC600'
        });
    };
</script>