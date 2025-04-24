$(document).ready(function () {
    const requestOptions = {
        method: "GET",
        redirect: "follow"
    };

    // Obtener información del usuario desde localStorage
    const currentUserEmail = localStorage.getItem('correo');

    // Realizar la solicitud de empleados una vez al cargar la página
    fetch("http://localhost:8080/api/user/empleados", requestOptions)
        .then(response => response.json())
        .then(data => {
            const employeesContainer = $('#employeesContainer');

            // Función para actualizar la tarjeta del empleado
            function updateEmployeeCard(updatedEmployee) {
                const cardToUpdate = $(`#employeeCard-${updatedEmployee.id}`);
                cardToUpdate.find('.card-header').text(updatedEmployee.nombre);
                cardToUpdate.find('.card-body').html(`
                    <p><strong>Email:</strong> ${updatedEmployee.email}</p>
                    <p><strong>Teléfono:</strong> ${updatedEmployee.telefono}</p>
                    <p><strong>Rol:</strong> ${updatedEmployee.rol.length > 0 ? updatedEmployee.rol[0].roleNombre : 'Sin rol'}</p>
                    <p><strong>Estado:</strong> ${updatedEmployee.enabled ? 'Habilitado' : 'Deshabilitado'}</p>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                            data-name="${updatedEmployee.nombre}" data-role="${updatedEmployee.rol.length > 0 ? updatedEmployee.rol[0].roleNombre : ''}"
                            data-celular="${updatedEmployee.telefono}" data-email="${updatedEmployee.email}" data-cedula="${updatedEmployee.cedula}"
                            data-id="${updatedEmployee.id}">Editar</button>
                        <a href="#" class="btn btn-${updatedEmployee.enabled ? 'danger' : 'primary'} toggle-status-btn" data-id="${updatedEmployee.id}">
                            ${updatedEmployee.enabled ? 'Deshabilitar' : 'Habilitar'}
                        </a>
                    </div>
                `);
            }

            // Rellenar el modal de edición al hacer clic en Editar
            $('#editModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const name = button.data('name');
                const role = button.data('role');
                const celular = button.data('celular');
                const email = button.data('email');
                const cedula = button.data('cedula');
                const id = button.data('id'); // Obtener el ID del empleado

                const modal = $(this);
                modal.find('#nombre').val(name);
                modal.find('#role').val(role);
                modal.find('#numeroCelular').val(celular);
                modal.find('#email').val(email);
                modal.find('#cedula').val(cedula);
                modal.find('#id').val(id); // Establecer el ID en el campo oculto
                modal.find('#originalName').val(name);
            });

            // Enviar formulario de edición
            $('#editForm').submit(function (event) {
                event.preventDefault();
                const formData = $(this).serializeArray();
                const editedEmployee = {};
                formData.forEach(input => editedEmployee[input.name] = input.value);

                const requestOptions = {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(editedEmployee),
                    redirect: "follow"
                };

                fetch(`http://localhost:8080/api/user/UpdateEmpleados/${editedEmployee.id}`, requestOptions)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al actualizar empleado');
                        }
                        return response.json();
                    })
                    .then(updatedEmployee => {
                        // Actualizar la tarjeta del empleado
                        updateEmployeeCard(updatedEmployee);

                        // Mostrar alerta de éxito
                        alert("¡Empleado actualizado correctamente!");

                        // Cerrar modal
                        $('#editModal').modal('hide');
                    })
                    .catch(error => {
                        console.error('Error al actualizar empleado:', error);
                        // Mostrar alerta de error si es necesario
                        alert("Error al actualizar empleado.");
                    });
            });

            // Función para cambiar el estado del empleado (habilitar/deshabilitar)
            employeesContainer.on('click', '.toggle-status-btn', function (event) {
                event.preventDefault();
                const employeeId = $(this).data('id');
                const isEnabled = $(this).hasClass('btn-danger');
                const requestOptions = {
                    method: isEnabled ? "DELETE" : "PUT",
                    body: "",
                    redirect: "follow"
                };
                const endpoint = isEnabled ? `http://localhost:8080/api/user/empleados/${employeeId}` : `http://localhost:8080/api/user/empleados/${employeeId}/enable`;

                fetch(endpoint, requestOptions)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error al ${isEnabled ? 'deshabilitar' : 'habilitar'} empleado`);
                        }
                        return response.text();
                    })
                    .then(result => {
                        // Actualizar el estado del empleado en la tarjeta
                        const card = $(`#employeeCard-${employeeId}`);
                        const estadoText = card.find('.card-body p:contains("Estado:")');
                        estadoText.html(`<strong>Estado:</strong> ${isEnabled ? 'Deshabilitado' : 'Habilitado'}`);
                        const toggleBtn = card.find('.toggle-status-btn');
                        toggleBtn.toggleClass('btn-danger btn-primary').text(isEnabled ? 'Habilitar' : 'Deshabilitar');

                        // Mostrar alerta de éxito
                        alert(`¡Empleado ${isEnabled ? 'deshabilitado' : 'habilitado'} correctamente!`);
                    })
                    .catch(error => {
                        console.error(`Error al ${isEnabled ? 'deshabilitar' : 'habilitar'} empleado:`, error);
                        // Mostrar alerta de error si es necesario
                        alert(`Error al ${isEnabled ? 'deshabilitar' : 'habilitar'} empleado.`);
                    });
            });

            // Construir las cards de empleados, filtrando el usuario actual
            data.forEach(empleado => {
                // Validar si el correo del empleado es diferente al del usuario actual
                if (empleado.email !== currentUserEmail) {
                    const card = `
                        <div class="col-md-4" id="employeeCard-${empleado.id}">
                            <div class="card mb-3">
                                <div class="card-header">${empleado.nombre}</div>
                                <div class="card-body">
                                    <p><strong>Email:</strong> ${empleado.email}</p>
                                    <p><strong>Teléfono:</strong> ${empleado.telefono}</p>
                                    <p><strong>Rol:</strong> ${empleado.rol.length > 0 ? empleado.rol[0].roleNombre : 'Sin rol'}</p>
                                    <p><strong>Estado:</strong> ${empleado.enabled ? 'Habilitado' : 'Deshabilitado'}</p>
                                    <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-name="${empleado.nombre}" data-role="${empleado.rol.length > 0 ? empleado.rol[0].roleNombre : ''}"
                                            data-celular="${empleado.telefono}" data-email="${empleado.email}" data-cedula="${empleado.cedula}"
                                            data-id="${empleado.id}">Editar</button>
                                        <a href="#" class="btn btn-${empleado.enabled ? 'danger' : 'primary'} toggle-status-btn" data-id="${empleado.id}">
                                            ${empleado.enabled ? 'Deshabilitar' : 'Habilitar'}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    employeesContainer.append(card);
                }
            });
        })
        .catch(error => console.error("Error al obtener empleados:", error));
});