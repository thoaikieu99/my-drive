<?php
$id = $_GET['id'] ?? 'null';
$file = 'data.txt';
if (is_numeric($id)) {
    if (file_exists($file)) {
        // Đọc file thành mảng, giữ nguyên các dòng trống
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        // Kiểm tra nếu dòng đó tồn tại thì xóa đi
        if (isset($lines[$id])) {
            unset($lines[$id]);
            // Ghi lại nội dung mới vào file
            file_put_contents($file, implode(PHP_EOL, $lines));
            echo "Đã xóa dòng số " . ($id + 1);
        } else {
            echo "Không tìm thấy dòng cần xóa.";
        }
    }
} else {

    if (file_put_contents($file, '') !== false) {
        echo "Da xoa sach";
    } else {
        return "Loi";
    }
}
