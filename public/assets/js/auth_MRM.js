// Función para mostrar el loader dinámicamente
function mostrarLoader() {
    // Crear el div del loader
    // Crear el div del loader
    const loader = document.createElement('div');
    loader.id = 'loader';
    loader.style.position = 'fixed';
    loader.style.top = '0';
    loader.style.left = '0';
    loader.style.width = '100%';
    loader.style.height = '100%';
    loader.style.backgroundColor = 'rgba(0, 0, 0, 0.5)'; // Fondo semitransparente
    loader.style.display = 'flex';
    loader.style.alignItems = 'center';
    loader.style.justifyContent = 'center';
    loader.style.zIndex = '1000';
    loader.innerHTML = '<div class="spinner"></div>'; // Añadir el spinner dentro del loader

    // Crear el spinner dentro del loader
    const spinner = document.createElement('div');
    spinner.classList.add('spinner'); // Asume que ya tienes estilos para la clase 'spinner' en tu CSS

    // Añadir el spinner al loader
    loader.appendChild(spinner);

    // Añadir el loader al body
    document.body.appendChild(loader);

}

// Función para ocultar y eliminar el loader
function ocultarLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.remove();
    }
}


// Escuchar el evento de envío del formulario de inicio de sesión
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();  // Evitar el envío automático del formulario

    mostrarLoader();
    // Obtener los valores de email y contraseña del formulario
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    console.log('Iniciando sesión con:', email, password);

    // Configurar los datos de la solicitud POST
    const requestOptions = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "username": email,
            "password": password
        })
    };

    try {
        console.log('Enviando solicitud a la API...');
        const response = await fetch("http://localhost:8080/api/auth/login", requestOptions);
        console.log('Respuesta de la API:', response);

        // Verificar si la respuesta es exitosa (código de estado HTTP 200-299)
        if (!response.ok) {
            console.error('Error en la respuesta de la API:', response.status, response.statusText);
            throw new Error('Error en el inicio de sesión');
        }

        // Extraer los datos JSON de la respuesta
        const data = await response.json();
        console.log('Datos recibidos:', data);  // Mostrar datos recibidos en la consola

        // Verificar si se recibió un accessToken en la respuesta
        if (data.accessToken) {
            // Almacenar el token en localStorage
            localStorage.setItem('accessToken', data.tokenType + data.accessToken);
            console.log('Token almacenado en localStorage');

            //Guardar informacion del usuario en localStorage
            localStorage.setItem('idUser', data.idUser);
            localStorage.setItem('nombre', data.nombre);
            localStorage.setItem('correo', data.correo);
            localStorage.setItem('cedula', data.cedula);
            localStorage.setItem('telefono', data.telefono);

            // Guardar el idRole en localStorage
            const idRole = data.rol[0].idRole; // Suponiendo que siempre hay un único rol
            localStorage.setItem('idRole', idRole);
            console.log('ID de Rol almacenado en localStorage:', idRole);

            // Redirigir según el tipo de usuario y rol obtenidos
            if (data.userType === 'EMPLEADO') {
                if (idRole === 1) {
                    console.log('Redirigiendo a BienvenidoAdm.html...');
                    console.log('Datos recibidos:', data);
                    console.log('ID DE USUARIO RECIBIDO:', data.idUser);
                    window.location.href = '../Frontend/Vistainventarioadmin.html';  // Redirigir a la página de administrador
                } else if (idRole === 2) {
                    console.log('Redirigiendo a BienvenidoEmpleado.html...');
                    window.location.href = '../Frontend/Vistainventarioauxiliar.html';  // Redirigir a la página de empleado
                } else {
                    console.warn('Rol desconocido:', data.rol);
                    // Manejar el rol desconocido según tu lógica
                    // Por ejemplo:
                    // window.location.href = '../Frontend/ErrorPage.html';
                }
            } else {
                console.warn('Tipo de usuario desconocido:', data.userType);
                console.log('TIPO USUARIO INCORRECTO');
                document.getElementById('loginMessage').innerText = 'ESTE LOGIN ES VÁLIDO SOLO PARA PERSONAL DE MRM';
                localStorage.removeItem('accessToken');
                localStorage.removeItem('idRole');
                localStorage.removeItem('idUser');
                // Manejar el tipo de usuario desconocido según tu lógica
                // Por ejemplo:
                // window.location.href = '../Frontend/ErrorPage.html';
            }
        } else {
            console.error('Token no encontrado en la respuesta:', data);
            throw new Error('Token de acceso no recibido');
        }

    } catch (error) {
        console.error('Error en el inicio de sesión:', error.message);
        document.getElementById('loginMessage').innerText = error.message + ' DATOS INVALIDOS  INTENTE NUEVAMENTE';
    } finally {
        // Ocultar el loader al finalizar el proceso
        ocultarLoader();
    }
});
