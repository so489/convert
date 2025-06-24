<?php
$uploadDir = "uploads/";
$uploadedFiles = [];

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['images']['name'][$key]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetFile)) {
            $uploadedFiles[] = $targetFile;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Convertly - Image Compare</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #fff8dc, #f9d423);
      color: #333;
    }

    .topbar {
      background-color: #ffd700;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 3px solid #e5a100;
    }

    .logo-name {
      display: flex;
      align-items: center;
      gap: 12px;
      animation: popIn 0.6s ease-out;
    }

    .logo-name img {
      width: 45px;
      height: 45px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }

    .logo-name h1 {
      font-size: 1.8rem;
      color: #333;
      text-shadow: 1px 1px 2px #fff9c4;
      animation: glow 2s ease-in-out infinite;
    }

    .tagline {
      font-size: 1rem;
      color: #555;
    }

    @keyframes glow {
      0%, 100% {
        text-shadow: 1px 1px 2px #fff59d, 0 0 8px rgba(255, 215, 0, 0.5);
      }
      50% {
        text-shadow: 2px 2px 4px #ffe082, 0 0 16px rgba(255, 193, 7, 0.8);
      }
    }

    .main-nav {
      background-color: #222;
      padding: 10px 0;
    }

    .main-nav ul {
      list-style: none;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
    }

    .main-nav ul li {
      margin: 0 15px;
    }

    .main-nav ul li a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      font-size: 1rem;
      border-radius: 6px;
      transition: background 0.3s, color 0.3s;
    }

    .main-nav ul li a:hover {
      background-color: #fdd835;
      color: #222;
    }

    h1 {
      text-align: center;
      margin: 40px 0 20px;
      font-size: 2rem;
    }
    
    form {
      text-align: center;
      margin-bottom: 30px;
    }

    input[type="file"] {
      padding: 10px;
      margin-right: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      background-color: #fffde7;
    }

    button {
      padding: 10px 20px;
      background: #fbc02d;
      color: #000;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #f9a825;
    }

    .compare-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      justify-content: center;
      margin: 30px auto;
      padding: 0 20px 40px;
    }

    .image-box {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      padding: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      max-width: 300px;
      text-align: center;
      animation: fadeIn 0.6s ease;
    }

    .image-box img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .download-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 16px;
      background-color: #43a047;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s;
    }

    .download-btn:hover {
      background-color: #2e7d32;
    }

    footer {
      margin-top: 40px;
      padding: 25px;
      background-color: #222;
      color: #eee;
      text-align: center;
      font-size: 0.9rem;
    }

    footer a {
      color: #ddd;
      text-decoration: none;
      margin: 0 10px;
    }

    footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .main-nav ul {
        flex-direction: column;
        align-items: center;
      }

      .main-nav ul li {
        margin: 10px 0;
      }

      .compare-container {
        flex-direction: column;
        align-items: center;
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- Topbar -->
  <div class="topbar">
    <div class="logo-name">
      <img src="logo.png" alt="Logo">
      <h1>Convertly</h1>
    </div>
    <div class="tagline">Compare Images with Ease</div>
  </div>

  <!-- Navigation -->
  <nav class="main-nav">
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="tools.html">Tools</a></li>
      <li><a href="convert.html">Convert</a></li>
      <li><a href="compare.php">Compare</a></li>
      <li><a href="pricing.html">Pricing</a></li>
      <li><a href="contact.html">Contact</a></li>
    </ul>
  </nav>

  <!-- Title -->
  <h1>📸 Image Comparison Tool</h1>

  <!-- Upload Form -->
  <form action="compare.php" method="post" enctype="multipart/form-data">
    <input type="file" name="images[]" multiple required />
    <button type="submit">Upload & Compare</button>
  </form>

  <!-- Uploaded Images -->
  <div class="compare-container">
    <?php foreach ($uploadedFiles as $file): ?>
      <div class="image-box">
        <img src="<?= $file ?>" alt="Uploaded Image" />
        <a href="<?= $file ?>" download class="download-btn">⬇ Download</a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Footer -->
  <footer>
    © 2025 Convertly |
    <a href="#">Company</a> |
    <a href="#">About Us</a> |
    <a href="#">Security</a> |
    <a href="#">Resources</a> |
    <a href="#">Blog</a> |
    <a href="#">Status</a> |
    <a href="#">Privacy</a> |
    <a href="#">Terms</a> |
    <a href="#">Contact</a>
  </footer>

</body>
</html>
