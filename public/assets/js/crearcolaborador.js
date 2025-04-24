document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.registroForm_Empl'); // Selector genérico para el formulario

    if (form) { // Verificar si se encontró el formulario
        form.addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevenir el envío por defecto del formulario

            // Obtener valores de los campos del formulario
            const nombre = document.getElementById('nombre').value;
            const telefono = document.getElementById('telefono').value;
            const rol = "COLABORADOR";
            const email = document.getElementById('email').value;
            const cedula = document.getElementById('cedula').value;
            const contrasena = document.getElementById('password').value;

            // Validar que todos los campos requeridos estén llenos
            if (!nombre || !telefono || !rol || !email || !cedula || !contrasena) {
                alert('Por favor, complete todos los campos.');
                return;
            }

            // Mostrar el JSON a enviar por consola para verificar
            mostrarJSON(nombre, email, contrasena, telefono, cedula, rol);

            const myHeaders = new Headers();
            myHeaders.append("Content-Type", "application/json");

            // Construir el objeto JSON a enviar
            const raw = JSON.stringify({
                "nombre": nombre,
                "username": email,
                "password": contrasena,
                "telefono": telefono,
                "tipoUser": "EMPLEADO",
                "cedula": parseInt(cedula),
                "roleName": rol
            });

            const requestOptions = {
                method: "POST",
                headers: myHeaders,
                body: raw,
                redirect: "follow"
            };

            try {
                // Realizar la solicitud fetch a la API
                const response = await fetch("http://localhost:8080/api/auth/register", requestOptions);

                if (!response.ok) {
                    if (response.status === 400) {
                        const errorMessage = await response.text();
                        throw new Error(errorMessage);
                    } else {
                        throw new Error('La solicitud no pudo completarse correctamente: ' + response.status + ' ' + response.statusText);
                    }
                }

                alert('¡Cuenta de EMPLEADO creada exitosamente!');

                // Redireccionar a la página de inicio de sesión
                form.reset();

            } catch (error) {
                console.error('Error en el registro:', error.message);
                alert(error.message);
            } finally {

            }
        });
    } else {
        console.error('No se encontró el formulario con clase "registroForm_Empl".');
    }
});

// Función para mostrar el JSON que se enviará por consola
function mostrarJSON(nombre, email, contrasena, telefono, cedula, rol) {
    const json = {
        "nombre": nombre,
        "username": email,
        "password": contrasena,
        "telefono": telefono,
        "tipoUser": "EMPLEADO",
        "cedula": parseInt(cedula),
        "roleName": rol
    };
    console.log('JSON a enviar:', json);
}


// Manejo del cambio de selección en el select
const selectElement = document.getElementById('rol'); // Reemplaza 'tuSelectId' con el ID real de tu select

selectElement.addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const selectedValue = selectedOption.value;
    const selectedText = selectedOption.textContent;

    console.log('Valor seleccionado:', selectedValue);
    console.log('Texto seleccionado:', selectedText);
});
