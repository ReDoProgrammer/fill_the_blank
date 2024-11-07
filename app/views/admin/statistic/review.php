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
                <th scope="col">#</th>
                <th scope="col">Môn học</th>
                <th scope="col">Bài học</th>
                <th scope="col">Số câu hỏi</th>
                <th scope="col">Số user làm bài</th>
                <th scope="col">Số lần thi</th>
            </tr>
        </thead>
        <tbody id="tblData"></tbody>
    </table>
</div>


<script>
    const $slSubjects = $('#slSubjects'),
        $slLessions = $('#slLessions'),
        $keyword = $('#txtKeyword'),
        $search = $('#btnSearch'),
        $table = $('#tblData'),
        pageSize = 10;

    let page = 1;
    $(document).ready(function() {
        LoadSubjects();
    })
    $slLessions.change(function() {
        if ($(this).val()) {
            LoadStatistic($(this).val());
        }

    })

    function LoadStatistic(lessionId) {
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
                    console.log(response);
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
</script>