<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <!-- Thẻ meta giúp trang web tự co giãn full màn hình trên điện thoại và thiết bị di động -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Web Full Màn Hình</title>
    <style>
        /* Đặt margin và padding về 0 để loại bỏ khoảng trắng mặc định */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Ép thẻ html và body chiếm trọn 100% chiều rộng và chiều cao */
        html, body {
            width: 100%;
            height: 100%;
            font-size: clamp(1rem, 2.5vw, 1.5rem);
        }

        /* Ví dụ nội dung (div) chiếm toàn bộ màn hình */
        .full-screen-container {
            width: 100vw;
            height: 100vh;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            display: flex;
            flex-direction: column; /* Sắp xếp các thẻ con thành các hàng dọc */
            gap: 5px; /* Khoảng cách giữa các hàng */
            }
            .row {
            background-color: #f0f0f0;
            padding: 10px;
        }

    </style>
</head>
<body>

    <div class="full-screen-container">
       <div class="container">
  <div class="row"><a href="text">Upload text</a></div>
  <div class="row"><a href="image">Upload image</a></div>
  <div class="row"><a href="file">Upload file</a></div>
</div>
        
    </div>

</body>
</html>
