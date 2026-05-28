<?php
// Lấy đường dẫn file từ query string
$file = $_GET['file'] ?? 'uploads/df.jpg';

// Kiểm tra file có tồn tại không
if (!file_exists($file)) {
    http_response_code(404);
    exit("File not found");
}

// Đọc ảnh gốc JPG/PNG
$info = pathinfo($file);
$ext = strtolower($info['extension']);

if ($ext === 'jpg' || $ext === 'jpeg') {
    $image = imagecreatefromjpeg($file);
    header("Content-Type: image/jpeg");
    imagejpeg($image, null, 10); // giảm chất lượng xuống 60%
} elseif ($ext === 'png') {
    $image = imagecreatefrompng($file);
    header("Content-Type: image/png");
    imagepng($image, null, 1); // mức nén 0–9
} else {
    http_response_code(415);
    exit("Unsupported format");
}

imagedestroy($image);
