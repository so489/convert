<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Folder setup
$uploadDir = 'uploads/';
$outputDir = 'converted/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

// POST request handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileType = $_POST['file_type'] ?? '';
    $file = $_FILES['file'];

    if (!$file || $file['error'] !== 0) {
        die("❌ File upload failed.");
    }

    $originalName = basename($file['name']);
    $tmpPath = $file['tmp_name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
    $savedFile = $uploadDir . $originalName;

    move_uploaded_file($tmpPath, $savedFile);

    // === IMAGE CONVERSION ===
    if ($fileType === 'image') {
        $outputImageFormat = strtolower($_POST['format_image'] ?? '');
        if (empty($outputImageFormat)) {
            die("❌ Please select an output format for the image.");
        }

        // Create image resource
        switch ($ext) {
            case 'jpeg':
            case 'jpg':
                $img = imagecreatefromjpeg($savedFile); break;
            case 'png':
                $img = imagecreatefrompng($savedFile); break;
            case 'gif':
                $img = imagecreatefromgif($savedFile); break;
            case 'bmp':
                $img = imagecreatefrombmp($savedFile); break;
            case 'webp':
                $img = imagecreatefromwebp($savedFile); break;
            default:
                die("❌ Unsupported image input format.");
        }

        // Resize
        $sizeOption = $_POST['size_option'] ?? 'original';
        $width = imagesx($img);
        $height = imagesy($img);

        switch ($sizeOption) {
            case 'small':  $width = 400; $height = 300; break;
            case 'medium': $width = 800; $height = 600; break;
            case 'large':  $width = 1280; $height = 960; break;
            case 'custom':
                $width = (int)$_POST['width'];
                $height = (int)$_POST['height'];
                if ($width < 1 || $height < 1) {
                    die("❌ Invalid custom dimensions.");
                }
                break;
        }

        $resized = imagecreatetruecolor($width, $height);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $width, $height, imagesx($img), imagesy($img));
        imagedestroy($img);
        $img = $resized;

        $outputPath = $outputDir . $filenameOnly . "_converted." . $outputImageFormat;

        // Save converted image
        switch ($outputImageFormat) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($img, $outputPath); break;
            case 'png':
                imagepng($img, $outputPath); break;
            case 'gif':
                imagegif($img, $outputPath); break;
            case 'bmp':
                imagebmp($img, $outputPath); break;
            case 'webp':
                imagewebp($img, $outputPath); break;
            default:
                die("❌ Unsupported output image format.");
        }

        imagedestroy($img);
        echo "<h2>✅ Image Converted Successfully!</h2>";
        echo "<a href='$outputPath' download>📥 Download Image</a><br><br>";
        echo "<a href='index.html'>🔁 Convert Another</a>";
        exit;
    }

    // === DOCUMENT CONVERSION ===
    if ($fileType === 'document') {
        $outputDocFormat = strtolower($_POST['format_document'] ?? '');
        if (empty($outputDocFormat)) {
            die("❌ Please select an output format for the document.");
        }

        $outputPath = $outputDir . $filenameOnly . '.' . $outputDocFormat;

        // Run LibreOffice command (ensure LibreOffice is installed on your server)
        $command = "soffice --headless --convert-to $outputDocFormat --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($savedFile);
        shell_exec($command);

        if (file_exists($outputPath)) {
            echo "<h2>✅ Document Converted Successfully!</h2>";
            echo "<a href='$outputPath' download>📥 Download Document</a><br><br>";
        } else {
            echo "<h2>❌ Document Conversion Failed</h2>";
            echo "<p>Make sure LibreOffice is installed and configured correctly on the server.</p>";
        }

        echo "<a href='index.html'>🔁 Convert Another</a>";
        exit;
    }

    // Fallback
    echo "❌ Invalid file type or missing format selection.";
}
?>
