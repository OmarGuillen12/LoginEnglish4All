<?php
// Conexión a la base de datos
require_once 'connect.php';

// Obtener cursos del admin
$adminCoursesQuery = "SELECT c.*, u.firstName, u.lastName 
                      FROM curso c 
                      JOIN users u ON c.id_creador = u.id 
                      WHERE u.user_type = 'admin'";
$adminCoursesResult = mysqli_query($conn, $adminCoursesQuery);

// Crear un curso de ejemplo (solo si no hay cursos reales)
$showExample = (mysqli_num_rows($adminCoursesResult) === 0);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f8f9fa;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
            min-height: 100vh;
            padding: 80px 0px 20px;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Navbar styles */
        .navbar {
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            margin-left: 20px;
        }

        .navbar-brand img {
            height: 40px;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            margin-right: 30px;
        }

        .nav-item {
            margin-left: 30px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            padding: 5px 0;
            position: relative;
        }

        .nav-link:hover {
            opacity: 0.8;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .navbar-toggler {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }

        /* Container styles */
        .container {
            background: #fff;
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.4s ease, opacity 0.4s ease;
            margin-bottom: 30px;
            margin-top: 30px;
        }

        .container::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #01b1a7, #ffdc16);
        }

        .welcome-message {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
            background: linear-gradient(90deg, #01b1a7, #ffdc16);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        form {
            margin: 0 1rem;
        }

        .input-group {
            margin-bottom: 1.8rem;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #01b1a7;
            font-size: 1.1rem;
        }

        input {
            color: #333;
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 14px 14px 14px 45px;
            font-size: 15px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #01b1a7;
            box-shadow: 0 0 0 3px rgba(1, 177, 167, 0.15);
        }

        .btn {
            font-size: 1rem;
            padding: 14px 0;
            border-radius: 8px;
            border: none;
            width: 100%;
            background: linear-gradient(90deg, #01b1a7, #ffdc16);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #01978f, #e6c614);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn:hover::before {
            opacity: 1;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(1, 177, 167, 0.3);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .or {
            margin: 1.8rem 0;
            color: #757575;
            position: relative;
            font-weight: 500;
        }

        .or:before,
        .or:after {
            content: "";
            display: inline-block;
            width: 30%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #01b1a7, transparent);
            position: absolute;
            top: 50%;
        }

        .or:before {
            left: 0;
        }

        .or:after {
            right: 0;
        }

        .signup-link {
            margin-top: 1.8rem;
            color: #666;
            font-size: 0.95rem;
        }

        .signup-link button {
            color: #01b1a7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            position: relative;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .signup-link button::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ffdc16;
            transition: width 0.3s;
        }

        .signup-link button:hover {
            color: #01978f;
        }

        .signup-link button:hover::after {
            width: 100%;
        }

        /* Courses Header Section */
        .courses-header {
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            position: relative;
            text-align: center;
            padding: auto;
            justify-content: space-between;
            margin-top: 20px;
            border-radius: 0px;
            padding: 15px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            height: 200px;
        }

        .courses-header h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .courses-header p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Estilos mejorados para las cards de cursos */
        .courses-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .course-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .course-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid #01978f;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .course-image {
            height: 180px;
            overflow: hidden;
            background: linear-gradient(90deg, #01978f, #e6c614);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }

        .course-info {
            padding: 20px;
        }

        .course-info h3 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .course-info p {
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            color: #7f8c8d;
            font-size: 0.9rem;
            padding-top: 10px;
            border-top: 1px solid #ecf0f1;
        }

        .course-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .course-meta i {
            color: #01978f;
        }

        .label {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
            margin-top: 10px;
        }

        .label-admin {
            background-color: #e8f4fc;
            color: #6e48aa;
        }

        .label-popular {
            background-color: #fdedf1;
            color: #e74c3c;
        }

        /* Estilos existentes del playground */
        .playground-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 3rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .playground-card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            text-align: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .playground-card:hover {
            transform: translateY(-10px);
        }

        .playground-img {
            width: 100%;
            height: 150px;
            background-color: #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            overflow: hidden;
            position: relative;
        }

        .playground-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .playground-card:hover .playground-img img {
            transform: scale(1.05);
        }

        .playground-feature-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: #01978f;
        }

        /* Labels */
        .playground-label {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .label-certified {
            background-color: #d4edda;
            color: #155724;
        }

        .label-gluten-free {
            background-color: #fff3cd;
            color: #856404;
        }

        .label-kids {
            background-color: #cce5ff;
            color: #004085;
        }

        .label-natural {
            background-color: #f8d7da;
            color: #721c24;
        }

        .playground-arrow {
            margin-left: 0.3rem;
            font-weight: bold;
            color: #01978f;
            font-size: 1.2rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            border-left: 4px solid #9d50bb;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: #333;
        }

        .modal-body {
            margin-top: 20px;
        }

        .course-description {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .sections-container h3 {
            margin-bottom: 20px;
            color: #6e48aa;
            font-size: 1.5rem;
        }

        .sections-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .section-item {
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 4px solid #2ecc71;
        }

        .section-item:hover {
            border-color: #6e48aa;
            box-shadow: 0 3px 10px rgba(110, 72, 170, 0.1);
        }

        .section-item h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .section-item p {
            color: #7f8c8d;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .quiz-item {
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 4px solid #e67e22;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .navbar-brand {
                margin-left: 0;
            }

            .navbar-nav {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                min-width: unset;
                height: auto;
                background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                border-radius: 0;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
                transition: all 0.5s ease;
                margin-right: 0;
            }

            .navbar-nav.active {
                left: 0;
            }

            .nav-item {
                margin: 12px 0;
            }

            .navbar-toggler {
                display: block;
            }

            .courses-header {
                padding: 40px 15px;
                margin-top: 15px;
            }

            .courses-header h1 {
                font-size: 2rem;
            }

            .courses-header p {
                font-size: 1rem;
            }

            .course-cards {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 90%;
                margin: 10% auto;
                padding: 20px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-animation {
            animation: fadeIn 0.5s ease-out;
        }

        /* Error messages */
        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            text-align: left;
            margin-top: 5px;
            display: none;
        }

        /* ===== FOOTER ===== */
  .minimal-footer {
    background-color: #fff;
    border-top: 3px solid #000;
    padding: 2rem 1rem;
    margin-top: 2rem;
  }

  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
  }

  .footer-logo-img {
    height: 40px;
    width: auto;
  }

  .footer-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1.5rem;
  }

  .footer-link {
    color: #000;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
  }

  .footer-link:hover {
    color: #01978f;
  }

  .footer-copyright {
    color: #666;
    font-size: 0.9rem;
    text-align: center;
  }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="#" class="navbar-brand">
            <img src="assets/logo.png" alt="Logo">
        </a>

        <button class="navbar-toggler" id="navbarToggler">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="navbar-nav" id="navbarNav">
            <li class="nav-item">
                <a href="#courses" class="nav-link">Courses</a>
            </li>
            <li class="nav-item">
                <a href="#content" class="nav-link">Games</a>
            </li>
        </ul>
    </nav>

    <div class="main-content" id="main">
        <div class="container" id="signup" style="display:none;">
            <h1 class="welcome-message">Create Student Account</h1>
            <p class="subtitle">Register to access your courses</p>
            <form method="post" action="register.php" id="registerForm">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="fName" id="fName" placeholder="First Name" required>
                    <div class="error-message" id="fNameError">Please enter a valid first name</div>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="lName" id="lName" placeholder="Last Name" required>
                    <div class="error-message" id="lNameError">Please enter a valid last name</div>
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="regEmail" placeholder="Email" required>
                    <div class="error-message" id="regEmailError">Please enter a valid email address</div>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="regPassword" placeholder="Password" required>
                    <div class="error-message" id="regPasswordError">Password must be at least 8 characters</div>
                </div>

                <!-- Campo fijo para semestre (siempre visible) -->
                <div class="input-group select-group">
                    <i class="fas fa-graduation-cap"></i>
                    <select name="semestre" id="semestre" required style="
            color: #333;
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 14px 14px 14px 45px;
            font-size: 15px;
            transition: all 0.3s;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml,%3Csvg fill=\'%2301b1a7\' height=\'20\' viewBox=\'0 0 24 24\' width=\'20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M7 10l5 5 5-5z\'/%3E%3C/svg%3E');
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 16px;
        ">
                        <option value="" disabled selected>Select your semester</option>
                        <option value="1er semestre">1st Semester</option>
                        <option value="2do semestre">2nd Semester</option>
                        <option value="3er semestre">3rd Semester</option>
                        <option value="4to semestre">4th Semester</option>
                        <option value="5to semestre">5th Semester</option>
                        <option value="6to semestre">6th Semester</option>
                    </select>
                    <div class="error-message" id="semestreError">Please select your semester</div>
                </div>

                <input type="hidden" name="user_type" value="student">
                <input type="submit" class="btn" value="Sign Up" name="signUp">
            </form>
            <div class="or">OR</div>
            <p class="signup-link">Already have an account? <button id="signInButton">Sign In</button></p>
        </div>

        <div class="container" id="signIn">
            <h1 class="welcome-message">Login to Your Account</h1>
            <p class="subtitle">The faster you login. The faster we get to work.</p>
            <form method="post" action="register.php" id="loginForm">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="loginEmail" placeholder="Email" required>
                    <div class="error-message" id="loginEmailError">Please enter a valid email address</div>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <div class="error-message" id="loginPasswordError">Password is required</div>
                </div>
                <input type="submit" class="btn" value="Login" name="signIn">
            </form>
            <div class="or">OR</div>
            <p class="signup-link">Don't have an account? <button id="signUpButton">Sign Up</button></p>
        </div>

        <!-- New Courses Header Section -->

    </div>

    <!-- New Courses Header Section - AHORA FUERA DEL MAIN-CONTENT -->
    <div class="courses-header" id="courses">
        <div class="courses-header-content">
            <h1>Courses</h1>
            <p>The more you study, the more you learn; and the more you learn, the more doors you open</p>
        </div>
    </div>

    <!-- Courses Container with Dynamic Cards -->
    <div class="courses-container" id="courses-container">
        <div class="course-cards">
            <?php if(!$showExample): ?>
                <?php while($course = mysqli_fetch_assoc($adminCoursesResult)): 
                    $sectionsQuery = "SELECT * FROM seccion WHERE id_curso = {$course['id_curso']} ORDER BY orden";
                    $sectionsResult = mysqli_query($conn, $sectionsQuery);
                    $sectionsCount = mysqli_num_rows($sectionsResult);
                ?>
                    <div class="course-card" onclick="openCourseModal(<?= $course['id_curso'] ?>, false)">
                        <div class="course-image">
                            <?= substr($course['titulo'], 0, 1) ?>
                        </div>
                        <div class="course-info">
                            <h3><?= htmlspecialchars($course['titulo']) ?></h3>
                            <p><?= htmlspecialchars($course['descripcion']) ?></p>
                            <div class="course-meta">
                                <span><i class="fas fa-book-open"></i> <?= $sectionsCount ?> Sections</span>
                                <span><i class="fas fa-user-tie"></i> <?= htmlspecialchars($course['firstName'] . ' ' . $course['lastName']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            
        </div>
    </div>

    <!-- Course Modal -->
    <div id="courseModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modalCourseTitle">Course Title</h2>
            <div class="modal-body">
                <div class="course-description">
                    <p id="modalCourseDescription">Course description will appear here...</p>
                    <p id="modalCourseExample">Course description will appear here...</p>
                </div>
                <div class="sections-container">
                    <h3>Sections</h3>
                    <div class="sections-list" id="sectionsList">
                        <!-- Las secciones se cargarán aquí dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="courses-header" id="content">
        <div class="courses-header-content">
            <h1>Games</h1>
            <p>Improve your English skills and build confidence by practicing consistently every day</p>
        </div>
    </div>

    <section class="playground-features">
        <div class="playground-card" onclick="location.href='chatbot.html'">
            <div class="playground-img">
                <img src="assets/writing.jpg" alt="Writing Practice">
            </div>
            <h3 class="playground-feature-title">Writing → Escritura</h3>
            <span class="playground-label label-certified">Play</span>
            <span class="playground-arrow">→</span>
        </div>

        <div class="playground-card" onclick="location.href='audio.html'">
            <div class="playground-img">
                <img src="assets/listening.jpg" alt="Listening Practice">
            </div>
            <h3 class="playground-feature-title">Listening → Escucha</h3>
            <span class="playground-label label-gluten-free">Play</span>
            <span class="playground-arrow">→</span>
        </div>

        <div class="playground-card" onclick="location.href='pronunciation.html'">
            <div class="playground-img">
                <img src="assets/speaking.jpg" alt="Speaking Practice">
            </div>
            <h3 class="playground-feature-title">Speaking → Habla</h3>
            <span class="playground-label label-kids">Play</span>
            <span class="playground-arrow">→</span>
        </div>

    </section>


    <script src="script.js"></script>
    <script>
        // Navbar toggle functionality
        document.getElementById('navbarToggler').addEventListener('click', function () {
            document.getElementById('navbarNav').classList.toggle('active');
        });
        

        // Datos del curso de ejemplo
        const exampleCourse = {
            titulo: "Example Course",
            descripcion: "This is a sample course to demonstrate how the platform works. Real courses will contain actual learning materials and sections.",
            sections: [
                {
                    titulo: "Section 1: Getting Started",
                    definicion: "This is an example section showing how course content would be organized."
                },
                {
                    titulo: "Section 2: Core Concepts",
                    definicion: "Example content demonstrating the structure of a typical learning module."
                },
                {
                    titulo: "Section 3: Practical Application",
                    definicion: "Sample section showing how practical exercises might be presented."
                }
            ]
        };

        function openCourseModal(courseId, isExample) {
            if(isExample) {
                // Mostrar el curso de ejemplo
                document.getElementById('modalCourseTitle').textContent = exampleCourse.titulo;
                document.getElementById('modalCourseDescription').textContent = exampleCourse.descripcion;
                
                const sectionsList = document.getElementById('sectionsList');
                sectionsList.innerHTML = '';
                
                exampleCourse.sections.forEach(section => {
                    const sectionItem = document.createElement('div');
                    sectionItem.className = 'section-item';
                    sectionItem.innerHTML = `
                        <h4>${section.titulo}</h4>
                        <p>${section.definicion}</p>
                        <p>${section.ejemplo}</p>
                    `;
                    sectionsList.appendChild(sectionItem);
                });
                
                document.getElementById('courseModal').style.display = 'block';
                document.body.style.overflow = 'hidden';
            } else {
                // Hacer petición AJAX para cursos reales
                fetch(`get_course_data.php?course_id=${courseId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modalCourseTitle').textContent = data.course.titulo;
                        document.getElementById('modalCourseDescription').textContent = data.course.descripcion;
                        document.getElementById('modalCourseExample').textContent = data.course.ejemplo;
                        
                        const sectionsList = document.getElementById('sectionsList');
                        sectionsList.innerHTML = '';
                        
                        if(data.sections.length > 0) {
                            data.sections.forEach(section => {
                                const sectionItem = document.createElement('div');
                                sectionItem.className = 'section-item';
                                sectionItem.innerHTML = `
                                    <h4>${section.titulo}</h4>
                                    <p>${section.definicion || 'No hay descripción disponible'}</p>
                                    <p>${section.ejemplo || 'No hay descripción disponible'}</p>
                                `;
                                sectionsList.appendChild(sectionItem);
                            });
                        } else {
                            sectionsList.innerHTML = '<p>Este curso no tiene secciones aún.</p>';
                        }
                        
                        document.getElementById('courseModal').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del curso');
                    });
            }
        }

        function closeModal() {
            document.getElementById('courseModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById('courseModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        
    </script>
</body>

<footer class="minimal-footer">
    <div class="footer-content">
      <div class="footer-logo">
        <img src="assets/logo.png" alt="Logo" class="footer-logo-img" />
      </div>
      <div class="footer-links">
        <a href="#main" class="footer-link">Login</a>
        <a href="#courses" class="footer-link">Courses</a>
        <a href="#content" class="footer-link">Content</a>
      </div>
      <div class="footer-copyright">
        © 2025 Englidh4All. All rights reserved.
      </div>
    </div>
  </footer>

</html>