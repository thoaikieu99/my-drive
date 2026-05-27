<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetDir = "uploads/";

    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = basename($_FILES["fileToUpload"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $tempPath = $_FILES["fileToUpload"]["tmp_name"];

    // Move the file from the temporary location to the target directory
    if (move_uploaded_file($tempPath, $targetFilePath)) {
        echo "The file " . $fileName . " has been uploaded successfully.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
