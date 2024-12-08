<div class="container">
    <div class="row mb-4 mt-2">
        <div class="col col-md-2 offset-md-4">
            <select name="" id="slSchoolYears" class="form-control"></select>
        </div>
        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search user..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
        <div class="col col-md-2 align-self-end text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa fa-plus text-white" data-bs-toggle="modal" data-bs-target="#modal"></i> Thêm
                mới
            </button>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Giáo viên</th>
            <th scope="col">Môn học</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody id="tblData"></tbody>
</table>

<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-end pagination-sm">

        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>

    </ul>
</nav>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTittle">Quá trình giảng dạy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="containter">
                    <div class="form-group">
                        <Label>Giáo viên</Label>
                        <select name="" id="slTeachers" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="">Môn học</label>
                        <select name="" id="slSubjects" class="form-control"></select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSubmit">Xác nhận</button>
            </div>
        </div>
    </div>
</div>


<script>
    var keyword = '',
        page = 1,
        pageSize = 10,
        id = -1;
    var pageNumber = 1; // Biến toàn cục để theo dõi tổng số trang
    const $btnSearch = $('#btnSearch'),
        $btnSubmit = $('#btnSubmit'),
        $keyword = $('#txtKeyword'),
        $table = $('#tblData'),
        $pagination = $('.pagination'),
        $modal = $('#modal'),
        $slSubjects = $('#slSubjects'),
        $slTeachers = $('#slTeachers'),
        $slSchoolYears = $('#slSchoolYears'),
        $modalTittle = $('#modalTittle');


    $(document).ready(function() {
        var currentYear = new Date().getFullYear();
        for (i = currentYear - 4; i <= currentYear + 10; i++) {
            $slSchoolYears.append(`<option value='${i}-${i+1}' ${i==currentYear?'selected':''}>Năm học ${i}-${i+1}</option>`)
        }
        LoadTeachers();
        LoadSubjects();
        LoadData();
        $('.pagination').on('click', '.page-link', function(e) {

            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).data('page'); // Lấy giá trị data-page           
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
                        console.log({
                            page
                        });
                        LoadData();
                    }
                }
            }

        });
    });

    $btnSubmit.click(function() {       
        
        let schoolyear = $('#slSchoolYears option:selected').val();
        let teacher_id = $slTeachers.val();
        let subject_id = $slSubjects.val();       
        let url = `<?php echo BASE_URL; ?>/admin/teaching/${id<0?'add':'update'}`;
        let data = {
            teacher_id,
            schoolyear,
            subject_id
        };
        if (id > 0) data.id = id;        
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                const {
                    code,
                    msg
                } = response;

                $.toast({
                    heading: (code == 200 || code == 201) ? 'SUCCESSFULLY' : 'ERROR',
                    text: msg,
                    icon: (code == 200 || code == 201) ? 'success' : 'error',
                    loader: true,
                    loaderBg: '#9EC600'
                });
                LoadData();
                $modal.modal('hide');
            },
            error: function(err) {
                console.log(err.responseText);
            }
        })

    })

    function LoadSubjects() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/subject/allSubjects',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                response.subjects.forEach(s => {
                    $slSubjects.append(`<option value="${s.id}">${s.name}</option>`);
                })
            },
            error: function(err) {
                console.log(err.responseText);
            }
        })
    }

    $btnSearch.click(function() {
        page = 1;
        keyword = $keyword.val().trim();
        console.log({
            keyword
        });
        LoadData();
    })

    function LoadData() {
        $table.empty();
        $pagination.empty();
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/teaching/search`,
            type: 'GET',
            dataType: 'json',
            data: {
                page,
                pageSize,
                keyword: $('#txtKeyword').val().trim()
            },
            success: function(response) {
                const {
                    currentPage,
                    totalPages,
                    teachings
                } = response;
                let idx = (page - 1) * pageSize;
                teachings.forEach(t => {
                    $table.append(`
                        <tr>
                            <td>${++idx}</td>
                            <td class = "fw-bold text-info">${t.teacher_name}</td>
                            <td class = "text-info">${t.subject_name}</td>                           
                            <td class="text-end">
                                <a href="javascript:void(0)" onClick="UpdateTeaching(${t.id})"><i class="fa fa-edit text-warning"></i></a>
                                <a href="javascript:void(0)" onClick="DeleteTeaching(${t.id},'${t.teacher_name}','${t.subject_name}')"><i class="fa fa-trash-o text-danger"></i></a>
                            </td>                          
                        </tr>
                    `);

                })
                $pagination.append(`<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true" data-page="0">Previous</a>
                            </li>`);
                for (let i = 1; i <= totalPages; i++) {
                    $pagination.append(`<li class="page-item ${currentPage === i ? "active" : ""}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>`);
                }
                $pagination.append(`<li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
                                <a class="page-link" href="#" data-page="${totalPages}">Next</a>
                            </li>`);
            },
            error: function(err) {
                console.log(err.responseText);
            }
        })
    }

    function UpdateTeaching(id){
        this.id = id;
        $.ajax({
            url: `<?php echo BASE_URL; ?>/admin/teaching/detail`,
            type:'GET',
            dataType:'json',
            data:{id},
            success:function(response){
                console.log(response);
                const {code,msg,detail} = response;
                $modal.modal('show');                
                const {teacher_id,subject_id} = detail;
                $slSubjects.val(subject_id);
                $slTeachers.val(teacher_id);
                $modalTittle.text('Cập nhật thông tin giảng dạy');
            },
            error:function(err){
                console.log(err.responseText);
                
            }
        })
        
    }
    function DeleteTeaching(id,teacher_name,subject_name){
        console.log({id,teacher_name,subject_name});
        Swal.fire({
            title: `Bạn thực sự muốn xoá quá trình giảng dạy môn <span class="text-warning">${subject_name}</span> của giáo viên <span class = "text-info fw-bold">${teacher_name}</span>?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>/admin/teaching/delete',
                    type: 'POST',
                    data: { id },
                    dataType: 'json',
                    success: function (response) {
                        const { code, msg } = response;
                        $.toast({
                            heading: code == 200 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                            text: msg,
                            icon: code == 200 ? 'success' : 'error',
                            loader: true,
                            loaderBg: '#9EC600'
                        });
                        if (code == 200) {
                            LoadData();
                        }
                    },
                    error: function (err) {
                        console.log(err.responseText);
                    }
                });
            }
        });
        
    }

    function LoadTeachers() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/teacher/search', // URL của phương thức search trong UserController
            method: 'GET',
            data: {
                keyword: '',
                page,
                pageSize: 1000
            },
            dataType: 'json',
            success: function(response) {

                // Hiển thị dữ liệu người dùng và thông tin phân trang
                const {
                    users,
                    totalPages,
                    currentPage,
                    pageSize,
                    totalRecords
                } = response;

                users.forEach(u => {
                    $slTeachers.append(`<option value = '${u.id}'>${u.fullname} (<span class = "text-danger">${u.username}</span>)</option>`)
                });



            },
            error: function(err) {
                console.log(err.responseText);
            }
        });
    }


    $modal.on('hide.bs.modal', function(e) {
        $modalTittle.text('Thêm mới quá trình giảng dạy');
        id = -1;
    });
</script>