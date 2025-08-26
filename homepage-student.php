<?php
session_start();
include('connect.php');

// Verificación de sesión y tipo de usuario
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'student') {
    header("Location: index.php");
    exit();
}

// Obtener información completa del usuario con manejo de errores
$userData = [
    'firstName' => '',
    'lastName' => '',
    'email' => '',
    'semestre' => 'No especificado'
];


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Estudiante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            padding: 20px 0;
            height: 100vh;
            position: fixed;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(-250px);
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .sidebar-header h3 {
            color: #fff;
            font-size: 1.3rem;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .sidebar-menu {
            padding: 20px 0;
            overflow-y: auto;
            max-height: calc(100vh - 100px);
        }
        .menu-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            margin: 5px 10px;
            border-radius: 6px;
        }
        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        .menu-item.active {
            background-color: #ff7e5f;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateX(5px);
        }
        .menu-item i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            justify-content: center;
            align-items: center;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .main-content {
            flex: 1;
            margin-left: 0;
            padding: 80px 20px 30px;
            transition: all 0.3s ease;
        }
        .sidebar.active + .main-content {
            margin-left: 250px;
        }

        /* Estilos para el footer del sidebar */
.sidebar-footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 15px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    display: flex;
    align-items: center;
    padding: 12px 25px;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    margin: 5px 10px;
    border-radius: 6px;
}

.logout-btn:hover {
    background-color: rgba(255, 255, 255, 0.15);
    transform: translateX(5px);
}

.logout-btn i {
    margin-right: 12px;
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
}

        .content-header {
            margin-bottom: 30px;
        }
        .content-header h1 {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
        }
        .dashboard-content {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 150px);
            transition: all 0.3s ease;
        }
        .profile-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #9d50bb;
        }
        .profile-info p {
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        .profile-info strong {
            color: #2c3e50;
        }
        .dashboard-list {
            margin-top: 20px;
        }
        .list-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .list-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .list-item h3 {
            color: #2c3e50;
            margin-bottom: 8px;
        }
        .list-item p {
            color: #666;
            margin-bottom: 5px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.9em;
            color: #888;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }
        .btn-join-course {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }
        .already-joined {
            color: #27ae60;
            font-weight: bold;
            margin-top: 10px;
        }
        .loading-message, .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .empty-message {
            font-style: italic;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin: 15px 0;
            font-weight: 500;
        }
        .hidden {
            display: none;
        }
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 250px;
                padding: 30px;
            }
            .mobile-menu-toggle {
                display: none;
            }
            .sidebar-overlay {
                display: none;
            }
        }
        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: flex;
            }
            .sidebar.active + .main-content {
                margin-left: 0;
            }
        }
        @media (max-width: 768px) {
            .content-header h1 {
                font-size: 1.5rem;
            }
        }
        @media (max-width: 480px) {
            .main-content {
                padding: 80px 15px 20px;
            }
            .dashboard-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Botón de menú móvil -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars" id="menuIcon"></i>
    </button>

    <!-- Overlay para fondo difuminado -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar con gradiente -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Panel del Estudiante</h3>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" onclick="changeContent('profile')">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </div>
            <div class="menu-item" onclick="changeContent('all-courses')">
                <i class="fas fa-book"></i>
                <span>Cursos</span>
            </div>
            <div class="menu-item" onclick="changeContent('my-courses')">
                <i class="fas fa-graduation-cap"></i>
                <span>Mis Cursos</span>
            </div>
            <div class="menu-item" onclick="changeContent('my-quizzes')">
                <i class="fas fa-list-check"></i>
                <span>Mis Exámenes</span>
            </div>
        </div>
        <!-- Botón para cerrar sesión -->
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1 id="content-title">Perfil</h1>
        </div>
        <div class="dashboard-content">
            <!-- Contenido de Perfil - Solo información del usuario -->
            <div id="profile-content" class="content-section">
                <h2>Información Personal</h2>
                <div class="profile-info">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($userData['firstName'] . ' ' . $userData['lastName']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>Semestre:</strong> <?php echo htmlspecialchars($userData['semestre']); ?></p>
                </div>
            </div>

            <!-- Contenido de Cursos - Solo cursos disponibles -->
            <div id="all-courses-content" class="hidden content-section">
                <h2>Cursos Disponibles</h2>
                <div id="all-courses-list" class="dashboard-list">
                    <div class="loading-message">Cargando cursos disponibles...</div>
                </div>
            </div>

            <!-- Contenido de Mis Cursos - Solo cursos del usuario -->
            <div id="my-courses-content" class="hidden content-section">
                <h2>Mis Cursos</h2>
                <div id="courses-list" class="dashboard-list">
                    <div class="loading-message">Cargando mis cursos...</div>
                </div>
            </div>

            <!-- Contenido de Mis Exámenes - Solo exámenes disponibles para el usuario -->
            <div id="my-quizzes-content" class="hidden content-section">
                <h2>Mis Exámenes</h2>
                <div id="quizzes-list" class="dashboard-list">
                    <div class="loading-message">Cargando mis exámenes...</div>
                </div>

            </div>
        </div>
    </div>

    <script>
    // ==============================================
    // FUNCIONES AUXILIARES
    // ==============================================
    
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    function getStatusBadge(status) {
        const statusMap = {
            'borrador': '<span style="color: #f39c12;">Borrador</span>',
            'activo': '<span style="color: #27ae60;">Activo</span>',
            'inactivo': '<span style="color: #95a5a6;">Inactivo</span>'
        };
        return statusMap[status] || status;
    }

    // ==============================================
    // FUNCIONES PARA CARGAR DATOS ESPECÍFICOS
    // ==============================================

    async function loadAllCourses() {
        const coursesList = document.getElementById('all-courses-list');
        coursesList.innerHTML = '<div class="loading-message">Cargando cursos disponibles...</div>';
        
        try {
            const response = await fetch('get-all-courses.php');
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.length === 0) {
                coursesList.innerHTML = '<div class="empty-message">No hay cursos disponibles actualmente</div>';
                return;
            }
            
            let html = '';
            data.forEach(course => {
                if (course.estado === 'activo') {
                    html += `
                    <div class="list-item" data-id="${course.id_curso}">
                        <h3>${course.titulo}</h3>
                        <p>${course.descripcion}</p>
                        <div class="meta">
                            <span>Profesor: ${course.creador}</span>
                            <span>Secciones: ${course.sections_count}</span>
                        </div>
                        ${course.ya_matriculado ? 
                            '<div class="already-joined">Ya estás matriculado</div>' : 
                            '<button class="btn btn-join-course" onclick="joinCourse('+course.id_curso+')">'+
                            '<i class="fas fa-plus-circle"></i> Unirse al Curso</button>'}
                    </div>
                    `;
                }
            });
            
            coursesList.innerHTML = html || '<div class="empty-message">No hay cursos disponibles actualmente</div>';
            
        } catch (error) {
            console.error('Error al cargar cursos:', error);
            coursesList.innerHTML = `
                <div class="error-message">
                    Error al cargar los cursos<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

    async function loadUserQuizzes() {
        const quizzesList = document.getElementById('quizzes-list');
        quizzesList.innerHTML = '<div class="loading-message">Cargando exámenes...</div>';
        
        try {
            const response = await fetch('get-student-quizzes.php');
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            const availableQuizzes = data.filter(quiz => !quiz.completado);
            
            if (availableQuizzes.length === 0) {
                quizzesList.innerHTML = '<div class="empty-message">No tienes exámenes pendientes</div>';
                return;
            }
            
            let html = '';
            availableQuizzes.forEach(quiz => {
                html += `
                <div class="list-item" data-id="${quiz.id}">
                    <h3>${quiz.title}</h3>
                    <p>${quiz.questions_count} preguntas</p>
                    <div class="meta">
                        <span>Profesor: ${quiz.teacher}</span>
                        <span>Estado: Pendiente</span>
                    </div>
                    <button class="btn btn-start-quiz" onclick="startQuiz(${quiz.id})">
                        <i class="fas fa-play"></i> Comenzar Examen
                    </button>
                </div>
                `;
            });
            
            quizzesList.innerHTML = html;
            
        } catch (error) {
            console.error('Error al cargar quizzes:', error);
            quizzesList.innerHTML = `
                <div class="error-message">
                    Error al cargar los exámenes<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

    async function loadUserCourses() {
        const coursesList = document.getElementById('courses-list');
        coursesList.innerHTML = '<div class="loading-message">Cargando mis cursos...</div>';
        
        try {
            const response = await fetch('get-student-courses.php');
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.length === 0) {
                coursesList.innerHTML = '<div class="empty-message">No estás inscrito en ningún curso</div>';
                return;
            }
            
            let html = '';
            data.forEach(course => {
                html += `
                <div class="list-item" data-id="${course.id_curso}">
                    <h3>${course.titulo}</h3>
                    <p>${course.descripcion}</p>
                    <div class="meta">
                        <span>Profesor: ${course.creador}</span>
                        <span>Secciones: ${course.sections_count}</span>
                    </div>
                    <button class="btn" onclick="viewCourse(${course.id_curso})">
                        <i class="fas fa-book-open"></i> Ver Curso
                    </button>
                </div>
                `;
            });
            
            coursesList.innerHTML = html;
            
        } catch (error) {
            console.error('Error al cargar cursos:', error);
            coursesList.innerHTML = `
                <div class="error-message">
                    Error al cargar los cursos<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

    // ==============================================
    // FUNCIONES PARA ACCIONES DE ESTUDIANTE
    // ==============================================

    function joinCourse(courseId) {
        fetch('join-course.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ courseId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Te has unido al curso exitosamente');
                loadAllCourses();
                loadUserCourses();
            } else {
                alert('Error: ' + (data.message || 'No se pudo unir al curso'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        });
    }

    function startQuiz(quizId) {
        window.location.href = `take-quiz.php?id=${quizId}`;
    }

    function viewCourse(courseId) {
        window.location.href = `view-course.php?id=${courseId}`;
    }

    // ==============================================
    // FUNCIÓN PRINCIPAL PARA CAMBIAR CONTENIDO
    // ==============================================

    function changeContent(section) {
        // Ocultar todos los contenidos
        document.getElementById('profile-content').classList.add('hidden');
        document.getElementById('all-courses-content').classList.add('hidden');
        document.getElementById('my-quizzes-content').classList.add('hidden');
        document.getElementById('my-courses-content').classList.add('hidden');
        
        // Mostrar el contenido seleccionado
        const contentSection = document.getElementById(`${section}-content`);
        contentSection.classList.remove('hidden');
        
        // Actualizar el título
        let title = '';
        switch(section) {
            case 'profile':
                title = 'Perfil';
                break;
            case 'all-courses':
                title = 'Cursos Disponibles';
                loadAllCourses();
                break;
            case 'my-quizzes':
                title = 'Mis Exámenes';
                loadUserQuizzes();
                break;
            case 'my-courses':
                title = 'Mis Cursos';
                loadUserCourses();
                break;
        }
        document.getElementById('content-title').textContent = title;
        
        // Actualizar el item activo en el sidebar
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        // Cerrar el sidebar en móviles
        if (window.innerWidth < 992) {
            toggleSidebar();
        }
    }

    // ==============================================
    // FUNCIONES DEL SIDEBAR
    // ==============================================

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    function setupClickOutside() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        overlay.addEventListener('click', function() {
            if (sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        });
    }

    // ==============================================
    // INICIALIZACIÓN AL CARGAR LA PÁGINA
    // ==============================================

    document.addEventListener('DOMContentLoaded', function() {
        setupClickOutside();
        
        // Event listener para el botón de menú móvil
        document.getElementById('mobileMenuToggle').addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    });
    </script>
</body>
</html>