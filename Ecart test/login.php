<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            header("Location: main.php");
            exit();
        } else {
            echo "<div class='error-message'>Invalid password!</div>";
        }
    } else {
        echo "<div class='error-message'>No user found!</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - FlipMart+</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #ece9f7 0%, #b7caff 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
            overflow: hidden;
        }
        #three-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(60, 60, 120, 0.15);
            min-width: 340px;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h2 {
            text-align: center;
            color: #2d3a4b;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        label {
            font-weight: 500;
            color: #3a4668;
            font-size: 0.9rem;
        }
        input[type="email"], input[type="password"] {
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            background: rgba(247, 249, 252, 0.8);
            transition: all 0.2s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border: 1.5px solid #5b8def;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(91, 141, 239, 0.1);
        }
        button[type="submit"] {
            background: linear-gradient(90deg, #5b8def 0%, #3a6ee8 100%);
            color: #fff;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #3a6ee8 0%, #5b8def 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(91, 141, 239, 0.3);
        }
        p {
            text-align: center;
            margin-top: 1.2rem;
            color: #6b7280;
        }
        a {
            color: #3a6ee8;
            text-decoration: none;
            font-weight: 500;
        }
        a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #e74c3c;
            background: #fbeaea;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .nav-links {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 3;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            transition: all 0.2s;
        }
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <canvas id="three-canvas"></canvas>
    
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </div>

    <div class="login-container">
        <h2>Welcome to FlipMart+</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
        <p><a href="index.php">← Back to Home</a></p>
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

        // Create floating particles
        const geometry = new THREE.BufferGeometry();
        const vertices = [];
        const colors = [];
        const color = new THREE.Color();
        
        for (let i = 0; i < 500; i++) {
            vertices.push((Math.random() - 0.5) * 1000);
            vertices.push((Math.random() - 0.5) * 1000);
            vertices.push((Math.random() - 0.5) * 1000);
            
            const palette = [0xffffff, 0x9f7aea, 0x6366f1, 0xf472b6, 0x60a5fa];
            color.setHex(palette[Math.floor(Math.random() * palette.length)]);
            colors.push(color.r, color.g, color.b);
        }
        
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
        geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));

        const material = new THREE.PointsMaterial({
            size: 2,
            vertexColors: true,
            transparent: true,
            opacity: 0.8
        });

        const particles = new THREE.Points(geometry, material);
        scene.add(particles);
        camera.position.z = 500;

        function animate() {
            requestAnimationFrame(animate);
            particles.rotation.x += 0.0005;
            particles.rotation.y += 0.0008;
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