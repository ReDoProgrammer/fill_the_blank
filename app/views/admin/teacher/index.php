<div class="container">
    <div class="row justify-content-end mb-4">
        <div class="col col-md-4 align-self-end text-end">
            <div class="input-group">
                <input type="text" id="txtKeyword" class="form-control" placeholder="Search user..." />
                <button class="btn btn-outline-secondary btn-info text-white" type="button" id="btnSearch">
                    <i class="fa fa-search fw-bold"></i> Search
                </button>
            </div>
        </div>
        <div class="col col-md-4 align-self-end text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
                <i class="fa fa-plus text-white" data-bs-toggle="modal" data-bs-target="#modal"></i> Thêm
                mới
            </button>
            <input type="file" id="btnUpload" class="btn btn-warning text-white" style="display:none;"
                accept=".xlsx, .xls" />
            <button class="btn btn-warning text-white" id="uploadTrigger">
                <i class="fa fa-upload" aria-hidden="true"></i> Upload
            </button>
            <button class="btn btn-danger text-white" id="deleteMany">
                <i class="fa fa-times" aria-hidden="true"></i> Delete (<span id="deleteCount" class="fw-bold">0</span>)
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
            <th scope="col">Tài khoản</th>
            <th scope="col">Mã giáo viên</th>
            <th scope="col">Họ và tên</th>
            <th scope="col">Điện thoại</th>
            <th scope="col">Email</th>
            <th scope="col">Quyền</th>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTittle">Thêm mới tài khoản giáo viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="containter">
                    <div class="row mb-3">
                        <div class="col-md-4 form-group">
                            <label for="">Tài khoản:</label>
                            <input type="text" name="" id="txtUsername" placeholder="Tài khoản giáo viên"
                                class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Mã giáo viên:</label>
                            <input type="text" name="" id="txtUserCode" placeholder="Mã giáo viên" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="">Họ và tên:</label>
                            <input type="text" name="" id="txtFullname" placeholder="Họ và tên" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            <label for="">Mật khẩu</label>
                            <input type="password" placeholder="Mật khẩu" id="txtPassword" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Xác nhận mật khẩu</label>
                            <input type="password" placeholder="Xác nhận mật khẩu" id="txtConfirmPassword"
                                class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="">Điện thoại:</label>
                            <input type="text" placeholder="Phone" id="txtPhone" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Email:</label>
                            <input type="text" placeholder="Địa chỉ hòm thư điện tử" id="txtEmail" class="form-control">
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
        $modalTittle = $('#modalTittle');
    const $btnUpload = $('#btnUpload');
    const $username = $('#txtUsername'),
        $usercode = $('#txtUserCode'),
        $fullname = $('#txtFullname'),
        $password = $('#txtPassword'),
        $confirmPassword = $('#txtConfirmPassword'),
        $phone = $('#txtPhone'),
        $email = $('#txtEmail');
    const $uploadTrigger = $('#uploadTrigger');
    const $deleteMany = $('#deleteMany');
    $(document).ready(function() {
        LoadData();

        // Khi click vào nút upload, giả lập click vào input file
        $uploadTrigger.click(function() {
            $btnUpload.click();
        });

        // Hàm tạo username từ fullname
        function generateUsername(fullName) {
            const removeDiacritics = (str) => {
                return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D");
            };

            const nameParts = removeDiacritics(fullName).toLowerCase().split(' ');
            const lastName = nameParts.pop();
            const initials = nameParts.map(name => name.charAt(0)).join('');
            return lastName + initials;
        }

        // Sự kiện khi file được chọn
        $btnUpload.change(function(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {
                    type: 'array'
                });

                var jsonData = [];

                workbook.SheetNames.forEach(function(sheetName) {
                    var worksheet = workbook.Sheets[sheetName];
                    var sheetJson = XLSX.utils.sheet_to_json(worksheet, {
                        header: 1
                    });

                    // Kiểm tra xem file có tiêu đề cột hay không
                    var headers = Array.isArray(sheetJson[0]) ? sheetJson.shift() : [];

                    var result = sheetJson.map(function(row) {
                        var rowData = {};

                        // Tạo một đối tượng từ mỗi hàng
                        row.forEach(function(cell, index) {
                            var key = headers[index] || `Column${index + 1}`;
                            rowData[key] = cell;
                        });

                        // Nếu có cột fullname, thêm cột username
                        if (rowData['Fullname']) {
                            rowData['Username'] = generateUsername(rowData['Fullname']);
                        }

                        return rowData;
                    });

                    jsonData.push({
                        sheetName: sheetName,
                        data: result
                    });
                });


                const result = jsonData[0].data;

                if (!checkPropertyInArray(result, 'Usercode') || !checkPropertyInArray(result, 'Fullname') || !checkPropertyInArray(result, 'Phone') || !checkPropertyInArray(result, 'Email') || !checkPropertyInArray(result, 'Password')) {
                    Swal.fire({
                        title: "Dữ liệu không hợp lệ!",
                        text: "Có vẻ như bạn đang import 1 file excel không đúng định dạng. Vui lòng kiểm tra lại",
                        icon: "error"
                    });
                    return;
                }
                if (result && result.length > 1) {
                    $.ajax({
                        url: '<?php echo BASE_URL; ?>/admin/user/import',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            users: result
                        },
                        success: function(response) {
                            const {
                                code,
                                msg
                            } = response;
                            if (code === 201) {
                                $.toast({
                                    heading: code === 200 || code === 201 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                                    text: msg,
                                    icon: code === 200 || code === 201 ? 'success' : 'error',
                                    loader: true, // Change it to false to disable loader
                                    loaderBg: '#9EC600' // To change the background
                                });
                                LoadData();
                            }
                        },
                        error: function(err) {
                            console.log(err);

                        }
                    })
                } else {
                    Swal.fire({
                        title: "Dữ liệu không hợp lệ!",
                        text: "File danh sách thành viên không có dữ liệu",
                        icon: "error"
                    });
                }

            };

            reader.readAsArrayBuffer(file);
        });

        $deleteMany.prop('disabled', true);
        // Khi checkAll được chọn hoặc bỏ chọn
        $('#checkAll').on('change', function() {
            // Lấy trạng thái của checkbox checkAll
            var isChecked = $(this).prop('checked');

            // Chọn hoặc bỏ chọn tất cả các checkbox trong bảng dựa trên trạng thái của checkAll
            $('#tblData input.form-check-input').prop('checked', isChecked);

            // Cập nhật số lượng checkbox được chọn
            updateDeleteCount();
        });

        // Khi bất kỳ checkbox nào trong bảng được chọn hoặc bỏ chọn
        $('#tblData').on('change', 'input.form-check-input', function() {
            // Kiểm tra xem tất cả các checkbox trong bảng có được chọn không
            var allChecked = $('#tblData input.form-check-input').length === $('#tblData input.form-check-input:checked').length;

            // Cập nhật trạng thái của checkbox checkAll
            $('#checkAll').prop('checked', allChecked);

            // Cập nhật số lượng checkbox được chọn
            updateDeleteCount();
        });


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



    $deleteMany.on('click', function() {
        // Lấy mảng các ID của các hàng có checkbox được chọn
        var selectedIds = $('#tblData input.form-check-input:checked').map(function() {
            return parseInt($(this).closest('tr').attr('id'));
        }).get();

        Swal.fire({
            title: `Bạn thực sự muốn xoá <span class = "fw-bold text-danger">${$('#deleteCount').text()}</span> tài khoản này?`,
            html: "<p class = 'text-danger'>Bạn sẽ không thể khôi phục lại dữ liệu đã bị xoá!</p>",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Vâng, Hãy xoá nó!"
        }).then((result) => {
            if (result.isConfirmed) {
                selectedIds.forEach(id => {
                    DeleteSingle(id);
                })
                $('#deleteCount').text('0');
                $("#checkAll").prop("checked", false);
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


    $btnSubmit.click(function() {
        const username = $username.val().trim();
        const usercode = $usercode.val().trim();
        const password = $password.val().trim();
        const confirmPassword = $confirmPassword.val().trim();
        const fullname = $fullname.val().trim();
        const phone = $phone.val().trim();
        const email = $email.val().trim();
        const role='teacher';
        if (username.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Tài khoản không thể để trống',
                icon: 'error',
                loader: true, // Change it to false to disable loader
                loaderBg: '#9EC600' // To change the background
            })
            return;
        }
        if (usercode.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Mã giáo viên không thể để trống',
                icon: 'error',
                loader: true, // Change it to false to disable loader
                loaderBg: '#9EC600' // To change the background
            })
            return;
        }

        if (fullname.length == 0) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Họ và tên không thể để trống',
                icon: 'error',
                loader: true, // Change it to false to disable loader
                loaderBg: '#9EC600' // To change the background
            })
            return;
        }
        if (password.length == 0 || confirmPassword.length == 0 || password != confirmPassword) {
            $.toast({
                heading: 'Ràng buộc dữ liệu',
                text: 'Mật khẩu không hợp lệ',
                icon: 'error',
                loader: true, // Change it to false to disable loader
                loaderBg: '#9EC600' // To change the background
            })
            return;
        }


        const url = `<?php echo BASE_URL; ?>/admin/user/${id <= 0 ? 'add' : 'update'}`;
        const data = {
            username: username,
            usercode,
            fullname: capitalizeWords(fullname),
            password: password,
            phone: phone,
            email: email,
            role
        }
        if (id > 0) data.id = id;

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json', // Chỉ định rằng bạn mong đợi JSON
            success: function(response) {

                // Nếu response không phải là đối tượng JSON, hãy thử parse nó
                // response = JSON.parse(response); // Sử dụng nếu dữ liệu không tự động parse

                const {
                    code,
                    msg
                } = response;
                $.toast({
                    heading: code === 200 || code === 201 ? 'SUCCESSFULLY' : `ERROR: ${code}`,
                    text: msg,
                    icon: code === 200 || code === 201 ? 'success' : 'error',
                    loader: true, // Change it to false to disable loader
                    loaderBg: '#9EC600' // To change the background
                });
                if (code == 201 || code == 200) {
                    $modal.modal('hide');
                    LoadData();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    })

    function LoadData() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>/admin/teacher/search', // URL của phương thức search trong UserController
            method: 'GET',
            data: {
                keyword,
                page,              
                pageSize
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
                let idx = (page - 1) * pageSize;
                pageNumber = totalPages;
                $table.empty(); // Đảm bảo bảng được làm mới trước khi thêm dữ liệu
                $pagination.empty(); // Đảm bảo phân trang được làm mới trước khi thêm dữ liệu

                users.forEach(u => {
                    $table.append(`
                    <tr id = "${u.id}">
                       <td class="text-center">
                            ${u.role != "admin" ? `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="check_${u.id}">
                                </div>
                            ` : ''}
                        </td>

                        <td>${++idx}</td>
                        <td class="fw-bold">${u.username}</td>
                        <td class="fw-bold text-secondary">${u.user_code}</td>
                        <td>${u.fullname}</td>
                        <td>${u.phone}</td>
                        <td>${u.email}</td>
                        <td class="fw-bold text-success">${u.role}</td>
                        <td class="text-end">
                        <a href="javascript:void(0)" onClick="UpdateUser(${u.id})"><i class="fa fa-edit text-warning"></i></a>
                        ${u.role == 'user' ? `
                            <a href="javascript:void(0)" onClick="DeleteUser(${u.id},'${u.username}')"><i class="fa fa-trash-o text-danger"></i></a>` : ''}                            
                        </td>
                    </tr>
                `);
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

    function generateUsername(fullName) {
        // Loại bỏ dấu trong họ tên
        const removeDiacritics = (str) => {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D");
        };

        // Tách họ tên thành mảng các từ
        const nameParts = removeDiacritics(fullName).toLowerCase().split(' ');

        // Lấy tên cuối cùng (tên chính)
        const lastName = nameParts.pop();

        // Lấy chữ cái đầu của các từ còn lại (họ và tên đệm)
        const initials = nameParts.map(name => name.charAt(0)).join('');

        // Kết hợp tên chính với chữ cái đầu của họ và tên đệm
        return lastName + initials;
    }

    function checkPropertyInArray(array, property) {
        return array.every(function(item) {
            return item.hasOwnProperty(property);
        });
    }

    // Hàm cập nhật số lượng checkbox được chọn
    function updateDeleteCount() {
        var checkedCount = $('#tblData input.form-check-input:checked').length;
        $deleteMany.prop('disabled', checkedCount > 0 ? false : true);

        $('#deleteCount').text(checkedCount);
    }
</script>