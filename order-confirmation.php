<?php
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
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
    <title>Order Confirmation - FlipMart+</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #ece9f7 0%, #b7caff 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
        }
        #three-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .confirmation-container {
            position: relative;
            z-index: 2;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .confirmation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(60, 60, 120, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .confirmation-title {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3a4b;
            margin-bottom: 1rem;
        }
        .confirmation-message {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .order-details {
            background: rgba(247, 249, 252, 0.8);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .order-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        .order-detail:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .nav-btn {
            color: #3a6ee8;
            text-decoration: none;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            background: rgba(91, 141, 239, 0.1);
            transition: all 0.2s;
        }
        .nav-btn:hover {
            background: rgba(91, 141, 239, 0.2);
            transform: translateY(-1px);
        }
        .nav-btn.primary {
            background: linear-gradient(90deg, #5b8def 0%, #3a6ee8 100%);
            color: white;
        }
        .nav-btn.primary:hover {
            background: linear-gradient(90deg, #3a6ee8 0%, #5b8def 100%);
        }
        .order-number {
            background: linear-gradient(135deg, #5b8def, #3a6ee8);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <canvas id="three-canvas"></canvas>
    
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="success-icon">✓</div>
            <h1 class="confirmation-title">Order Confirmed!</h1>
            <p class="confirmation-message">
                Thank you for your purchase, <?php echo htmlspecialchars($user['username']); ?>! 
                Your order has been successfully placed and your cart has been cleared.
            </p>
            
            <div class="order-number">
                Order #<?php echo strtoupper(substr(md5(time() . $user_id), 0, 8)); ?>
            </div>
            
            <div class="order-details">
                <div class="order-detail">
                    <span>Order Date:</span>
                    <span><?php echo date('F j, Y'); ?></span>
                </div>
                <div class="order-detail">
                    <span>Customer:</span>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="order-detail">
                    <span>Email:</span>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="order-detail">
                    <span>Status:</span>
                    <span style="color: #10b981;">Confirmed</span>
                </div>
            </div>
            
            <div class="nav-links">
                <a href="main.php" class="nav-btn primary">Continue Shopping</a>
                <a href="profile.php" class="nav-btn">View Profile</a>
                <a href="main.php" class="nav-btn">Back to Home</a>
            </div>
        </div>
    </div>

    <script src="common.js"></script>
    <script type="module">
        import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.module.js';

        const canvas = document.getElementById("three-canvas");
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({canvas, alpha: true});
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0x000000, 0);

        // Create celebration particles
        const particlesGeometry = new THREE.BufferGeometry();
        const particlesVertices = [];
        const particlesColors = [];
        const color = new THREE.Color();
        
        for (let i = 0; i < 200; i++) {
            particlesVertices.push((Math.random() - 0.5) * 1000);
            particlesVertices.push((Math.random() - 0.5) * 800);
            particlesVertices.push((Math.random() - 0.5) * 500);
            
            // Use celebration colors (gold, green, blue, purple)
            const celebrationColors = [0xffd700, 0x10b981, 0x3b82f6, 0x8b5cf6, 0xef4444];
            color.setHex(celebrationColors[Math.floor(Math.random() * celebrationColors.length)]);
            particlesColors.push(color.r, color.g, color.b);
        }
        
        particlesGeometry.setAttribute('position', new THREE.Float32BufferAttribute(particlesVertices, 3));
        particlesGeometry.setAttribute('color', new THREE.Float32BufferAttribute(particlesColors, 3));

        const particlesMaterial = new THREE.PointsMaterial({
            size: 3,
            vertexColors: true,
            transparent: true,
            opacity: 0.8
        });

        const particles = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particles);

        // Create floating checkmarks
        const checkmarks = [];
        for (let i = 0; i < 5; i++) {
            const geometry = new THREE.PlaneGeometry(20, 20);
            const material = new THREE.MeshBasicMaterial({
                color: 0x10b981,
                transparent: true,
                opacity: 0.3
            });
            
            const checkmark = new THREE.Mesh(geometry, material);
            checkmark.position.set(
                (Math.random() - 0.5) * 800,
                (Math.random() - 0.5) * 600,
                (Math.random() - 0.5) * 300
            );
            
            checkmarks.push(checkmark);
            scene.add(checkmark);
        }
        
        camera.position.z = 600;

        function animate() {
            requestAnimationFrame(animate);
            
            // Rotate particles
            particles.rotation.x += 0.001;
            particles.rotation.y += 0.002;
            
            // Animate checkmarks
            checkmarks.forEach((checkmark, index) => {
                checkmark.rotation.z += 0.01 + index * 0.002;
                checkmark.position.y += Math.sin(Date.now() * 0.001 + index) * 0.5;
                checkmark.position.x += Math.cos(Date.now() * 0.001 + index) * 0.3;
            });
            
            renderer.render(scene, camera);
        }
        animate();

        // Responsive resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
