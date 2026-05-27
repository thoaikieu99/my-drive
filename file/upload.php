<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .aaa {
            display: block;
            margin-bottom: 10px;
        }

        .back {
            padding-bottom: 1.2rem;
        }
    </style>

</head>

<body>
    <?php

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $projectFolder = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

    $baseUrl = $protocol . "://" . $host . $projectFolder;


    echo "<div class='back'>";
    echo "<a  href='$baseUrl'>Back</a><br>";
    echo "</div>";


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $targetDir = "uploads/";

        // Ensure the uploads directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        if (isset($_POST['name']) && $_POST['name']) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $fileName = $_POST['name'] . '.' . $extension;
        }


        $targetFilePath = $targetDir . $fileName;
        $tempPath = $_FILES["fileToUpload"]["tmp_name"];

        // Move the file from the temporary location to the target directory
        if (move_uploaded_file($tempPath, $targetFilePath)) {
            echo "The file " . $fileName . " has been uploaded successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    ?>
</body>

</html>