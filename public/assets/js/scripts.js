document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    const form = document.querySelector('#editForm');
    let currentCard = null;

    function cargarEmpleados() {
        const empleados = JSON.parse(localStorage.getItem('empleados')) || [];
        const contenedor = document.querySelector('.container .row');
        contenedor.innerHTML = ''; // Limpiar el contenedor antes de cargar empleados

        empleados.forEach(empleado => {
            const card = document.createElement('div');
            card.classList.add('col-md-4');
            card.innerHTML = `
                <div class="card mb-3">
                    <div class="card-header">${empleado.nombre}</div>
                    <div class="card-body">
                        <p>Rol: ${empleado.rol}</p>
                        <i class="fa fa-info-circle" data-bs-toggle="tooltip" title="
                            Celular: ${empleado.numeroCelular}<br>
                            Email: ${empleado.email}<br>
                            Cédula: ${empleado.cedula}<br>
                            Contraseña: ${empleado.contraseña}
                        "></i>
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" 
                                data-name="${empleado.nombre}" data-role="${empleado.rol}" data-celular="${empleado.numeroCelular}" 
                                data-email="${empleado.email}" data-cedula="${empleado.cedula}" data-contraseña="${empleado.contraseña}">
                                Editar
                            </button>
                            <a href="#" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            `;
            contenedor.appendChild(card);
        });

        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, { html: true });
        });

        // Asignar eventos de clic a los botones de editar y eliminar
        document.querySelectorAll('.btn-primary[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function (event) {
                currentCard = event.target.closest('.card');
                const name = event.target.getAttribute('data-name');
                const role = event.target.getAttribute('data-role');
                const celular = event.target.getAttribute('data-celular');
                const email = event.target.getAttribute('data-email');
                const cedula = event.target.getAttribute('data-cedula');
                const contraseña = event.target.getAttribute('data-contraseña');

                form.nombre.value = name;
                form.role.value = role;
                form.numeroCelular.value = celular;
                form.email.value = email;
                form.cedula.value = cedula;
                form.contraseña.value = contraseña;
                form.originalName.value = name;

                modal.show();
            });
        });

        document.querySelectorAll('.btn-danger').forEach(button => {
            button.removeEventListener('click', eliminarEmpleado);
            button.addEventListener('click', eliminarEmpleado);
        });
    }

    function eliminarEmpleado(event) {
        if (confirm('¿Estás seguro de que deseas eliminar este colaborador?')) {
            const card = event.target.closest('.card');
            const nombreEmpleado = card.querySelector('.card-header').innerText;

            // Eliminar la tarjeta del DOM
            card.remove();

            // Eliminar el empleado del almacenamiento local
            let empleados = JSON.parse(localStorage.getItem('empleados')) || [];
            empleados = empleados.filter(emp => emp.nombre !== nombreEmpleado);
            localStorage.setItem('empleados', JSON.stringify(empleados));

            // Volver a cargar empleados para refrescar la vista
            cargarEmpleados();
        }
    }

    // Cargar empleados almacenados al cargar la página
    cargarEmpleados();

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const name = form.nombre.value;
        const role = form.role.value;
        const celular = form.numeroCelular.value;
        const email = form.email.value;
        const cedula = form.cedula.value;
        const contraseña = form.contraseña.value;

        // Verifica si es una edición o un nuevo colaborador
        let empleados = JSON.parse(localStorage.getItem('empleados')) || [];
        const originalName = form.originalName.value;

        if (originalName) {
            empleados = empleados.map(emp => emp.nombre === originalName 
                ? { ...emp, nombre: name, rol: role, numeroCelular: celular, email: email, cedula: cedula, contraseña: contraseña } 
                : emp);
        } else {
            empleados.push({ nombre: name, rol: role, numeroCelular: celular, email: email, cedula: cedula, contraseña: contraseña });
        }

        localStorage.setItem('empleados', JSON.stringify(empleados));

        modal.hide();

        // Eliminar cualquier overlay restante
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        cargarEmpleados(); // Recargar los empleados para refrescar los datos
    });
});
