const signUpButton = document.getElementById('signUpButton');
const signInButton = document.getElementById('signInButton');
const signInForm = document.getElementById('signIn');
const signUpForm = document.getElementById('signup');

// Función para mostrar formulario con animación
function showForm(form) {
    if (form === 'signup') {
        signInForm.style.display = "none";
        signUpForm.style.display = "block";
        signUpForm.classList.add('form-animation');
        setTimeout(() => signUpForm.classList.remove('form-animation'), 500);
    } else {
        signUpForm.style.display = "none";
        signInForm.style.display = "block";
        signInForm.classList.add('form-animation');
        setTimeout(() => signInForm.classList.remove('form-animation'), 500);
    }
}

// Event listeners
signUpButton.addEventListener('click', function () {
    showForm('signup');
});

signInButton.addEventListener('click', function () {
    showForm('signin');
});

// Validación de formularios
document.getElementById('registerForm').addEventListener('submit', function (e) {
    let valid = true;

    // Validar nombre
    const fName = document.getElementById('fName');
    if (!fName.value.trim()) {
        document.getElementById('fNameError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('fNameError').style.display = 'none';
    }

    // Validar apellido
    const lName = document.getElementById('lName');
    if (!lName.value.trim()) {
        document.getElementById('lNameError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('lNameError').style.display = 'none';
    }

    // Validar email
    const regEmail = document.getElementById('regEmail');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(regEmail.value)) {
        document.getElementById('regEmailError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('regEmailError').style.display = 'none';
    }

    // Validar contraseña
    const regPassword = document.getElementById('regPassword');
    if (regPassword.value.length < 8) {
        document.getElementById('regPasswordError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('regPasswordError').style.display = 'none';
    }

    // Validar semestre
    const semestre = document.getElementById('semestre');
    if (!semestre.value) {
        document.getElementById('semestreError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('semestreError').style.display = 'none';
    }

    if (!valid) e.preventDefault();
});

document.getElementById('loginForm').addEventListener('submit', function (e) {
    let valid = true;

    // Validar email
    const loginEmail = document.getElementById('loginEmail');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(loginEmail.value)) {
        document.getElementById('loginEmailError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('loginEmailError').style.display = 'none';
    }

    // Validar contraseña
    const loginPassword = document.getElementById('loginPassword');
    if (!loginPassword.value) {
        document.getElementById('loginPasswordError').style.display = 'block';
        valid = false;
    } else {
        document.getElementById('loginPasswordError').style.display = 'none';
    }

    if (!valid) e.preventDefault();
});

// Resetear mensajes de error al cambiar de formulario
signUpButton.addEventListener('click', function () {
    document.querySelectorAll('.error-message').forEach(el => {
        el.style.display = 'none';
    });
});

signInButton.addEventListener('click', function () {
    document.querySelectorAll('.error-message').forEach(el => {
        el.style.display = 'none';
    });
});