$(document).ready(function () {
    // Función para mostrar alerta
    function mostrarAlerta(productosConBajaCantidad) {
        let alertaHtml = `
            <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="alertModalLabel">Productos con baja cantidad</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul>
                                ${productosConBajaCantidad.map(producto => `<li>${producto.descripcion} (Cantidad: ${producto.stock})</li>`).join('')}
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('body').append(alertaHtml);
        $('#alertModal').modal('show');
    }

    // Función para cargar datos del inventario desde la API
    function cargarDatosInventario() {
        fetch('http://localhost:8080/api/productos')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener datos del inventario');
                }
                return response.json();
            })
            .then(data => {
                const tbody = $('#inventoryTable tbody');
                const productosConBajaCantidad = [];

                // Limpiar cualquier contenido previo en el tbody
                tbody.empty();

                // Iterar sobre los datos y construir las filas de la tabla
                data.forEach((producto, index) => {
                    if (producto.stock < 5) {
                        productosConBajaCantidad.push(producto);
                    }
                    const row = `
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>${producto.id}</td>
                            <td>${index + 1}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.codigo}</td>
                            <td>${producto.categoriaId}</td>
                            <td>${producto.stock}</td>
                            <td>${producto.marcaId}</td>
                            <td>${producto.precioCosto}</td>
                            <td>${producto.precioVenta}</td>
                            <td><img src="${producto.imgUrl}" alt="${producto.descripcion}" width="50"></td>
                            <td><button class="btn btn-warning btn-sm editButton">Edit</button></td>
                        </tr>
                    `;
                    tbody.append(row);
                });

                // Mostrar alerta si hay productos con baja cantidad
                if (productosConBajaCantidad.length > 0) {
                    mostrarAlerta(productosConBajaCantidad);
                }
            })
            .catch(error => {
                console.error('Error al cargar datos del inventario:', error);
            });
    }

    // Eliminar elementos seleccionados
    $('#deleteButton').click(function () {
        $('#inventoryTable tbody input[type="checkbox"]:checked').each(function () {
            let id = $(this).closest('tr').find('td:eq(1)').text(); // Obtener el ID del producto
            eliminarProducto(id); // Llamar a la función para eliminar el producto en el backend
            $(this).closest('tr').remove(); // Eliminar la fila de la tabla
        });
    });

    // Editar elementos
    $('#inventoryTable').on('click', '.editButton', function () {
        let id = $(this).closest('tr').find('td:eq(1)').text(); // Obtener el ID del producto
        cargarDatosEdicion(id); // Cargar datos del producto para edición
    });

    // Guardar cambios en la edición
    $('#saveChangesButton').click(function () {
        let id = $('#editId').val();
        let formData = {
            descripcion: $('#editName').val(),
            codigo: $('#editCode').val(),
            categoriaId: $('#editCategory').val(),
            stock: $('#editQuantity').val(),
            marcaId: $('#editSupplier').val(),
            precioCosto: $('#editCost').val(),
            precioVenta: $('#editSaleCost').val(),
            imgUrl: $('#editImgUrl').val()  // Asegúrate de que este campo coincida con el formulario de edición
        };

        // Realizar solicitud PUT al backend para actualizar el producto
        editarProducto(id, formData);
    });

    // Mostrar modal de creación al hacer clic en Agregar
    $('#btnAgregar').click(function () {
        $('#createModal').modal('show');
    });

    // Enviar formulario de creación al backend
    $('#createProductButton').click(function () {
        let formData = {
            descripcion: $('#nombre').val(),
            codigo: $('#codigo').val(),
            categoriaId: parseInt($('#categoriaId').val()),
            marcaId: parseInt($('#marcaId').val()),
            stock: parseInt($('#cantidad').val()),
            precioCosto: parseFloat($('#precioCosto').val()),
            precioVenta: parseFloat($('#precioVenta').val()),
            imgUrl: $('#imgUrl').val()  // Asegúrate de que este campo coincida con el formulario de creación
        };

        // Realizar la solicitud POST al backend para crear un nuevo producto
        crearProducto(formData);
    });

    // Función para cargar datos de un producto para edición
    function cargarDatosEdicion(id) {
        fetch(`http://localhost:8080/api/productos/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener datos del producto');
                }
                return response.json();
            })
            .then(data => {
                $('#editId').val(data.id);
                $('#editName').val(data.descripcion);
                $('#editCode').val(data.codigo);
                $('#editCategory').val(data.categoriaId);
                $('#editQuantity').val(data.stock);
                $('#editSupplier').val(data.marcaId);
                $('#editCost').val(data.precioCosto);
                $('#editSaleCost').val(data.precioVenta);
                $('#editImgUrl').val(data.imgUrl);  // Asegúrate de cargar el campo de imagen en el formulario de edición
                $('#editModal').modal('show');
            })
            .catch(error => {
                console.error('Error al cargar datos para edición:', error);
            });
    }

    // Función para editar un producto por ID en el backend
    function editarProducto(id, formData) {
        fetch(`http://localhost:8080/api/productos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al editar el producto');
                }
                return response.json();
            })
            .then(data => {
                console.log('Producto editado exitosamente:', data);
                // Actualizar la fila en la tabla con los nuevos datos
                $('#inventoryTable tbody tr').each(function () {
                    if ($(this).find('td:eq(1)').text() === id) {
                        $(this).find('td:eq(3)').text(formData.descripcion);
                        $(this).find('td:eq(4)').text(formData.codigo);
                        $(this).find('td:eq(5)').text(formData.categoriaId);
                        $(this).find('td:eq(6)').text(formData.stock);
                        $(this).find('td:eq(7)').text(formData.marcaId);
                        $(this).find('td:eq(8)').text(formData.precioCosto);
                        $(this).find('td:eq(9)').text(formData.precioVenta);
                        $(this).find('td:eq(10)').html(`<img src="${formData.imgUrl}" alt="${formData.descripcion}" width="50">`); // Actualizar también la imagen
                        return false; // Salir del bucle each
                    }
                });
                $('#editModal').modal('hide');
            })
            .catch(error => {
                console.error('Error al editar el producto:', error);
            });
    }

    // Función para crear un nuevo producto en el backend
    function crearProducto(formData) {
        fetch('http://localhost:8080/api/productos/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al crear el producto');
                }
                return response.json();
            })
            .then(data => {
                console.log('Producto creado exitosamente:', data);
                // Agregar la nueva fila a la tabla
                const newRow = `
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>${data.id}</td>
                        <td>${data.index}</td>
                        <td>${data.descripcion}</td>
                        <td>${data.codigo}</td>
                        <td>${data.categoriaId}</td>
                        <td>${data.stock}</td>
                        <td>${data.marcaId}</td>
                        <td>${data.precioCosto}</td>
                        <td>${precioVenta}</td>
                        <td><img src="${data.imgUrl}" alt="${data.descripcion}" width="50"></td>
                        <td><button class="btn btn-warning btn-sm editButton">Editar</button></td>
                    </tr>
                `;
                $('#inventoryTable tbody').append(newRow);
                $('#createModal').modal('hide');
            })
            .catch(error => {
                console.error('Error al crear el producto:', error);
            });
    }

    // Función para eliminar un producto por ID en el backend
    function eliminarProducto(id) {
        fetch(`http://localhost:8080/api/productos/${id}`, {
            method: 'DELETE',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al eliminar el producto');
                }
                console.log('Producto eliminado exitosamente');
            })
            .catch(error => {
                console.error('Error al eliminar el producto:', error);
            });
    }

    // Cargar inicialmente los datos del inventario al cargar la página
    cargarDatosInventario();

    // Actualizar los datos del inventario cada vez que se agregue, edite o elimine un producto
    $('#createProductButton, #saveChangesButton, #deleteButton').click(function () {
        cargarDatosInventario();
    });
});

