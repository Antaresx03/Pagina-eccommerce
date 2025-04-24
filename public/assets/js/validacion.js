// Function to log out the user
function cerrarSesion() {
    mostrarLoader(); // Show loader before logout process

    // Remove tokens from localStorage
    localStorage.removeItem('accessToken');
    localStorage.removeItem('idRole');
    localStorage.removeItem('idUser');
    localStorage.removeItem('nombre');
    localStorage.removeItem('correo');
    localStorage.removeItem('cedula');
    localStorage.removeItem('telefono');

    // Redirect to login page after a delay
    setTimeout(() => {
        ocultarLoader(); // Hide loader after logout process
        redireccionar('../Frontend/LoginMRM.html'); // Adjust path if necessary
    }, 1000); // Adjust delay time as needed
}

// DOMContentLoaded event listener to attach logout functionality
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('cerrarSesion').addEventListener('click', cerrarSesion);

    //document.addEventListener('DOMContentLoaded', () => {
    // Obtener valores del localStorage
    const nombre = localStorage.getItem('nombre') || 'NOMBRE de usuario no encontrado';
    const email = localStorage.getItem('correo') || 'correo no encontrado';
    const cedula = localStorage.getItem('cedula') || 'cedula no encontrada';
    const telefono = localStorage.getItem('telefono') || 'telefono no encontrado';
    console.log('Datos recibidos:');
    console.log('Datos guardados:');
    console.log(localStorage.getItem('idUser'));
    console.log(nombre);
    console.log(email);
    console.log(cedula);
    console.log(telefono);
    // Actualizar el contenido de los elementos en el DOM
    document.getElementById('userName').textContent = 'Bienvenido, Sr ' + nombre;
    document.getElementById('userEmail').textContent = email;
    document.getElementById('userNameInput').value = nombre;
    document.getElementById('userEmailInput').value = email;
    document.getElementById('userPhoneInput').value = telefono;

});





// Función autoejecutable asíncrona para validar el token y el rol antes de cargar la página
(async function () {
    try {
        mostrarLoader(); // Mostrar el loader al inicio de la validación

        const paginaActual = window.location.pathname.split('/').pop();
        const paginasSinValidacion = [
            'LoginMRM.html',
            'Recuperarcontraseña.html',
            'Registro.html',
            'validacion.html'
        ];

        const tokenValido = await validarToken();

        if (paginasSinValidacion.includes(paginaActual)) {
            if (tokenValido) {
                const idRole = localStorage.getItem('idRole');
                if (idRole) {
                    ocultarLoader(); // Ocultar loader antes de redirigir
                    redireccionarPaginaInicio(idRole); // Redirigir si hay token y rol definido
                } else {
                    ocultarLoader(); // Ocultar loader antes de redirigir
                    redireccionar('/Frontend/LoginMRM.html'); // Redirigir al login si no hay rol definido
                }
            } else if (paginaActual === 'validacion.html') {
                const idRole = localStorage.getItem('idRole');
                if (idRole) {
                    setTimeout(() => {
                        ocultarLoader(); // Ocultar loader antes de redirigir
                        redireccionarPaginaInicio(idRole); // Redirigir después de 2 segundos si hay rol
                    }, 2000);
                } else {
                    ocultarLoader(); // Ocultar loader antes de redirigir
                    redireccionar('/Frontend/LoginMRM.html'); // Redirigir al login si no hay rol definido
                }
            } else {
                ocultarLoader(); // Ocultar el loader si la página no requiere validación
            }
            return;
        }

        /*  if (!tokenValido) {
              console.log('Token inválido o no encontrado, redirigiendo a Login.html');
              ocultarLoader(); // Ocultar loader antes de redirigir
              redireccionar('/Frontend/LoginMRM.html'); // Redirigir al login si el token es inválido
              return;
          }*/

        const idRole = localStorage.getItem('idRole');
        if (!idRole) {
            console.log('Rol de usuario no encontrado, redirigiendo a Login.html');
            ocultarLoader(); // Ocultar loader antes de redirigir
            redireccionar('/Frontend/LoginMRM.html'); // Redirigir al login si no hay rol definido
            return;
        }

        const vistasPermitidas = {
            'Crearcolaboradores.html': [1],
            'empleados.html': [1],
            'Ventas.html': [1, 2],
            'Vistainventarioadmin.html': [1],
            'Vistainventarioauxiliar.html': [2],
            'perfilEmpl.html': [1, 2, 3]
        };

        if (!vistasPermitidas[paginaActual] || !vistasPermitidas[paginaActual].includes(parseInt(idRole))) {
            console.log('Rol no permitido para esta página, redirigiendo a validacion.html');

            if (paginaActual !== 'validacion.html') {
                ocultarLoader(); // Ocultar loader antes de redirigir
                redireccionar('../Frontend/validacion.html'); // Redirigir a validacion.html si el rol no está permitido
            } else {
                mostrarMensaje('Acceso no autorizado. Redirigiendo a Login...');
                setTimeout(() => {
                    ocultarLoader(); // Ocultar loader antes de redirigir
                    redireccionar('../Frontend/LoginMRM.html'); // Redirigir al login después de 2 segundos desde validacion.html
                }, 2000);
            }
            return;
        }

        console.log('Validación exitosa, cargando contenido de la página...');
        ocultarLoader(); // Ocultar loader después de cargar contenido
        cargarContenido(); // Cargar el contenido si la validación es exitosa

    } catch (error) {
        console.error('Error en la validación:', error.message);
        mostrarMensaje('Error en la validación: ' + error.message);
        ocultarLoader(); // Ocultar loader en caso de error
        redireccionar('../Frontend/LoginMRM.html'); // Redirigir al login en caso de error durante la validación
    }
})();

/**
 * Función para redirigir a una URL específica.
 * @param {string} url - URL a la cual redirigir.
 */
function redireccionar(url) {
    window.location.href = url;
}

/**
 * Función para redirigir a la página de inicio correspondiente según el rol.
 * @param {number} idRole - ID del rol del usuario.
 */
function redireccionarPaginaInicio(idRole) {
    const paginasInicio = {
        1: '../Frontend/Vistainventarioadmin.html',
        2: '../Frontend/Vistainventarioauxiliar.html'
        // Agregar más roles y sus páginas de inicio si es necesario
    };

    const url = paginasInicio[idRole] || '../Frontend/LoginMRM.html';
    redireccionar(url);
}

/**
 * Función para mostrar un mensaje de error en la página.
 * @param {string} mensaje - Mensaje de error a mostrar.
 */
function mostrarMensaje(mensaje) {
    eliminarMensajesPrevios();
    const mensajeElemento = document.createElement('div');
    mensajeElemento.id = 'mensaje-error';
    mensajeElemento.textContent = mensaje;
    mensajeElemento.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; background-color: #f44336; color: white; padding: 10px; text-align: center; z-index: 1000;';
    document.body.appendChild(mensajeElemento);

    // Ocultar el mensaje después de 2 segundos
    setTimeout(() => {
        mensajeElemento.remove();
    }, 2000);
}

/**
 * Función para eliminar mensajes previos.
 */
function eliminarMensajesPrevios() {
    const mensajePrevio = document.getElementById('mensaje-error');
    if (mensajePrevio) {
        mensajePrevio.remove();
    }
}

/**
 * Función para validar el token.
 * @returns {boolean} True si el token es válido, false si no lo es.
 */
async function validarToken() {
    const accessToken = localStorage.getItem('accessToken');
    if (!accessToken) {
        return false;
    }

    try {
        const payload = decodificarToken(accessToken);
        const exp = payload.exp;
        const now = Math.floor(Date.now() / 1000);

        if (exp < now) {
            console.log('Token ha expirado');
            return false;
        }

        return true;
    } catch (error) {
        console.error('Error al validar el token:', error);
        return false;
    }
}

/**
 * Función para cargar el contenido de la página después de la validación.
 * Esta función simula cargar contenido en la consola.
 */
function cargarContenido() {
    console.log('Cargando contenido de la página...');
}

/**
 * Función para mostrar un loader mientras se valida el token.
 */
function mostrarLoader() {
    eliminarLoaderPrevio(); // Elimina cualquier loader previo antes de mostrar uno nuevo

    const loader = document.createElement('div');
    loader.id = 'loader';
    loader.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); display: flex; align-items: center; justify-content: center; z-index: 1000;';
    loader.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(loader);
}

function ocultarLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.remove(); // Elimina el loader si existe
    }
}

function eliminarLoaderPrevio() {
    const loaderPrevio = document.getElementById('loader');
    if (loaderPrevio) {
        loaderPrevio.remove(); // Elimina cualquier loader previo
    }
}

/**
 * Función para decodificar un token JWT.
 * @param {string} token - El token JWT a decodificar.
 * @returns {object} - El payload del token decodificado.
 */
function decodificarToken(token) {
    const payloadBase64 = token.split('.')[1];
    const decodedPayload = atob(payloadBase64);
    return JSON.parse(decodedPayload);
}