<?php

// index.php

$baseUploadDir = "uploads/";

// Tạo thư mục uploads
if (!is_dir($baseUploadDir)) {
  mkdir($baseUploadDir, 0777, true);
}

$message = "";

/* =========================
   TẠO FOLDER
========================= */
if (isset($_POST['create_folder'])) {

  $folderName = trim($_POST['folder_name']);

  if ($folderName != "") {

    $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $folderName);

    $newFolder = $baseUploadDir . $folderName;

    if (!is_dir($newFolder)) {

      mkdir($newFolder, 0777, true);

      $message = "✅ Đã tạo folder: $folderName";
    } else {

      $message = "⚠ Folder đã tồn tại";
    }
  }
}

/* =========================
   FOLDER HIỆN TẠI
========================= */
$currentFolder = isset($_GET['folder'])
  ? basename($_GET['folder'])
  : "";

$currentPath = $baseUploadDir;

if ($currentFolder != "") {
  $currentPath .= $currentFolder . "/";
}

/* =========================
   DOWNLOAD FOLDER ZIP
========================= */
if (isset($_GET['download_folder'])) {

  $folder = basename($_GET['download_folder']);

  $folderPath = $baseUploadDir . $folder;

  if (is_dir($folderPath)) {

    $zipName = $folder . ".zip";

    $zip = new ZipArchive();

    if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

      $files = scandir($folderPath);

      foreach ($files as $file) {

        if ($file != "." && $file != "..") {

          $filePath = $folderPath . "/" . $file;

          if (is_file($filePath)) {

            $zip->addFile($filePath, $file);
          }
        }
      }

      $zip->close();

      header('Content-Type: application/zip');

      header('Content-Disposition: attachment; filename=' . $zipName);

      header('Content-Length: ' . filesize($zipName));

      readfile($zipName);

      unlink($zipName);

      exit;
    }
  }
}

/* =========================
   CHẶN ROOT UPLOAD
========================= */
if ($currentFolder == "" && isset($_FILES['images'])) {

  die("Không được upload vào thư mục root");
}

/* =========================
   UPLOAD ẢNH
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['images'])) {

  if (!empty($_FILES['images']['name'][0])) {

    $total = count($_FILES['images']['name']);

    for ($i = 0; $i < $total; $i++) {

      $tmpName = $_FILES['images']['tmp_name'][$i];

      $originalName = basename($_FILES['images']['name'][$i]);

      $fileName = time() . "_" . rand(1000, 9999) . "_" . $originalName;

      $targetFile = $currentPath . $fileName;

      $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

      $allowTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

      if (in_array($ext, $allowTypes)) {

        if (move_uploaded_file($tmpName, $targetFile)) {

          $message .= "✅ Upload: $fileName <br>";
        } else {

          $message .= "❌ Lỗi upload: $fileName <br>";
        }
      }
    }
  }
}

/* =========================
   XOÁ ẢNH
========================= */
if (isset($_GET['delete'])) {

  $file = basename($_GET['delete']);

  $path = $currentPath . $file;

  if (file_exists($path)) {

    unlink($path);

    header("Location: ?folder=" . urlencode($currentFolder));
    exit;
  }
}

/* =========================
   XOÁ TOÀN BỘ ẢNH
========================= */
if (isset($_GET['clear_folder'])) {

  $folder = basename($_GET['clear_folder']);

  $folderPath = $baseUploadDir . $folder . "/";

  if (is_dir($folderPath)) {

    foreach (scandir($folderPath) as $file) {

      if ($file != "." && $file != "..") {

        $filePath = $folderPath . $file;

        if (is_file($filePath)) {

          unlink($filePath);
        }
      }
    }

    header("Location: ?folder=" . urlencode($folder));
    exit;
  }
}

/* =========================
   XOÁ FOLDER
========================= */
if (isset($_GET['delete_folder'])) {

  $folder = basename($_GET['delete_folder']);

  $folderPath = $baseUploadDir . $folder;

  if (is_dir($folderPath)) {

    foreach (scandir($folderPath) as $file) {

      if ($file != "." && $file != "..") {

        $filePath = $folderPath . "/" . $file;

        if (is_file($filePath)) {

          unlink($filePath);
        }
      }
    }

    rmdir($folderPath);

    header("Location: index.php");
    exit;
  }
}

/* =========================
   DANH SÁCH FOLDER
========================= */
$folders = [];

foreach (scandir($baseUploadDir) as $item) {

  if ($item != "." && $item != "..") {

    if (is_dir($baseUploadDir . $item)) {

      $folders[] = $item;
    }
  }
}

/* =========================
   DANH SÁCH ẢNH
========================= */
$images = [];

if ($currentFolder != "" && is_dir($currentPath)) {

  foreach (scandir($currentPath) as $file) {

    if ($file != "." && $file != "..") {

      if (is_file($currentPath . $file)) {

        $images[] = $file;
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Quản lý ảnh</title>

  <style>
    body {
      margin: 0;
      padding: 30px;
      background: #f4f4f4;
      font-family: Arial;
    }

    .container {
      max-width: 1200px;
      margin: auto;
    }

    .card {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
    }

    h1 {
      margin-top: 0;
    }

    /* Folder */

    .folder-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }

    .folder {
      padding: 10px 18px;
      background: #007bff;
      color: white;
      border-radius: 10px;
      text-decoration: none;
    }

    .folder:hover {
      background: #0056b3;
    }

    /* Create folder */

    .create-folder {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .create-folder input {
      flex: 1;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
    }

    .create-folder button {
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      background: green;
      color: white;
      cursor: pointer;
    }

    /* Download folder */

    .download-folder {
      display: inline-block;
      margin-top: 15px;
      padding: 12px 20px;
      background: green;
      color: white;
      text-decoration: none;
      border-radius: 10px;
    }

    /* Preview */

    .preview {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 20px;
    }

    .preview-item {
      position: relative;
      width: 140px;
      height: 140px;
    }

    .preview-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 12px;
    }

    .remove-btn {
      position: absolute;
      top: -8px;
      right: -8px;
      width: 30px;
      height: 30px;
      border: none;
      border-radius: 50%;
      background: red;
      color: white;
      cursor: pointer;
    }

    .add-more {
      width: 140px;
      height: 140px;
      border: 2px dashed #999;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      cursor: pointer;
      background: #fafafa;
    }

    .hidden-input {
      display: none;
    }

    /* Buttons */

    .upload-btn {
      margin-top: 20px;
      padding: 12px 25px;
      border: none;
      border-radius: 10px;
      background: #007bff;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }

    .delete-btn {
      display: inline-block;
      background: red;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
    }

    /* Message */

    .message {
      background: #eee;
      padding: 15px;
      border-radius: 10px;
      margin-top: 20px;
    }

    /* Gallery */

    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }

    .image-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .image-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      cursor: pointer;
    }

    .image-info {
      padding: 15px;
    }

    /* Modal */

    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.9);
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    .modal img {
      max-width: 90%;
      max-height: 80%;
      border-radius: 15px;
    }

    .modal-btn {
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      background: #007bff;
      color: white;
      text-decoration: none;
    }

    .close-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 40px;
      color: white;
      cursor: pointer;
    }
  </style>
</head>

<body>



  <div class="container">

    <!-- FOLDER -->
    <div class="card">
      <?php
      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
      $domain = $_SERVER['HTTP_HOST'];

      $project_path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

      $base_url = $protocol . $domain . $project_path;
      $base_url = dirname($base_url) . '/';
      echo "<div class='back'>";
      echo "<a  href='$base_url'>Back</a><br>";
      echo "</div>";
      ?>

      <h1>Folders</h1>

      <div class="folder-list">

        <a class="folder" href="index.php">
          📁 Root
        </a>

        <?php foreach ($folders as $folder): ?>

          <a class="folder"
            href="?folder=<?= urlencode($folder) ?>">
            📁 <?= htmlspecialchars($folder) ?>
          </a>

        <?php endforeach; ?>

      </div>

      <form method="POST" class="create-folder">

        <input type="text"
          name="folder_name"
          placeholder="Tên folder mới">

        <button type="submit" name="create_folder">
          Tạo Folder
        </button>

      </form>

      <?php if ($currentFolder != ""): ?>

        <a class="download-folder"
          href="?download_folder=<?= urlencode($currentFolder) ?>">
          ⬇ Download Folder
        </a>

        <br><br>

        <a class="delete-btn"
          href="?clear_folder=<?= urlencode($currentFolder) ?>"
          onclick="return confirm('Xóa toàn bộ ảnh trong folder?')"
          style="background:orange;">
          🗑 Xóa toàn bộ ảnh
        </a>

        <a class="delete-btn"
          href="?delete_folder=<?= urlencode($currentFolder) ?>"
          onclick="return confirm('Xóa folder này?')"
          style="background:darkred;">
          ❌ Xóa Folder
        </a>

      <?php endif; ?>

      <?php if ($message): ?>

        <div class="message">
          <?= $message ?>
        </div>

      <?php endif; ?>

    </div>

    <!-- UPLOAD -->
    <div class="card">

      <h1>
        Upload ảnh
        <?= $currentFolder ? " - $currentFolder" : "" ?>
      </h1>

      <?php if ($currentFolder == ""): ?>

        <div class="message">
          ⚠ Vui lòng tạo hoặc chọn folder để upload ảnh
        </div>

      <?php else: ?>

        <form method="POST"
          enctype="multipart/form-data">

          <input type="file"
            id="images"
            name="images[]"
            class="hidden-input"
            accept="image/*"
            multiple>

          <div class="preview" id="preview"></div>

          <button class="upload-btn" type="submit">
            Upload
          </button>

        </form>

      <?php endif; ?>

    </div>

    <!-- GALLERY -->
    <?php if ($currentFolder != ""): ?>

      <div class="gallery">

        <?php foreach ($images as $img): ?>

          <div class="image-card">

            <img src="<?= $currentPath . $img ?>"
              onclick="openModal('<?= $currentPath . $img ?>')">

            <div class="image-info">

              <div>
                <?= htmlspecialchars($img) ?>
              </div>

              <br>

              <a class="delete-btn"
                href="?folder=<?= urlencode($currentFolder) ?>&delete=<?= urlencode($img) ?>"
                onclick="return confirm('Xóa ảnh này?')">
                Xóa
              </a>

            </div>

          </div>

        <?php endforeach; ?>

      </div>

    <?php endif; ?>

  </div>

  <!-- MODAL -->
  <div class="modal" id="imageModal">

    <span class="close-btn" onclick="closeModal()">
      ×
    </span>

    <img id="modalImage">

    <br>

    <a id="downloadBtn"
      class="modal-btn"
      download>
      ⬇ Download Ảnh
    </a>

  </div>

  <script>
    const input = document.getElementById('images');
    const preview = document.getElementById('preview');

    let selectedFiles = [];

    /* OPEN FILE PICKER */

    function openPicker() {

      input.click();
    }

    /* RENDER PREVIEW */

    function renderPreview() {

      if (!preview) return;

      preview.innerHTML = '';

      selectedFiles.forEach((file, index) => {

        const reader = new FileReader();

        reader.onload = function(e) {

          const div = document.createElement('div');

          div.className = 'preview-item';

          div.innerHTML = `
                <img src="${e.target.result}">
                <button type="button"
                        class="remove-btn"
                        onclick="removeImage(${index})">
                    ×
                </button>
            `;

          preview.appendChild(div);
        }

        reader.readAsDataURL(file);

      });

      const addBtn = document.createElement('div');

      addBtn.className = 'add-more';

      addBtn.innerHTML = '+';

      addBtn.onclick = openPicker;

      preview.appendChild(addBtn);
    }

    /* ADD IMAGE */

    if (input) {

      input.addEventListener('change', function() {

        const files = Array.from(this.files);

        selectedFiles = [...selectedFiles, ...files];

        updateFiles();

        renderPreview();

      });

    }

    /* REMOVE IMAGE */

    function removeImage(index) {

      selectedFiles.splice(index, 1);

      updateFiles();

      renderPreview();
    }

    /* UPDATE FILE INPUT */

    function updateFiles() {

      const dataTransfer = new DataTransfer();

      selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
      });

      input.files = dataTransfer.files;
    }

    /* MODAL */

    const modal = document.getElementById('imageModal');

    const modalImage = document.getElementById('modalImage');

    const downloadBtn = document.getElementById('downloadBtn');

    function openModal(src) {

      modal.style.display = 'flex';

      modalImage.src = src;

      downloadBtn.href = src;
    }

    function closeModal() {

      modal.style.display = 'none';
    }

    modal.addEventListener('click', function(e) {

      if (e.target === modal) {

        closeModal();
      }

    });

    /* START */

    renderPreview();
  </script>

</body>

</html>