<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Upload avatar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["avatar"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (in_array($imageFileType, $allowed)) {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->bind_param("si", $targetFile, $user_id);
            $stmt->execute();
        }
    }
}

// ✅ Delete avatar
if (isset($_POST['delete_avatar'])) {
    $stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!empty($row['avatar']) && file_exists($row['avatar'])) {
        unlink($row['avatar']);
    }
    $empty = NULL;
    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $empty, $user_id);
    $stmt->execute();
}

// ✅ Choose predefined avatar
if (isset($_POST['predefined_avatar'])) {
    $predefined = $_POST['predefined_avatar'];
    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $predefined, $user_id);
    $stmt->execute();
}

// user info nikalna
$stmt = $conn->prepare("SELECT username, email, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile - FlipMart+</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #0f172a;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      color: #f1f5f9;
    }
    .profile-card {
      max-width: 900px;
      margin: 60px auto;
      padding: 2.5rem;
      background: rgba(17, 24, 39, 0.9);
      border-radius: 20px;
      box-shadow: 0 12px 40px rgba(0,0,0,0.6);
      backdrop-filter: blur(20px);
      text-align: center;
    }
    .profile-avatar img, .default-avatar {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #6366f1;
      box-shadow: 0 0 20px #6366f1aa;
      transition: 0.3s;
    }
    .profile-avatar img:hover {
      transform: scale(1.05);
      box-shadow: 0 0 28px #818cf8;
    }
    .default-avatar {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.2rem;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(135deg, #3b82f6, #6366f1);
    }
    .profile-avatar {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 1rem;
}

.profile-avatar img,
.default-avatar {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #6366f1;
  box-shadow: 0 0 20px #6366f1aa;
  transition: 0.3s;
}

/* hover sirf image pe ho */
.profile-avatar img:hover {
  transform: scale(1.05);
  box-shadow: 0 0 28px #818cf8;
}

.default-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3.2rem;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #3b82f6, #6366f1);
}

    h1 {
      margin-top: 1.5rem;
      font-size: 2rem;
      color: #e2e8f0;
    }
    .upload-section {
      margin-top: 2rem;
    }
    /* Custom upload button */
    .upload-btn {
      display: inline-block;
      padding: 0.8rem 1.6rem;
      border-radius: 10px;
      background: linear-gradient(90deg, #6366f1, #3b82f6);
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 4px 15px rgba(99,102,241,0.4);
      margin-top: 1rem;
    }
    .upload-btn:hover { transform: scale(1.05); }
    .btn {
      padding: 0.7rem 1.5rem;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      transition: 0.2s;
      margin-top: 0.8rem;
    }
    .btn-danger { background: linear-gradient(90deg,#ef4444,#dc2626); color: white; }
    .btn-danger:hover { opacity: 0.9; }
    .predefined-avatars {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }
    .predefined-avatars button {
      background: none;
      border: 2px solid transparent;
      padding: 0;
      border-radius: 50%;
      cursor: pointer;
      transition: 0.2s;
    }
    .predefined-avatars img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 0 12px rgba(255,255,255,0.1);
    }
    .predefined-avatars button:hover {
      border: 2px solid #6366f1;
      transform: scale(1.1);
    }
    .info-group {
      margin: 1.2rem 0;
      text-align: left;
    }
    .info-label {
      font-weight: 600;
      color: #94a3b8;
      font-size: 0.9rem;
    }
    .info-value {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.15);
      padding: 0.9rem;
      border-radius: 10px;
      color: #f8fafc;
      font-weight: 500;
    }
    .nav-links {
      margin-top: 2.5rem;
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .nav-links a {
      text-decoration: none;
      padding: 0.7rem 1.3rem;
      border-radius: 10px;
      font-weight: 600;
      background: rgba(255,255,255,0.08);
      color: #f1f5f9;
      transition: 0.2s;
    }
    .nav-links a:hover {
      background: rgba(255,255,255,0.2);
    }
  </style>
</head>
<body>
  <div class="profile-card">
    <div class="profile-avatar">
      <?php if (!empty($user['avatar'])): ?>
          <img id="avatarPreview" src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
      <?php else: ?>
          <div id="avatarPreview" class="default-avatar">
              <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
          </div>
      <?php endif; ?>
    </div>
    <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>

    <!-- Upload form -->
    <div class="upload-section">
      <form method="POST" enctype="multipart/form-data">
        <!-- Hidden input -->
        <input type="file" id="avatarInput" name="avatar" accept="image/*" hidden>
        <!-- Fancy button -->
        <label for="avatarInput" class="upload-btn">📸 Upload Photo</label>
        <!-- Save button -->
        <br>
        <button type="submit" class="btn btn-primary">Save</button>
      </form>

      <?php if (!empty($user['avatar'])): ?>
      <form method="POST">
        <button type="submit" name="delete_avatar" class="btn btn-danger">Delete Avatar</button>
      </form>
      <?php endif; ?>
    </div>

    <!-- Predefined Avatars -->
    <!-- <h3 style="margin-top:2rem;color:#cbd5e1;">Or Choose a Predefined Avatar</h3>
    <div class="predefined-avatars">
      <?php 
        $predefinedAvatars = [
          "assets/avatars/avatar1.png",
          "assets/avatars/avatar2.png",
          "assets/avatars/avatar3.png",
          "assets/avatars/avatar4.png"
        ];
        foreach($predefinedAvatars as $img): ?>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="predefined_avatar" value="<?php echo $img; ?>">
            <button type="submit">
              <img src="<?php echo $img; ?>" alt="Avatar Option">
            </button>
          </form>
      <?php endforeach; ?>
    </div> -->

    <!-- User Info -->
    <div class="profile-info" style="margin-top:2rem;">
      <div class="info-group">
        <div class="info-label">Username</div>
        <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
      </div>
      <div class="info-group">
        <div class="info-label">Email</div>
        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
      </div>
      <div class="info-group">
        <div class="info-label">Member Since</div>
        <div class="info-value"><?php echo date('F j, Y'); ?></div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="nav-links">
      <a href="main.php">🏠 Home</a>
      <a href="main.php">🛒 Continue Shopping</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>

  <!-- JS for live preview -->
  <script>
    const avatarInput = document.getElementById("avatarInput");
    const avatarPreview = document.getElementById("avatarPreview");

    if (avatarInput) {
      avatarInput.addEventListener("change", function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            if (avatarPreview.tagName === "IMG") {
              avatarPreview.src = e.target.result;
            } else {
              avatarPreview.innerHTML = "";
              avatarPreview.style.background = `url('${e.target.result}') center/cover`;
            }
          }
          reader.readAsDataURL(file);
        }
      });
    }
  </script>
</body>
</html>
