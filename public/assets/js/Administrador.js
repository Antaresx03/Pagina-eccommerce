document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            alert('Por favor, complete todos los campos.');
            return;
        }

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
