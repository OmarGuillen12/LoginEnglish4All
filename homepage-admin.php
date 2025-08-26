<?php
session_start();
include('connect.php');

// Verificación de sesión y tipo de usuario
if (!isset($_SESSION['user_type'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['user_type'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Administrador</title>
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

/* Sidebar Styles */
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

/* Mobile Toggle Button */
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

/* Overlay para fondo difuminado */
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

/* Main Content */
.main-content {
    flex: 1;
    margin-left: 0;
    padding: 80px 20px 30px;
    transition: all 0.3s ease;
}

.sidebar.active + .main-content {
    margin-left: 250px;
}

.content-header {
    margin-bottom: 30px;
}

.content-header h1 {
    color: #6e48aa;
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

/* Estilos específicos para el formulario de curso */
.quiz-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
}

input[type="text"], input[type="email"], input[type="password"], textarea, select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
    transition: all 0.3s;
}

input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, textarea:focus, select:focus {
    border-color: #9d50bb;
    box-shadow: 0 0 0 3px rgba(157, 80, 187, 0.2);
    outline: none;
}

.question-container {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid #9d50bb;
    transition: all 0.3s;
}

.question-container:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.btn {
    background: linear-gradient(135deg, #6e48aa 0%, #9d50bb 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
    transition: all 0.3s;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(110, 72, 170, 0.3);
}

.btn:active {
    transform: translateY(0);
}

.btn-danger {
    background: linear-gradient(135deg, #ff5e62 0%, #ff9966 100%);
}

.btn-danger:hover {
    box-shadow: 0 4px 8px rgba(255, 94, 98, 0.3);
}

#sections-container {
    margin-top: 25px;
}

#course-form, #create-user-form {
    margin-bottom: 40px;
}

.hidden {
    display: none;
}

.success-message {
    color: #27ae60;
    text-align: center;
    margin: 20px 0;
    font-weight: bold;
    font-size: 18px;
}

.error-message {
    color: #ff5e62;
    text-align: center;
    margin: 15px 0;
    font-weight: 500;
}

/* Estilos para listas de cursos */
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
    position: relative;
}

.list-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.list-item h3 {
    color: #6e48aa;
    margin-bottom: 8px;
}

.list-item p {
    color: #666;
    margin-bottom: 5px;
}

.list-item .meta {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 0.9em;
    color: #888;
}

.loading-message {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

.empty-message {
    text-align: center;
    padding: 20px;
    color: #666;
}

/* Estilos para otras secciones */
#profile-content, #course-content, #my-courses-content, #create-user-content {
    max-width: 800px;
    margin: 0 auto;
}

/* Efecto de transición suave para el contenido */
.content-section {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Estilos para los botones de acción */
.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 10px;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s;
    padding: 5px;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edit-btn {
    color: #3498db;
    background-color: rgba(52, 152, 219, 0.1);
}

.edit-btn:hover {
    background-color: rgba(52, 152, 219, 0.2);
}

.delete-btn {
    color: #e74c3c;
    background-color: rgba(231, 76, 60, 0.1);
}

.delete-btn:hover {
    background-color: rgba(231, 76, 60, 0.2);
}

/* Estilos para los modales */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    overflow-y: auto;
    padding: 20px;
    box-sizing: border-box;
}

.modal-content {
    background-color: white;
    margin: 2% auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    width: 90%;
    animation: modalFadeIn 0.3s;
    position: relative;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.close-modal {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 1.5rem;
    color: #777;
    cursor: pointer;
    background: none;
    border: none;
}

.close-modal:hover {
    color: #333;
}

.modal-title {
    color: #6e48aa;
    margin-bottom: 20px;
    font-size: 1.5rem;
    text-align: center;
}

/* Estilos para el modal de confirmación */
#confirm-modal .modal-content {
    max-width: 500px;
    text-align: center;
}

#confirm-modal .btn-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
}

/* Media Queries para Responsive */
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
    #course-content > div, #my-courses-content > div, #create-user-content > div {
        flex-direction: column;
        gap: 15px;
    }
    #course-content > div > div, #my-courses-content > div > div, #create-user-content > div > div {
        flex: 1 1 100%;
    }
    .btn-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .btn {
        width: 100%;
        margin-right: 0;
        margin-bottom: 10px;
    }
    /* Ajustes para teclado móvil */
    textarea, input[type="text"], input[type="email"], input[type="password"] {
        font-size: 16px;
        padding: 10px;
        min-height: 50px;
    }
    
    /* Ajustes para modales en móviles */
    .modal-content {
        margin: 5% auto;
        width: 95%;
        padding: 15px;
    }
    
    .modal-title {
        font-size: 1.3rem;
    }
    
    .action-buttons {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 80px 15px 20px;
    }
    .dashboard-content {
        padding: 15px;
    }
    .question-container, .list-item {
        padding: 15px;
    }
    /* Mejoras adicionales para móviles */
    .content-header h1 {
        font-size: 1.3rem;
    }
    .btn {
        padding: 10px 15px;
        font-size: 15px;
    }
    
    /* Ajustes para modales en móviles pequeños */
    .modal-content {
        margin: 10% auto;
        width: 98%;
        padding: 15px 10px;
    }
    
    .modal-title {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    
    .close-modal {
        top: 10px;
        right: 10px;
        font-size: 1.3rem;
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
        <h3>Panel del Administrador</h3>
    </div>
    <div class="sidebar-menu">
        <div class="menu-item active" onclick="changeContent('profile')">
            <i class="fas fa-user"></i>
            <span>Perfil</span>
        </div>
        <div class="menu-item" onclick="changeContent('create-user')">
            <i class="fas fa-user-plus"></i>
            <span>Crear Profesor</span>
        </div>
        <div class="menu-item" onclick="changeContent('course')">
            <i class="fas fa-book"></i>
            <span>Crear Curso</span>
        </div>
        <div class="menu-item" onclick="changeContent('my-courses')">
            <i class="fas fa-graduation-cap"></i>
            <span>Mis Cursos</span>
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
            <!-- Contenido de Perfil -->
            <div id="profile-content" class="content-section">
                <h2>Información del Administrador</h2>
                <p>Nombre: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrador'); ?></p>
                <p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@escuela.com'); ?></p>
                <p>Rol: Administrador</p>
                <div style="margin-top: 20px;">
                    <p>Bienvenido al panel de control para administradores. Desde aquí puedes gestionar profesores y cursos.</p>
                </div>
            </div>

            <!-- Contenido para crear usuario -->
            <div id="create-user-content" class="hidden content-section">
                <div class="quiz-container">
                    <h2>Crear Nuevo Profesor</h2>
                    
                    <form id="create-user-form">
                        <div class="form-group">
                            <label for="user-firstName">Nombre:</label>
                            <input type="text" id="user-firstName" required placeholder="Nombre del profesor">
                        </div>
                        
                        <div class="form-group">
                            <label for="user-lastName">Apellido:</label>
                            <input type="text" id="user-lastName" required placeholder="Apellido del profesor">
                        </div>
                        
                        <div class="form-group">
                            <label for="user-email">Email:</label>
                            <input type="email" id="user-email" required placeholder="Correo electrónico">
                        </div>
                        
                        <div class="form-group">
                            <label for="user-password">Contraseña:</label>
                            <input type="password" id="user-password" required placeholder="Contraseña">
                        </div>
                        
                        <input type="hidden" id="user-type" value="teacher">
                        
                        <div class="btn-container">
                            <button type="button" class="btn" id="save-user">Guardar Profesor</button>
                        </div>
                    </form>
                    
                    <div id="user-success-message" class="success-message hidden">
                        ¡Profesor creado correctamente!
                    </div>
                    <div id="user-error-message" class="error-message hidden"></div>
                </div>
            </div>

            <!-- Contenido de Course -->
            <div id="course-content" class="hidden content-section">
                <div class="quiz-container">
                    <h2>Crear Nuevo Curso</h2>
                    
                    <div id="course-form">
                        <div class="form-group">
                            <label for="course-title">Título del Curso:</label>
                            <input type="text" id="course-title" required placeholder="Ej: Matemáticas Avanzadas"
                                   onclick="this.focus();" autofocus>
                        </div>
                        
                        <div class="form-group">
                            <label for="course-description">Descripción:</label>
                            <textarea id="course-description" rows="3" required 
                                     placeholder="Descripción detallada del curso" onclick="this.focus();"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Estado del Curso:</label>
                            <select id="course-status" class="form-control">
                                <option value="borrador">Borrador</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        
                        <div id="sections-container">
                            <!-- Las secciones se agregarán aquí dinámicamente -->
                        </div>
                        
                        <div class="btn-container">
                            <button type="button" class="btn" id="add-section">+ Agregar Sección</button>
                            <button type="button" class="btn" id="save-course">Guardar Curso</button>
                        </div>
                    </div>
                    
                    <div id="course-success-message" class="success-message hidden">
                        ¡Curso guardado correctamente!
                    </div>
                    <div id="course-error-message" class="error-message hidden"></div>
                </div>
            </div>

            <!-- Contenido de Mis Cursos -->
            <div id="my-courses-content" class="hidden content-section">
                <div class="quiz-container">
                    <h2>Mis Cursos</h2>
                    <div id="courses-list" class="dashboard-list">
                        <!-- Los cursos se cargarán aquí dinámicamente -->
                        <div class="loading-message">Cargando cursos...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar curso -->
    <div id="edit-course-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('edit-course-modal')">&times;</button>
            <h3 class="modal-title">Editar Curso</h3>
            <div id="edit-course-container">
                <!-- El contenido del curso se cargará aquí dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <button class="close-modal" onclick="closeModal('confirm-modal')">&times;</button>
            <h3 class="modal-title" id="confirm-title">Confirmar acción</h3>
            <p id="confirm-message">¿Estás seguro de que deseas realizar esta acción?</p>
            <div class="btn-container" style="display: flex; justify-content: center; gap: 15px; margin-top: 25px;">
                <button type="button" class="btn btn-danger" id="confirm-cancel">Cancelar</button>
                <button type="button" class="btn" id="confirm-ok">Aceptar</button>
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

    function showModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = '';
    }

    function showConfirm(title, message, callback) {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        showModal('confirm-modal');
        
        const confirmOk = document.getElementById('confirm-ok');
        const confirmCancel = document.getElementById('confirm-cancel');
        
        // Limpiar eventos anteriores
        confirmOk.onclick = null;
        confirmCancel.onclick = null;
        
        // Asignar nuevos eventos
        confirmOk.onclick = function() {
            closeModal('confirm-modal');
            callback(true);
        };
        
        confirmCancel.onclick = function() {
            closeModal('confirm-modal');
            callback(false);
        };
    }

    // ==============================================
    // FUNCIONES PARA CARGAR DATOS
    // ==============================================

    async function loadUserCourses() {
        const coursesList = document.getElementById('courses-list');
        coursesList.innerHTML = '<div class="loading-message">Cargando cursos...</div>';
        
        try {
            const response = await fetch('get-user-courses.php');
            const data = await response.json();
            
            if (data.length === 0) {
                coursesList.innerHTML = '<div class="empty-message">No has creado ningún curso aún</div>';
                return;
            }
            
            let html = '';
            data.forEach(course => {
                html += `
                <div class="list-item" data-id="${course.id_curso}">
                    <h3>${course.titulo}</h3>
                    <p>${course.sections_count} secciones</p>
                    <div class="meta">
                        <span>Creado: ${formatDate(course.fecha_creacion)}</span>
                        <span>Estado: ${getStatusBadge(course.estado)}</span>
                    </div>
                    <div class="action-buttons">
                        <button class="action-btn edit-btn" onclick="editCourse(${course.id_curso})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteCourse(${course.id_curso}, '${course.titulo.replace(/'/g, "\\'")}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                `;
            });
            coursesList.innerHTML = html;
            
        } catch (error) {
            console.error('Error:', error);
            coursesList.innerHTML = '<div class="error-message">Error al cargar los cursos</div>';
        }
    }

    // ==============================================
    // FUNCIONES PARA ELIMINAR
    // ==============================================

    function deleteCourse(courseId, courseTitle) {
        showConfirm(
            'Eliminar Curso',
            `¿Estás seguro de que deseas eliminar el curso "${courseTitle}"? Esta acción no se puede deshacer.`,
            async function(confirmed) {
                if (confirmed) {
                    try {
                        const response = await fetch('delete-course.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ courseId })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            loadUserCourses();
                        } else {
                            alert('Error al eliminar el curso: ' + (result.message || 'Error desconocido'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al conectar con el servidor');
                    }
                }
            }
        );
    }

    // ==============================================
    // FUNCIONES PARA EDITAR
    // ==============================================

    async function editCourse(courseId) {
        try {
            // Mostrar carga mientras se obtienen los datos
            const editCourseContainer = document.getElementById('edit-course-container');
            editCourseContainer.innerHTML = '<div class="loading-message">Cargando curso...</div>';
            showModal('edit-course-modal');
            
            // Obtener los datos del curso
            const response = await fetch(`get-course.php?id=${courseId}`);
            const courseData = await response.json();
            
            if (courseData.error) {
                throw new Error(courseData.error);
            }
            
            // Construir el formulario de edición
            let html = `
                <div class="form-group">
                    <label for="edit-course-title">Título del Curso:</label>
                    <input type="text" id="edit-course-title" value="${courseData.titulo.replace(/"/g, '&quot;')}" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-course-description">Descripción:</label>
                    <textarea id="edit-course-description" rows="3" required>${courseData.descripcion}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Estado del Curso:</label>
                    <select id="edit-course-status" class="form-control">
                        <option value="borrador" ${courseData.estado === 'borrador' ? 'selected' : ''}>Borrador</option>
                        <option value="activo" ${courseData.estado === 'activo' ? 'selected' : ''}>Activo</option>
                        <option value="inactivo" ${courseData.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>
                
                <div id="edit-sections-container">
            `;
            
            // Agregar cada sección
            courseData.sections.forEach((section, index) => {
                html += `
                <div class="question-container" id="edit-section-${index + 1}">
                    <div class="form-group">
                        <label>Título de la Sección ${index + 1}:</label>
                        <input type="text" id="edit-section-title-${index + 1}" value="${section.titulo.replace(/"/g, '&quot;')}" required>
                    </div>
                    <div class="form-group">
                        <label>Definición/Contenido:</label>
                        <textarea id="edit-section-definition-${index + 1}" rows="3" required>${section.definicion}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Ejemplo:</label>
                        <textarea id="edit-section-example-${index + 1}" rows="2">${section.ejemplo || ''}</textarea>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeEditSection(${index + 1}, ${courseData.sections.length})">Eliminar Sección</button>
                </div>
                `;
            });
            
            html += `
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn" onclick="addEditSection()">+ Agregar Sección</button>
                    <button type="button" class="btn" onclick="updateCourse(${courseId})">Guardar Cambios</button>
                </div>
            `;
            
            editCourseContainer.innerHTML = html;
            
        } catch (error) {
            console.error('Error al cargar el curso:', error);
            editCourseContainer.innerHTML = `
                <div class="error-message">
                    Error al cargar el curso<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

    // ==============================================
    // FUNCIONES PARA MANEJAR LA EDICIÓN
    // ==============================================

    // Variable para contar secciones en el modal de edición
    let editSectionCount = 0;

    function addEditSection() {
        editSectionCount++;
        const container = document.getElementById('edit-sections-container');
        const newSection = `
        <div class="question-container" id="edit-section-${editSectionCount}">
            <div class="form-group">
                <label>Título de la Sección ${editSectionCount}:</label>
                <input type="text" id="edit-section-title-${editSectionCount}" required placeholder="Ej: Introducción al Álgebra">
            </div>
            <div class="form-group">
                <label>Definición/Contenido:</label>
                <textarea id="edit-section-definition-${editSectionCount}" rows="3" required placeholder="Explicación detallada de la sección"></textarea>
            </div>
            <div class="form-group">
                <label>Ejemplo:</label>
                <textarea id="edit-section-example-${editSectionCount}" rows="2" placeholder="Ejemplo práctico (opcional)"></textarea>
            </div>
            <button type="button" class="btn btn-danger" onclick="removeEditSection(${editSectionCount}, 1)">Eliminar Sección</button>
        </div>
        `;
        container.insertAdjacentHTML('beforeend', newSection);
        
        // Desplazarse a la nueva sección
        const newSectionElement = document.getElementById(`edit-section-${editSectionCount}`);
        newSectionElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function removeEditSection(id, totalSections) {
        if (totalSections <= 1) {
            alert('Debe haber al menos una sección');
            return;
        }
        
        const sectionEl = document.getElementById(`edit-section-${id}`);
        if (sectionEl) {
            sectionEl.remove();
            
            // Reorganizar los números de las secciones restantes
            const sections = document.querySelectorAll('#edit-sections-container .question-container');
            sections.forEach((s, index) => {
                const labels = s.querySelectorAll('label');
                labels[0].textContent = `Título de la Sección ${index + 1}:`;
                s.id = `edit-section-${index + 1}`;
                
                // Actualizar los IDs de los inputs
                const titleInput = s.querySelector('input[type="text"]');
                const definitionTextarea = s.querySelectorAll('textarea')[0];
                const exampleTextarea = s.querySelectorAll('textarea')[1];
                const deleteBtn = s.querySelector('button');
                
                titleInput.id = `edit-section-title-${index + 1}`;
                definitionTextarea.id = `edit-section-definition-${index + 1}`;
                exampleTextarea.id = `edit-section-example-${index + 1}`;
                deleteBtn.setAttribute('onclick', `removeEditSection(${index + 1}, ${sections.length})`);
            });
            
            editSectionCount = sections.length;
        }
    }

    async function updateCourse(courseId) {
        const title = document.getElementById('edit-course-title').value.trim();
        const description = document.getElementById('edit-course-description').value.trim();
        const status = document.getElementById('edit-course-status').value;
        const sections = [];
        
        // Validar campos básicos
        if (!title) {
            alert('Por favor, ingresa un título para el curso');
            return;
        }
        
        if (!description) {
            alert('Por favor, ingresa una descripción para el curso');
            return;
        }
        
        // Recopilar secciones
        const sectionElements = document.querySelectorAll('#edit-sections-container .question-container');
        
        if (sectionElements.length === 0) {
            alert('Debe haber al menos una sección');
            return;
        }
        
        let isValid = true;
        sectionElements.forEach((s, index) => {
            const sId = index + 1;
            const sectionTitle = document.getElementById(`edit-section-title-${sId}`).value.trim();
            const sectionDefinition = document.getElementById(`edit-section-definition-${sId}`).value.trim();
            
            if (!sectionTitle || !sectionDefinition) {
                isValid = false;
                alert(`Por favor, completa todos los campos obligatorios de la sección ${sId}`);
                return;
            }
            
            sections.push({
                title: sectionTitle,
                definition: sectionDefinition,
                example: document.getElementById(`edit-section-example-${sId}`).value.trim()
            });
        });
        
        if (!isValid) return;
        
        try {
            const response = await fetch('update-course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    courseId,
                    title,
                    description,
                    status,
                    sections
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Curso actualizado correctamente');
                closeModal('edit-course-modal');
                loadUserCourses();
            } else {
                alert('Error al actualizar el curso: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    }

    // ==============================================
    // FUNCIÓN PRINCIPAL PARA CAMBIAR CONTENIDO
    // ==============================================

    function changeContent(section) {
        // Ocultar todos los contenidos
        document.getElementById('profile-content').classList.add('hidden');
        document.getElementById('create-user-content').classList.add('hidden');
        document.getElementById('course-content').classList.add('hidden');
        document.getElementById('my-courses-content').classList.add('hidden');
        
        // Mostrar el contenido seleccionado
        const contentSection = document.getElementById(`${section}-content`);
        contentSection.classList.remove('hidden');
        
        // Agregar animación
        contentSection.style.animation = 'fadeIn 0.5s ease-in-out';
        
        // Actualizar el título
        let title = '';
        switch(section) {
            case 'profile':
                title = 'Perfil';
                break;
            case 'create-user':
                title = 'Crear Profesor';
                setTimeout(() => {
                    const firstNameInput = document.getElementById('user-firstName');
                    if (firstNameInput) {
                        firstNameInput.focus();
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            firstNameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }, 300);
                break;
            case 'course':
                title = 'Crear Curso';
                setTimeout(() => {
                    const courseTitle = document.getElementById('course-title');
                    if (courseTitle) {
                        courseTitle.focus();
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            courseTitle.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }, 300);
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
    // FUNCIONES PARA EL SISTEMA DE CURSOS
    // ==============================================

    function initializeCourseSystem() {
        let sectionCount = 0;
        const sectionsContainer = document.getElementById('sections-container');
        const addSectionBtn = document.getElementById('add-section');
        const saveCourseBtn = document.getElementById('save-course');
        const courseTitle = document.getElementById('course-title');
        const courseDescription = document.getElementById('course-description');
        const courseStatus = document.getElementById('course-status');
        const courseSuccessMessage = document.getElementById('course-success-message');
        const courseErrorMessage = document.getElementById('course-error-message');
        
        function createSectionTemplate() {
            sectionCount++;
            return `
            <div class="question-container" id="section-${sectionCount}">
                <div class="form-group">
                    <label>Título de la Sección ${sectionCount}:</label>
                    <input type="text" id="section-title-${sectionCount}" required 
                           placeholder="Ej: Introducción al Álgebra" onclick="this.focus();">
                </div>
                <div class="form-group">
                    <label>Definición/Contenido:</label>
                    <textarea id="section-definition-${sectionCount}" rows="3" required 
                             placeholder="Explicación detallada de la sección" onclick="this.focus();"></textarea>
                </div>
                <div class="form-group">
                    <label>Ejemplo:</label>
                    <textarea id="section-example-${sectionCount}" rows="2" 
                               placeholder="Ejemplo práctico (opcional)"></textarea>
                </div>
                <button type="button" class="btn btn-danger" onclick="removeSection(${sectionCount})">Eliminar Sección</button>
            </div>
            `;
        }
        
        window.removeSection = function(id) {
            const sectionEl = document.getElementById(`section-${id}`);
            if (sectionEl) {
                if (document.querySelectorAll('#sections-container .question-container').length <= 1) {
                    showCourseError('Debe haber al menos una sección');
                    return;
                }
                sectionEl.remove();
                const sections = document.querySelectorAll('#sections-container .question-container');
                sections.forEach((s, index) => {
                    const labels = s.querySelectorAll('label');
                    labels[0].textContent = `Título de la Sección ${index + 1}:`;
                });
            }
        };
        
        function showCourseError(message) {
            courseErrorMessage.textContent = message;
            courseErrorMessage.classList.remove('hidden');
            setTimeout(() => {
                courseErrorMessage.classList.add('hidden');
            }, 5000);
        }
        
        function validateCourseForm() {
            const title = courseTitle.value.trim();
            const description = courseDescription.value.trim();
            const sections = document.querySelectorAll('#sections-container .question-container');
            
            if (!title) {
                showCourseError('Por favor, ingresa un título para el curso');
                return false;
            }
            
            if (!description) {
                showCourseError('Por favor, ingresa una descripción para el curso');
                return false;
            }
            
            if (sections.length === 0) {
                showCourseError('Debe haber al menos una sección');
                return false;
            }
            
            let isValid = true;
            sections.forEach((s, index) => {
                const sId = index + 1;
                const sectionTitle = document.getElementById(`section-title-${sId}`).value.trim();
                const sectionDefinition = document.getElementById(`section-definition-${sId}`).value.trim();
                
                if (!sectionTitle || !sectionDefinition) {
                    showCourseError('Por favor, completa todos los campos obligatorios de las secciones');
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        addSectionBtn.addEventListener('click', function() {
            sectionsContainer.insertAdjacentHTML('beforeend', createSectionTemplate());
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
            
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                const newSection = document.getElementById(`section-title-${sectionCount}`);
                if (newSection) {
                    setTimeout(() => {
                        newSection.focus();
                        newSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }
        });
        
        saveCourseBtn.addEventListener('click', function() {
            courseSuccessMessage.classList.add('hidden');
            courseErrorMessage.classList.add('hidden');
            
            if (!validateCourseForm()) return;
            
            const courseData = {
                title: courseTitle.value.trim(),
                description: courseDescription.value.trim(),
                status: courseStatus.value,
                sections: []
            };
            
            document.querySelectorAll('#sections-container .question-container').forEach((s, index) => {
                const sId = index + 1;
                courseData.sections.push({
                    title: document.getElementById(`section-title-${sId}`).value.trim(),
                    definition: document.getElementById(`section-definition-${sId}`).value.trim(),
                    example: document.getElementById(`section-example-${sId}`).value.trim()
                });
            });
            
            saveCourseBtn.disabled = true;
            saveCourseBtn.textContent = 'Guardando...';
            
            fetch('save-course.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(courseData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    courseSuccessMessage.classList.remove('hidden');
                    courseTitle.value = '';
                    courseDescription.value = '';
                    sectionsContainer.innerHTML = '';
                    sectionCount = 0;
                    addSectionBtn.click();
                    setTimeout(() => {
                        courseSuccessMessage.classList.add('hidden');
                    }, 3000);
                } else {
                    showCourseError(data.message || 'Error al guardar el curso');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCourseError('Error al conectar con el servidor: ' + error.message);
            })
            .finally(() => {
                saveCourseBtn.disabled = false;
                saveCourseBtn.textContent = 'Guardar Curso';
            });
        });
        
        // Agregar primera sección automáticamente al entrar a cursos
        document.querySelector('.menu-item[onclick="changeContent(\'course\')"]').addEventListener('click', function() {
            setTimeout(() => {
                if (sectionCount === 0 && document.getElementById('course-content').classList.contains('hidden') === false) {
                    addSectionBtn.click();
                }
            }, 300);
        });
    }

    // ==============================================
    // FUNCIONES PARA EL SISTEMA DE CREACIÓN DE USUARIOS
    // ==============================================

    function initializeUserSystem() {
        const saveUserBtn = document.getElementById('save-user');
        const userSuccessMessage = document.getElementById('user-success-message');
        const userErrorMessage = document.getElementById('user-error-message');
        
        function validateUserForm() {
            const firstName = document.getElementById('user-firstName').value.trim();
            const lastName = document.getElementById('user-lastName').value.trim();
            const email = document.getElementById('user-email').value.trim();
            const password = document.getElementById('user-password').value.trim();
            
            if (!firstName) {
                showUserError('Por favor, ingresa el nombre del profesor');
                return false;
            }
            
            if (!lastName) {
                showUserError('Por favor, ingresa el apellido del profesor');
                return false;
            }
            
            if (!email) {
                showUserError('Por favor, ingresa el email del profesor');
                return false;
            }
            
            if (!password) {
                showUserError('Por favor, ingresa una contraseña');
                return false;
            }
            
            return true;
        }
        
        function showUserError(message) {
            userErrorMessage.textContent = message;
            userErrorMessage.classList.remove('hidden');
            setTimeout(() => {
                userErrorMessage.classList.add('hidden');
            }, 5000);
        }
        
        saveUserBtn.addEventListener('click', function() {
            userSuccessMessage.classList.add('hidden');
            userErrorMessage.classList.add('hidden');
            
            if (!validateUserForm()) return;
            
            const userData = {
                firstName: document.getElementById('user-firstName').value.trim(),
                lastName: document.getElementById('user-lastName').value.trim(),
                email: document.getElementById('user-email').value.trim(),
                password: document.getElementById('user-password').value.trim(),
                userType: document.getElementById('user-type').value
            };
            
            saveUserBtn.disabled = true;
            saveUserBtn.textContent = 'Guardando...';
            
            fetch('create-user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(userData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    userSuccessMessage.classList.remove('hidden');
                    document.getElementById('user-firstName').value = '';
                    document.getElementById('user-lastName').value = '';
                    document.getElementById('user-email').value = '';
                    document.getElementById('user-password').value = '';
                    
                    setTimeout(() => {
                        userSuccessMessage.classList.add('hidden');
                    }, 3000);
                } else {
                    showUserError(data.message || 'Error al crear el profesor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showUserError('Error al conectar con el servidor: ' + error.message);
            })
            .finally(() => {
                saveUserBtn.disabled = false;
                saveUserBtn.textContent = 'Guardar Profesor';
            });
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
        
        // Inicializar sistemas
        initializeCourseSystem();
        initializeUserSystem();
    });
</script>
</body>
</html>