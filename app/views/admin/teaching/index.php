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
            <th scope="col" class="text-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="checkAll">
                    <label class="form-check-label" for="checkAll">
                        All
                    </label>
                </div>
            </th>
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
                <h5 class="modal-title" id="modalTittle">Thêm mới tài khoản học viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="containter">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <Label>Giáo viên</Label>
                            <select name="" id="slTeachers" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Môn học</label>
                            <select name="" id="slSubjects" class="form-control"></select>
                        </div>
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



        $('.pagination').on('click', '.page-link', function(e) {

            e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ <a>
            currentPage = $(this).data('page'); // Lấy giá trị data-page
            console.log({
                currentPage,
                pageNumber
            });
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






    $btnSearch.click(function() {
        page = 1;
        keyword = $keyword.val().trim();
        console.log({
            keyword
        });
        LoadData();
    })

    function LoadData() {

    }


    function LoadTeachers() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/teacher/search', // URL của phương thức search trong UserController
            method: 'GET',
            data: {
                keyword:'',
                page,
                pageSize:1000
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
        });
    }

    function UpdateUser(id) {
        this.id = id;
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/user/detail',
            type: 'GET',
            data: {
                id
            },
            dataType: 'json',
            success: function(response) {
                const {
                    code,
                    msg,
                    user
                } = response;
                console.log(user);

                if (code == 200) {
                    $modal.modal('show');
                    $modalTittle.text('Cập nhật thông tin tài khoản');
                    $username.prop('readonly', true);
                    $username.val(user.username);
                    $usercode.val(user.user_code);
                    $fullname.val(user.fullname);
                    $password.val('12345');
                    $confirmPassword.val('12345');
                    $phone.val(user.phone);
                    $email.val(user.email);
                }
            },
            error: function(err) {
                console.log(err);
            }
        })
    }

    function DeleteUser(id, username) {
        Swal.fire({
            title: `Bạn thực sự muốn xoá tài khoản <span class="text-warning">${username}</span>?`,
            text: "Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                DeleteSingle(id);
            }
        });
    }


    $modal.on('hide.bs.modal', function(e) {
        $modalTittle.text('Thêm mới tài khoản');
        $username.prop('readonly', false);
        $usercode.val('');
        $username.val('');
        $fullname.val('');
        $password.val('12345');
        $confirmPassword.val('12345');
        $phone.val('');
        $email.val('');
        id = -1;
    });


    const DeleteSingle = function(id) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/user/delete', // URL của phương thức delete trong UserController
            type: 'POST',
            data: {
                id
            },
            dataType: 'json',
            success: function(response) {
                const {
                    code,
                    msg
                } = response;
                $.toast({
                    heading: code == 200 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                    text: msg,
                    icon: code == 200 ? 'success' : 'error',
                    loader: true, // Change it to false to disable loader
                    loaderBg: '#9EC600' // To change the background
                });
                if (code == 200) {
                    LoadData(); // Tải lại dữ liệu sau khi xóa thành công
                }
            },
            error: function(err) {
                console.log(err.responseText);
            }
        });
    }
</script>