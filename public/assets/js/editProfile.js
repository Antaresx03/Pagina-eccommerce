$(document).ready(function () {
    const userName = localStorage.getItem('nombre');
    const userEmail = localStorage.getItem('correo');
    const userPhone = localStorage.getItem('telefono');
    const userCedula = localStorage.getItem('cedula');

    // Mostrar en inputs
    $('#nombre').val(userName);
    $('#telefono').val(userPhone);
    $('#cedula').val(userCedula);

    // Enviar formulario (se hará POST clásico al PHP)
    $('#editProfileForm').on('submit', function () {
        // Aquí no es necesario evitar el default porque el form sí debe enviarse
        // Solo actualizamos el localStorage
        localStorage.setItem('nombre', $('#nombre').val());
        localStorage.setItem('telefono', $('#telefono').val());
        localStorage.setItem('cedula', $('#cedula').val());
    });
});
