root
│   .htaccess
│   composer.json
│   index.php
└───app
    ├───controllers
    │   ├───admin
    │   │   ├───AdminAuthController.php
    │   │   └───DashboardController.php
    │   ├───AuthController.php
    │   ├───HomeController.php
    │   ├───ProductController.php
    │   └───UserAuthController.php
    ├───core
    │   ├───App.php
    │   ├───BaseController.php
    │   ├───Controller.php
    │   └───AdminController.php
    ├───models
    │   ├───Product.php
    │   └───UserModel.php
    ├───views
    │   ├───layouts
    │   │   ├───main.php
    │   │   └───admin.php
    │   ├───home
    │   │   └───index.php
    │   ├───auth
    │   │   └───login.php
    │   └───admin
    │       └───auth
    │           └───login.php
    └───init.php
└───public
    └───assets
        ├───css
        │   └───styles.css
        ├───images
        │   └───logo.png
        └───js
            └───script.js



- Định dạng câu hỏi chưa ổn (giao diện làm bài thay đổi so với lúc soạn câu hỏi, chưa đúng với khi biên soạn như: không lỗi không xuống dòng/không hiển thị blank trả lời (hay mình chưa biết cách nhập?)), khoảng trống dành cho phần trả lời còn thiếu so với một số câu hỏi có trả lời dài, -> nên tự động dãn ra/thu nhỏ theo ký tự trả lời?
- ko  lần làm bài đã giới hạn 3 lần, do vậy nên có thông báo sau khi lưu bài lần 3 và khóa chức năng làm bài.
- Sau khi làm xong bài, trả lời từng câu thì khi quay lại bị mất kết quả, phải làm lại -> sửa lại. 
- Do đã quy định số lần làm bài sau khi “lưu kết quả”, do vậy có nên cho lưu và không cho chỉnh sửa sau khi trả lời không hoặc nếu mục đích để ôn tập thì có thể chỉnh sửa khi biết làm sai nhưng cũng phải quy định số lần cho từng câu khi làm? hoặc tính giờ làm? 
- Phần chi tiết bài thi nên hiển thị tất cả câu hỏi, có đáp án (bôi đỏ những câu làm sai), mục đích để bạn nào làm lần 1 có thể vào tham khảo để làm lần 2-3 cho tốt hơn hoặc hiển thị kết quả ngoài màn hình làm bài luôn (câu nào sai màu đỏ)?
- Chưa sửa, xóa được câu hỏi, khi mở ra bị ẩn và mất đáp án (done)
- Hoàn thiện chức năng thống kê (xuất file kết quả làm bài của các user) 
- Thông tin tài khoản, thay đổi mật khẩu (có thể import danh sách lớp (user))

