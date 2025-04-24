// Escuchar evento del formulario correctamente
document.querySelector('#changePasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const claveAct = document.getElementById('contraseña_actual').value;
    const nuevaClave = document.getElementById('nueva_contraseña').value;
    const confirmClave = document.getElementById('confirmar_contraseña').value;

    if (claveAct === nuevaClave) {
        alert("⚠ La nueva contraseña debe ser diferente a la actual.");
        return;
    }

    if (nuevaClave !== confirmClave) {
        alert("❌ Las contraseñas no coinciden.");
        return;
    }

    $.ajax({
        url: 'cambiar_contraseña.php',
        method: 'POST',
        data: {
            contraseña_actual: claveAct,
            nueva_contraseña: nuevaClave,
            confirmar_contraseña: confirmClave
        },
        success: function (response) {
            $('#changePasswordModal').modal('hide');
            alert('✅ Contraseña actualizada correctamente.');
        },
        error: function () {
            alert('❌ Error al conectar con el servidor.');
        }
    });
});

function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}
