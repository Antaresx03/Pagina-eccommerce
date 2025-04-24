// spinner.js

// Función para mostrar el spinner
function showSpinner() {
    const spinnerContainer = document.getElementById('spinnerContainer');
    if (spinnerContainer) {
        spinnerContainer.style.display = 'flex';
    }
}

// Función para ocultar el spinner
function hideSpinner() {
    const spinnerContainer = document.getElementById('spinnerContainer');
    if (spinnerContainer) {
        spinnerContainer.style.display = 'none';
    }
}

// Función para añadir el spinner al inicio del body
function addSpinnerToBody() {
    const spinnerHTML = `
        <div class="spinner-container" id="spinnerContainer">
            <div class="spinner"></div>
        </div>
    `;

    document.body.insertAdjacentHTML('afterbegin', spinnerHTML);
}

// Añadir el spinner al cargar la página
document.addEventListener('DOMContentLoaded', addSpinnerToBody);
