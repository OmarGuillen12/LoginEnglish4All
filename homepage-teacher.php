<?php
session_start();
include('connect.php');

// Verificación de sesión y tipo de usuario
if (!isset($_SESSION['user_type'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['user_type'] != 'teacher') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Profesor</title>
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

/* Estilos específicos para el formulario de quiz */
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

input[type="text"], textarea, select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px;
    transition: all 0.3s;
}

input[type="text"]:focus, textarea:focus, select:focus {
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

#questions-container, #sections-container {
    margin-top: 25px;
}

#quiz-form, #course-form {
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

/* Estilos para listas de cursos y exámenes */
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
#profile-content, #course-content, #my-quizzes-content, #my-courses-content {
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
    #course-content > div, #my-courses-content > div, #my-quizzes-content > div {
        flex-direction: column;
        gap: 15px;
    }
    #course-content > div > div, #my-courses-content > div > div, #my-quizzes-content > div > div {
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
    textarea, input[type="text"] {
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






/* Estilos para el modal de estadísticas */
.stats-container {
    margin-top: 20px;
}

.question-stats {
    margin-bottom: 25px;
}

.question-title {
    font-weight: 600;
    margin-bottom: 8px;
    color: #6e48aa;
}

.bar-container {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.bar-label {
    width: 120px;
    font-size: 0.9em;
    color: #555;
}

.bar-wrapper {
    flex-grow: 1;
    height: 20px;
    background-color: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
}

.bar {
    height: 100%;
    background: linear-gradient(90deg, #6e48aa, #9d50bb);
    border-radius: 10px;
    transition: width 0.5s ease;
}

.bar-percentage {
    margin-left: 10px;
    font-size: 0.9em;
    color: #666;
}

.stats-summary {
    margin-top: 30px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    border-left: 4px solid #6e48aa;
}

.stats-summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.stats-summary-label {
    font-weight: 600;
    color: #555;
}

.stats-summary-value {
    color: #6e48aa;
    font-weight: 600;
}

/* Estilo para el botón de estadísticas */
.stats-btn {
    color: #9b59b6;
    background-color: rgba(155, 89, 182, 0.1);
}

.stats-btn:hover {
    background-color: rgba(155, 89, 182, 0.2);
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
            <h3>Panel del Profesor</h3>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" onclick="changeContent('profile')">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </div>
            <div class="menu-item" onclick="changeContent('quiz')">
                <i class="fas fa-question-circle"></i>
                <span>Crear Examen</span>
            </div>
            <div class="menu-item" onclick="changeContent('my-quizzes')">
                <i class="fas fa-list-check"></i>
                <span>Mis Exámenes</span>
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
                <h2>Información del Profesor</h2>
                <p>Nombre: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Profesor'); ?></p>
                <p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'profesor@escuela.com'); ?></p>
                <p>Rol: Profesor</p>
                <div style="margin-top: 20px;">
                    <p>Bienvenido al panel de control para profesores. Desde aquí puedes crear exámenes y gestionar tus cursos.</p>
                </div>
            </div>

            <!-- Contenido de Quiz -->
            <div id="quiz-content" class="hidden content-section">
                <div class="quiz-container">
                    <h2>Crear Nuevo Examen</h2>
                    
                    <div id="quiz-form">
                        <div class="form-group">
                            <label for="quiz-title">Título del Examen:</label>
                            <input type="text" id="quiz-title" required placeholder="Ej: Examen de Matemáticas - Primer Parcial"
                                   onclick="this.focus();" autofocus>
                        </div>
                        
                        <div id="questions-container">
                            <!-- Las preguntas se agregarán aquí dinámicamente -->
                        </div>
                        
                        <div class="btn-container">
                            <button type="button" class="btn" id="add-question">+ Agregar Pregunta</button>
                            <button type="button" class="btn" id="save-quiz">Guardar Examen</button>
                        </div>
                    </div>
                    
                    <div id="success-message" class="success-message hidden">
                        ¡Examen guardado correctamente!
                    </div>
                    <div id="error-message" class="error-message hidden"></div>
                </div>
            </div>

            <!-- Contenido de Mis Exámenes -->
            <div id="my-quizzes-content" class="hidden content-section">
                <div class="quiz-container">
                    <h2>Mis Exámenes</h2>
                    <div id="quizzes-list" class="dashboard-list">
                        <!-- Los exámenes se cargarán aquí dinámicamente -->
                        <div class="loading-message">Cargando exámenes...</div>
                    </div>
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

    <!-- Modal para editar examen -->
    <div id="edit-quiz-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('edit-quiz-modal')">&times;</button>
            <h3 class="modal-title">Editar Examen</h3>
            <div id="edit-quiz-container">
                <!-- El contenido del examen se cargará aquí dinámicamente -->
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

    async function loadUserQuizzes() {
    const quizzesList = document.getElementById('quizzes-list');
    quizzesList.innerHTML = '<div class="loading-message">Cargando exámenes...</div>';
    
    try {
        const response = await fetch('get-user-quizzes.php');
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error(`Respuesta inesperada: ${text.substring(0, 100)}...`);
        }
        
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        if (!Array.isArray(data)) {
            throw new Error('Formato de datos inesperado');
        }
        
        if (data.length === 0) {
            quizzesList.innerHTML = '<div class="empty-message">No has creado ningún examen aún</div>';
            return;
        }
        
        let html = '';
        data.forEach(quiz => {
            html += `
            <div class="list-item" data-id="${quiz.id}">
                <h3>${quiz.title}</h3>
                <p>${quiz.questions_count} preguntas</p>
                <div class="meta">
                    <span>Creado: ${formatDate(quiz.created_at)}</span>
                    <span>Estado: ${getStatusBadge(quiz.status || 'activo')}</span>
                </div>
                <div class="action-buttons">
                    <button class="action-btn stats-btn" onclick="showQuizStats(${quiz.id}, '${quiz.title.replace(/'/g, "\\'")}')">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button class="action-btn edit-btn" onclick="editQuiz(${quiz.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteQuiz(${quiz.id}, '${quiz.title.replace(/'/g, "\\'")}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
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

// Función para mostrar estadísticas del examen
async function showQuizStats(quizId, quizTitle) {
    const statsContainer = document.getElementById('stats-container');
    statsContainer.innerHTML = '<div class="loading-message">Cargando estadísticas...</div>';
    document.getElementById('stats-title').textContent = `Estadísticas: ${quizTitle}`;
    showModal('stats-modal');
    
    try {
        const response = await fetch(`get-quiz-stats.php?id=${quizId}`);
        const statsData = await response.json();
        
        if (statsData.error) {
            throw new Error(statsData.error);
        }
        
        let html = '';
        
        if (statsData.questions.length === 0) {
            html = '<div class="empty-message">No hay datos de respuestas para este examen.</div>';
        } else {
            // Resumen general
            html += `
            <div class="stats-summary">
                <div class="stats-summary-item">
                    <span class="stats-summary-label">Total de estudiantes:</span>
                    <span class="stats-summary-value">${statsData.total_students}</span>
                </div>
                <div class="stats-summary-item">
                    <span class="stats-summary-label">Promedio de aciertos:</span>
                    <span class="stats-summary-value">${statsData.average_correct}%</span>
                </div>
            </div>
            `;
            
            // Estadísticas por pregunta
            statsData.questions.forEach((question, index) => {
                const correctPercentage = Math.round((question.correct_answers / statsData.total_students) * 100) || 0;
                
                html += `
                <div class="question-stats">
                    <h4 class="question-title">Pregunta ${index + 1}: ${question.text}</h4>
                    
                    <div class="bar-container">
                        <div class="bar-label">Respuestas correctas:</div>
                        <div class="bar-wrapper">
                            <div class="bar" style="width: ${correctPercentage}%"></div>
                        </div>
                        <div class="bar-percentage">${correctPercentage}% (${question.correct_answers}/${statsData.total_students})</div>
                    </div>
                    
                    <div class="bar-container">
                        <div class="bar-label">Opción 1 (Correcta):</div>
                        <div class="bar-wrapper">
                            <div class="bar" style="width: ${question.option1_percentage}%; background: ${question.option1_percentage === correctPercentage ? 'linear-gradient(90deg, #6e48aa, #9d50bb)' : 'linear-gradient(90deg, #27ae60, #2ecc71)'}"></div>
                        </div>
                        <div class="bar-percentage">${question.option1_percentage}%</div>
                    </div>
                    
                    <div class="bar-container">
                        <div class="bar-label">Opción 2:</div>
                        <div class="bar-wrapper">
                            <div class="bar" style="width: ${question.option2_percentage}%; background: linear-gradient(90deg, #e74c3c, #f39c12)"></div>
                        </div>
                        <div class="bar-percentage">${question.option2_percentage}%</div>
                    </div>
                    
                    <div class="bar-container">
                        <div class="bar-label">Opción 3:</div>
                        <div class="bar-wrapper">
                            <div class="bar" style="width: ${question.option3_percentage}%; background: linear-gradient(90deg, #e74c3c, #f39c12)"></div>
                        </div>
                        <div class="bar-percentage">${question.option3_percentage}%</div>
                    </div>
                </div>
                `;
            });
        }
        
        statsContainer.innerHTML = html;
        
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
        statsContainer.innerHTML = `
            <div class="error-message">
                Error al cargar las estadísticas<br>
                <small>${error.message}</small>
            </div>
        `;
    }
}
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

    function deleteQuiz(quizId, quizTitle) {
        showConfirm(
            'Eliminar Examen',
            `¿Estás seguro de que deseas eliminar el examen "${quizTitle}"? Esta acción no se puede deshacer.`,
            async function(confirmed) {
                if (confirmed) {
                    try {
                        const response = await fetch('delete-quiz.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ quizId })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            loadUserQuizzes();
                        } else {
                            alert('Error al eliminar el examen: ' + (result.message || 'Error desconocido'));
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al conectar con el servidor');
                    }
                }
            }
        );
    }

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

    async function editQuiz(quizId) {
        try {
            // Mostrar carga mientras se obtienen los datos
            const editQuizContainer = document.getElementById('edit-quiz-container');
            editQuizContainer.innerHTML = '<div class="loading-message">Cargando examen...</div>';
            showModal('edit-quiz-modal');
            
            // Obtener los datos del examen
            const response = await fetch(`get-quiz.php?id=${quizId}`);
            const quizData = await response.json();
            
            if (quizData.error) {
                throw new Error(quizData.error);
            }
            
            // Construir el formulario de edición
            let html = `
                <div class="form-group">
                    <label for="edit-quiz-title">Título del Examen:</label>
                    <input type="text" id="edit-quiz-title" value="${quizData.title.replace(/"/g, '&quot;')}" required>
                </div>
                
                <div id="edit-questions-container">
            `;
            
            // Agregar cada pregunta
            quizData.questions.forEach((question, index) => {
                html += `
                <div class="question-container" id="edit-question-${index + 1}">
                    <div class="form-group">
                        <label>Pregunta ${index + 1}:</label>
                        <textarea id="edit-question-text-${index + 1}" rows="3" required>${question.text}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Opción 1 (Respuesta Correcta):</label>
                        <input type="text" id="edit-option1-${index + 1}" value="${question.option1.replace(/"/g, '&quot;')}" required>
                    </div>
                    <div class="form-group">
                        <label>Opción 2:</label>
                        <input type="text" id="edit-option2-${index + 1}" value="${question.option2.replace(/"/g, '&quot;')}" required>
                    </div>
                    <div class="form-group">
                        <label>Opción 3:</label>
                        <input type="text" id="edit-option3-${index + 1}" value="${question.option3.replace(/"/g, '&quot;')}" required>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeEditQuestion(${index + 1}, ${quizData.questions.length})">Eliminar Pregunta</button>
                </div>
                `;
            });
            
            html += `
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn" onclick="addEditQuestion()">+ Agregar Pregunta</button>
                    <button type="button" class="btn" onclick="updateQuiz(${quizId})">Guardar Cambios</button>
                </div>
            `;
            
            editQuizContainer.innerHTML = html;
            
        } catch (error) {
            console.error('Error al cargar el examen:', error);
            editQuizContainer.innerHTML = `
                <div class="error-message">
                    Error al cargar el examen<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

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

    // Variables para contar preguntas y secciones en los modales de edición
    let editQuestionCount = 0;
    let editSectionCount = 0;

    function addEditQuestion() {
        editQuestionCount++;
        const container = document.getElementById('edit-questions-container');
        const newQuestion = `
        <div class="question-container" id="edit-question-${editQuestionCount}">
            <div class="form-group">
                <label>Pregunta ${editQuestionCount}:</label>
                <textarea id="edit-question-text-${editQuestionCount}" rows="3" required placeholder="Escribe aquí la pregunta"></textarea>
            </div>
            <div class="form-group">
                <label>Opción 1 (Respuesta Correcta):</label>
                <input type="text" id="edit-option1-${editQuestionCount}" required placeholder="Respuesta correcta">
            </div>
            <div class="form-group">
                <label>Opción 2:</label>
                <input type="text" id="edit-option2-${editQuestionCount}" required placeholder="Segunda opción">
            </div>
            <div class="form-group">
                <label>Opción 3:</label>
                <input type="text" id="edit-option3-${editQuestionCount}" required placeholder="Tercera opción">
            </div>
            <button type="button" class="btn btn-danger" onclick="removeEditQuestion(${editQuestionCount}, 1)">Eliminar Pregunta</button>
        </div>
        `;
        container.insertAdjacentHTML('beforeend', newQuestion);
        
        // Desplazarse a la nueva pregunta
        const newQuestionElement = document.getElementById(`edit-question-${editQuestionCount}`);
        newQuestionElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function removeEditQuestion(id, totalQuestions) {
        if (totalQuestions <= 1) {
            alert('Debe haber al menos una pregunta');
            return;
        }
        
        const questionEl = document.getElementById(`edit-question-${id}`);
        if (questionEl) {
            questionEl.remove();
            
            // Reorganizar los números de las preguntas restantes
            const questions = document.querySelectorAll('#edit-questions-container .question-container');
            questions.forEach((q, index) => {
                const labels = q.querySelectorAll('label');
                labels[0].textContent = `Pregunta ${index + 1}:`;
                q.id = `edit-question-${index + 1}`;
                
                // Actualizar los IDs de los inputs
                const textarea = q.querySelector('textarea');
                const inputs = q.querySelectorAll('input[type="text"]');
                const deleteBtn = q.querySelector('button');
                
                textarea.id = `edit-question-text-${index + 1}`;
                inputs[0].id = `edit-option1-${index + 1}`;
                inputs[1].id = `edit-option2-${index + 1}`;
                inputs[2].id = `edit-option3-${index + 1}`;
                deleteBtn.setAttribute('onclick', `removeEditQuestion(${index + 1}, ${questions.length})`);
            });
            
            editQuestionCount = questions.length;
        }
    }

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

    async function updateQuiz(quizId) {
        const title = document.getElementById('edit-quiz-title').value.trim();
        const questions = [];
        
        // Validar título
        if (!title) {
            alert('Por favor, ingresa un título para el examen');
            return;
        }
        
        // Recopilar preguntas
        const questionElements = document.querySelectorAll('#edit-questions-container .question-container');
        
        if (questionElements.length === 0) {
            alert('Debe haber al menos una pregunta');
            return;
        }
        
        let isValid = true;
        questionElements.forEach((q, index) => {
            const qId = index + 1;
            const questionText = document.getElementById(`edit-question-text-${qId}`).value.trim();
            const option1 = document.getElementById(`edit-option1-${qId}`).value.trim();
            const option2 = document.getElementById(`edit-option2-${qId}`).value.trim();
            const option3 = document.getElementById(`edit-option3-${qId}`).value.trim();
            
            if (!questionText || !option1 || !option2 || !option3) {
                isValid = false;
                alert(`Por favor, completa todos los campos de la pregunta ${qId}`);
                return;
            }
            
            questions.push({
                text: questionText,
                option1: option1,
                option2: option2,
                option3: option3
            });
        });
        
        if (!isValid) return;
        
        try {
            const response = await fetch('update-quiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    quizId,
                    title,
                    questions
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Examen actualizado correctamente');
                closeModal('edit-quiz-modal');
                loadUserQuizzes();
            } else {
                alert('Error al actualizar el examen: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
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
        document.getElementById('quiz-content').classList.add('hidden');
        document.getElementById('my-quizzes-content').classList.add('hidden');
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
            case 'quiz':
                title = 'Crear Examen';
                setTimeout(() => {
                    const quizTitle = document.getElementById('quiz-title');
                    if (quizTitle) {
                        quizTitle.focus();
                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            quizTitle.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }, 300);
                break;
            case 'my-quizzes':
                title = 'Mis Exámenes';
                loadUserQuizzes();
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
    // FUNCIONES PARA EL SISTEMA DE QUIZZES
    // ==============================================

    function initializeQuizSystem() {
        let questionCount = 0;
        const questionsContainer = document.getElementById('questions-container');
        const addQuestionBtn = document.getElementById('add-question');
        const saveQuizBtn = document.getElementById('save-quiz');
        const quizTitle = document.getElementById('quiz-title');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        
        function createQuestionTemplate() {
            questionCount++;
            return `
            <div class="question-container" id="question-${questionCount}">
                <div class="form-group">
                    <label>Pregunta ${questionCount}:</label>
                    <textarea id="question-text-${questionCount}" rows="3" required 
                             placeholder="Escribe aquí la pregunta" onclick="this.focus();"></textarea>
                </div>
                <div class="form-group">
                    <label>Opción 1 (Respuesta Correcta):</label>
                    <input type="text" id="option1-${questionCount}" required 
                           placeholder="Respuesta correcta" onclick="this.focus();">
                </div>
                <div class="form-group">
                    <label>Opción 2:</label>
                    <input type="text" id="option2-${questionCount}" required 
                           placeholder="Segunda opción" onclick="this.focus();">
                </div>
                <div class="form-group">
                    <label>Opción 3:</label>
                    <input type="text" id="option3-${questionCount}" required 
                           placeholder="Tercera opción" onclick="this.focus();">
                </div>
                <button type="button" class="btn btn-danger" onclick="removeQuestion(${questionCount})">Eliminar Pregunta</button>
            </div>
            `;
        }
        
        window.removeQuestion = function(id) {
            const questionEl = document.getElementById(`question-${id}`);
            if (questionEl) {
                if (document.querySelectorAll('.question-container').length <= 1) {
                    showError('Debe haber al menos una pregunta');
                    return;
                }
                questionEl.remove();
                const questions = document.querySelectorAll('.question-container');
                questions.forEach((q, index) => {
                    const labels = q.querySelectorAll('label');
                    labels[0].textContent = `Pregunta ${index + 1}:`;
                });
            }
        };
        
        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        }
        
        function validateForm() {
    const title = quizTitle.value.trim();
    const questions = document.querySelectorAll('.question-container');
    
    if (!title) {
        showError('Por favor, ingresa un título para el examen');
        return false;
    }
    
    if (questions.length === 0) {
        showError('Debe haber al menos una pregunta');
        return false;
    }
    
    let isValid = true;
    questions.forEach((question) => {
        // Buscar elementos dentro del contenedor de la pregunta
        const questionText = question.querySelector('textarea').value.trim();
        const options = question.querySelectorAll('input[type="text"]');
        const option1 = options[0].value.trim();
        const option2 = options[1].value.trim();
        const option3 = options[2].value.trim();
        
        if (!questionText || !option1 || !option2 || !option3) {
            showError('Por favor, completa todos los campos de las preguntas');
            isValid = false;
            // Resaltar la pregunta incompleta
            question.style.border = '2px solid #ff5e62';
            setTimeout(() => {
                question.style.border = '';
            }, 3000);
        }
    });
    
    return isValid;
}
        
        addQuestionBtn.addEventListener('click', function() {
            questionsContainer.insertAdjacentHTML('beforeend', createQuestionTemplate());
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
            
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                const newQuestion = document.getElementById(`question-text-${questionCount}`);
                if (newQuestion) {
                    setTimeout(() => {
                        newQuestion.focus();
                        newQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }
        });
        
       saveQuizBtn.addEventListener('click', function() {
    successMessage.classList.add('hidden');
    errorMessage.classList.add('hidden');
    
    if (!validateForm()) return;
    
    const quizData = {
        title: quizTitle.value.trim(),
        questions: []
    };
    
    document.querySelectorAll('.question-container').forEach((question) => {
        const questionText = question.querySelector('textarea').value.trim();
        const options = question.querySelectorAll('input[type="text"]');
        
        quizData.questions.push({
            text: questionText,
            option1: options[0].value.trim(),
            option2: options[1].value.trim(),
            option3: options[2].value.trim()
        });
    });
            
            saveQuizBtn.disabled = true;
            saveQuizBtn.textContent = 'Guardando...';
            
            fetch('save-quiz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(quizData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    successMessage.classList.remove('hidden');
                    quizTitle.value = '';
                    questionsContainer.innerHTML = '';
                    questionCount = 0;
                    addQuestionBtn.click();
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 3000);
                } else {
                    showError(data.message || 'Error al guardar el examen');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error al conectar con el servidor: ' + error.message);
            })
            .finally(() => {
                saveQuizBtn.disabled = false;
                saveQuizBtn.textContent = 'Guardar Examen';
            });
        });
        
        // Agregar primera pregunta automáticamente
        addQuestionBtn.click();
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
        initializeQuizSystem();
        initializeCourseSystem();
    });
</script>

<!-- Modal para estadísticas de examen -->
<div id="stats-modal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <button class="close-modal" onclick="closeModal('stats-modal')">&times;</button>
        <h3 class="modal-title" id="stats-title">Estadísticas del Examen</h3>
        <div id="stats-container">
            <div class="loading-message">Cargando estadísticas...</div>
        </div>
    </div>
</div>

</body>
</html>