document.addEventListener("DOMContentLoaded", function () {
    const rol = localStorage.getItem("idRole");
    console.log("Rol obtenido del localStorage: " + rol);

    // Mostrar el sidebar correspondiente según el rol
    if (rol === "1") {
        document.getElementById("sidebar-admin").style.display = "flex";
        initializeSidebarEvents('cerrarSesionAdmin');
    } else if (rol === "2") {
        document.getElementById("sidebar-auxiliar").style.display = "flex";
        initializeSidebarEvents('cerrarSesionAdmin');
    }
});

function initializeSidebarEvents(cerrarSesionId) {
    const cerrarSesionBtn = document.getElementById(cerrarSesionId);
    if (cerrarSesionBtn) {
        cerrarSesionBtn.addEventListener('click', cerrarSesion);
    }
}

// Function to log out the user
function cerrarSesion() {
    mostrarLoader(); // Show loader before logout process

    // Remove tokens from localStorage
    localStorage.removeItem('accessToken');
    localStorage.removeItem('idRole');
    localStorage.removeItem('nombre');
    localStorage.removeItem('correo');
    localStorage.removeItem('cedula');
    localStorage.removeItem('telefono');

    // Redirect to login page after a delay
    setTimeout(() => {
        ocultarLoader(); // Hide loader after logout process
        redireccionar('../Frontend/loginMRM.html'); // Adjust path if necessary
    }, 1000); // Adjust delay time as needed
}

// Function to redirect to a specific URL
function redireccionar(url) {
    window.location.href = url;
}

// Functions to show and hide the loader
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

    // Función para cargar los datos del usuario en el modal de editar perfil
    function cargarDatosUsuarioEnModal() {
        const nombre = localStorage.getItem('nombre');
        const correo = localStorage.getItem('correo');
        const telefono = localStorage.getItem('telefono');

        document.getElementById('userNameInput').value = nombre;
        document.getElementById('userEmailInput').value = correo;
        document.getElementById('userPhoneInput').value = telefono;
    }

    // Escuchar evento para cargar los datos del usuario al abrir el modal de editar perfil
    $('#editarPerfilModal').on('show.bs.modal', function (event) {
        cargarDatosUsuarioEnModal();
    });
}