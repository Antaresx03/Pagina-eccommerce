//Inicio de sesion cliente
document.addEventListener('DOMContentLoaded', function () {
    // Selecciona el formulario y los elementos de entrada
    const formulario = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Añade un evento de envío al formulario
    formulario.addEventListener('submit', function (event) {
        event.preventDefault();

        // Valida los campos del formulario
        if (emailInput.value.trim() === '' || passwordInput.value.trim() === '') {
            alert('Por favor, completa todos los campos.');
            return;
        }

        // Si todos los campos están llenos, realiza la acción deseada (puede ser enviar a un servidor o mostrar una alerta)
        //alert('Inicio de sesión exitoso');

        // Limpia los campos del formulario
        formulario.reset();
    });
});
function togglePassword() {
    var passwordField = document.getElementById("password");
    var passwordFieldType = passwordField.getAttribute("type");
    if (passwordFieldType === "password") {
        passwordField.setAttribute("type", "text");
    } else {
        passwordField.setAttribute("type", "password");
    }
}
