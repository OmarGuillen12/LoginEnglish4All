<?php
session_start();
include 'connect.php';

// Registrar nuevo usuario
if (isset($_POST['signUp'])) {
    $firstName = $conn->real_escape_string($_POST['fName']);
    $lastName = $conn->real_escape_string($_POST['lName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $semestre = $conn->real_escape_string($_POST['semestre']);

    // Validar semestre
    $allowed_semesters = [
        '1er semestre', '2do semestre', '3er semestre',
        '4to semestre', '5to semestre', '6to semestre'
    ];
    
    if (!in_array($semestre, $allowed_semesters)) {
        die("Semestre no válido");
    }

    // Hash de contraseña seguro
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Verificar si el email existe
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        die("El email ya está registrado");
    }
    $checkEmail->close();

    // Insertar nuevo usuario
    $insertQuery = $conn->prepare("INSERT INTO users (firstName, lastName, email, password, user_type, semestre) 
                                 VALUES (?, ?, ?, ?, 'student', ?)");
    $insertQuery->bind_param("sssss", $firstName, $lastName, $email, $passwordHash, $semestre);

    if ($insertQuery->execute()) {
        // Iniciar sesión automáticamente
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = 'student';
        header("Location: homepage-student.php");
        exit();
    } else {
        die("Error al crear la cuenta: " . $conn->error);
    }
}

// Proceso de login
if (isset($_POST['signIn'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT id, firstName, email, password, user_type FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    
    if ($sql->execute()) {
        $result = $sql->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // Verificar contraseña (compatible con MD5 y BCrypt)
            if (password_verify($password, $row['password']) || md5($password) === $row['password']) {
                // Migrar a BCrypt si estaba en MD5
                if (!password_needs_rehash($row['password'], PASSWORD_BCRYPT)) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->bind_param("si", $newHash, $row['id']);
                    $update->execute();
                }
                
                // Configurar sesión
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_type'] = $row['user_type'];
                $_SESSION['firstName'] = $row['firstName'];
                
                // Redirección según tipo de usuario
                $redirect = "homepage-{$row['user_type']}.php";
                if (!file_exists($redirect)) {
                    $redirect = "index.php";
                }
                
                header("Location: $redirect");
                exit();
            }
        }
    }
    
    // Mensaje genérico para evitar revelar información
    sleep(2); // Prevenir ataques de fuerza bruta
    die("Credenciales inválidas");
}

$conn->close();
?>