<!DOCTYPE html>
<html>

<head>
  <style>
    .aaa {
      display: block;
      margin-bottom: 10px;
    }
  </style>

</head>

<body>
  <div id="fileName">
    <?php
    $dir = "uploads/*.*"; // Lấy tất cả các file trong folder


    foreach (glob($dir) as $file) {
      echo "<a class='aaa' href='uploads/" . basename($file) . "'>" . basename($file) . "</a>";
    }
    ?>
  </div>
  Select file to upload:
  <form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return xacNhanSubmit();">

    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="text" name="name" id="name">
    <input type="submit" value="Upload" name="submit">
  </form>

  <script>
    function xacNhanSubmit() {
      // Hiển thị hộp thoại cảnh báo
      var fileInput = document.getElementById("fileToUpload");
      var email = document.getElementById("name").value.trim();

      if (fileInput.files.length === 0) {
        alert("Vui lòng chọn một tập tin!");
        return false;
      }

      // 4. Lấy tên của file đầu tiên được chọn
      var tenFile = fileInput.files[0].name;
      var nameE = tenFile.split(".");
      var checkName = tenFile
      if (email) {
        var vali = validateFileNameOnly(email);
        if (!vali.isValid) {
          alert(vali.message);
          return false;
        }
        checkName = email + "." + nameE[1];
      }
      // console.log(email.nameE[1])
      var getC = document.getElementById('fileName');
      const children = getC.children;
      var isSb = '';
      Array.from(children).forEach(child => {
        if (checkName == child.innerText) {
          isSb = checkName;
          return false;
        }
      });
      if (isSb) {
        var kiemTra = confirm("Bạn có muốn ghi de tep tin này không?");
        return kiemTra;
      }


      // Trả về true nếu người dùng chọn "Đồng ý", false nếu chọn "Hủy"

    }


    function validateFileNameOnly(fileName) {
      fileName = fileName.trim();
      // 1. Kiểm tra trống hoặc chỉ toàn khoảng trắng
      if (!fileName || fileName.trim() === "") {
        return {
          isValid: false,
          message: "Tên file không được để trống."
        };
      }

      // 3. Kiểm tra khoảng trắng ở GIỮA tên file (Nếu muốn CẤM HOÀN TOÀN khoảng trắng)
      if (fileName.includes(" ")) {
        return {
          isValid: false,
          message: "Tên file không được chứa khoảng trắng."
        };
      }

      // 4. Kiểm tra ký tự đặc biệt bị cấm (\ / : * ? " < > |)
      const invalidCharsRegex = /[\\/:*?"<>|]/;
      if (invalidCharsRegex.test(fileName)) {
        return {
          isValid: false,
          message: "Tên file chứa ký tự cấm (\\ / : * ? \" < > |)."
        };
      }

      // 5. Giới hạn độ dài tên file (Hệ điều hành thường giới hạn dưới 255 ký tự)
      if (fileName.length > 30) {
        return {
          isValid: false,
          message: "Tên file quá dài (tối đa 30 ký tự)."
        };
      }

      return {
        isValid: true,
        message: "Tên file hợp lệ."
      };
    }
  </script>

</body>

</html>