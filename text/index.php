<?php
$message = "";
$file = 'data.txt';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form (sử dụng isset để tránh lỗi)
    $new_content = isset($_POST['file_content']) ? $_POST['file_content'] : '';
    $clean_text  = trim(preg_replace('/^[ \t]*[\r\n]+/m', '', $new_content));
    if (trim($clean_text)) {
        $clean_text = $clean_text . "\n";
        // Ghi đè nội dung mới vào file

        if (file_put_contents($file, $clean_text, FILE_APPEND) !== false) {
            $message = "<div style='color: green; font-weight: bold;'>Cập nhật file thành công!</div>";
        } else {
            $message = "<div style='color: red; font-weight: bold;'>Lỗi! Không thể ghi file.</div>";
        }
    }
}

?>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .container-grid {
            display: grid;
            /* Cột 1 chiếm hết phần còn lại (1fr), cột 2 cố định 30px */
            grid-template-columns: 1fr 40px;
            gap: 2px;
            /* Khoảng cách giữa 2 cột */
        }

        .cot-1 {
            background: #f0f0f0;
            padding: 5px;
        }

        .cot-2 {
            background: #ffcccb;
            width: 30px;
            padding: 5px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        textarea {
            width: 100%;
            max-width: 600px;
            padding: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .my-button {
            /* Biến thẻ inline span thành block/inline-block để nhận các thuộc tính kích thước */
            display: inline-block;

            /* Tạo hiệu ứng con trỏ và chuyển đổi mượt mà */
            cursor: pointer;
            transition: background-color 0.3s ease;
            -webkit-user-select: none;
            /* Safari */
            -ms-user-select: none;
            /* IE 10 và Edge cũ */
            user-select: none;
        }

        /* Hiệu ứng khi di chuột vào nút */
        .my-button:hover {
            background-color: #0056b3;
        }

        /* Hiệu ứng khi nhấn giữ nút */
        .my-button:active {
            transform: scale(0.98);
        }
    </style>
</head>

<body>
    <div>
        <?php
        if (file_exists($file)) {
            $lines = file($file);
            foreach ($lines as $line_number => $line) {
                echo "<div class='container-grid' id='$line_number'> ";
                echo "<div class='cot-1'>";
                echo "<div>" . htmlspecialchars($line) . "</div>";
                echo "</div>";
                echo "<div class='cot-2 my-button' onclick='deleteLine(this.parentElement.id)'> ";
                echo "<span  style='color: red; font-weight: bold;'> X </span>";
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </div>
    <?php
    echo $message;
    if (isset($lines[0])) {
        echo "<button type='button' onclick='xoaSanPham()'>Delete all!</button>";
    }
    ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="file_content">Nội dung file:</label><br><br>
            <!-- Hiển thị nội dung hiện tại vào trong textarea -->
            <textarea id="file_content" name="file_content" rows="5"></textarea>
        </div>

        <button type="submit">Lưu Cập Nhật</button>
    </form>
    <script>
        function clearData() {
            fetch('clear.php')
                .then(response => response.text())
                .then(data => {
                    alert(data); // Hiển thị thông báo từ PHP (Tùy chọn)

                    location.href = window.location.pathname; // Tải lại trang ngay lập tức 🔄
                });
        }

        function deleteLine(id) {
            fetch('clear.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    const buttons = document.getElementsByClassName('container-grid');
                    buttons[id].remove();
                    Array.from(buttons).forEach((element, index) => {
                        if (index >= (id)) {

                            element.id = `${index}`;

                        }
                    });
                    // location.href = window.location.pathname; // Tải lại trang ngay lập tức 🔄
                });

        }

        function xoaSanPham() {
            // 1. Hiện hộp thoại hỏi Yes/No
            let xacNhan = confirm("Bạn có chắc chắn muốn xóa tất cả không?");

            // 2. Nếu chọn Yes (OK)
            if (xacNhan) {
                clearData();
                // alert("Đã xóa sản phẩm có ID: " + parentId);
                // // Bạn có thể viết tiếp code Ajax để xóa trong Database tại đây
            } else {
                // Nếu chọn No (Cancel)
                console.log("Đã hủy thao tác xóa.");
            }
        }
    </script>
</body>

</html>