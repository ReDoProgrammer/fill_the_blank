<script src="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/plugins/datepicker/bootstrap-datepicker.min.css">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-2 text-end">
            <label for="from_date">Từ ngày:</label>
        </div>

        <div class="col-md-2">
            <div class="input-group date" id="from_date">
                <input type="text" class="form-control" />
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-2 text-end">
            <label for="from_date">Tới ngày:</label>
        </div>
        <div class="col-md-2">
            <div class="input-group date" id="to_date">
                <input type="text" class="form-control" />
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search question...">
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
    const $from_date = $('#from_date'), $to_date = $('#to_date');
    const $keyword = $('#txtKeyword'), $search = $('#btnSearch'),$table = $('#tblData');
  
    var from_date, to_date, keyword = '';
    $(document).ready(function () {
        // Lấy ngày đầu tiên của tháng hiện tại và định dạng theo dd/MM/yyyy
        var today = new Date();
        var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        var formattedDate = ('0' + firstDayOfMonth.getDate()).slice(-2) + '/' + ('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2) + '/' + firstDayOfMonth.getFullYear();
        $('#from_date input').val(formattedDate);

        formattedDate = ('0' + today.getDate()).slice(-2) + '/' + ('0' + (today.getMonth() + 1)).slice(-2) + '/' + today.getFullYear();
        $('#to_date input').val(formattedDate);
        $from_date.datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
        $to_date.datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
        from_date = $('#from_date input').val();
        to_date = $('#to_date input').val();
        LoadData();
    });
    
    $search.click(function () {
        from_date = $('#from_date input').val();
        to_date = $('#to_date input').val();
        keyword = $keyword.val().trim();
        LoadData();
    })

    function LoadData() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/statistic/get_lessions_statistic',
            type: 'GET',
            dataType: 'json',
            data: {
               keyword, from_date, to_date
            },
            success:function(response){
                $table.empty();
                const {code,msg,result} = response;
                console.log(response);
                
                if(code === 200){
                   
                    let idx = 0;
                    
                    result.forEach(l=>{
                        $table.append(`
                            <tr>
                                <td>${++idx}</td>
                                <td class="text-info fw-bold">${l.subject_name}</td>
                                <td>${l.lession_name}</td>                            
                                <td>${l.total_questions}</td>                            
                                <td>${l.total_users}</td>                            
                                <td>${l.total_exams}</td>                            
                            </tr>
                        `);
                    })
                    
                }
            },
            error:function(err){
                console.log(err);
                
            }
        })
    }
</script>