document.addEventListener("DOMContentLoaded", () => {
    const tbody = document.querySelector("#inventoryTable tbody");
    const createBtn = document.getElementById("createProductButton");
    const editBtn = document.getElementById("saveChangesButton");
    const deleteBtn = document.getElementById("deleteButton");

    function cargarProductos() {
        
        fetch("../controllers/ProductoController.php", {
            method: "POST",
            body: new URLSearchParams({ action: "listar" })
        })
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = "";
            // Dentro de la función cargarProductos()
data.forEach((p, index) => {
    const stockBajoClass = p.cantidad < 10 ? 'table-danger font-weight-bold' : '';
    tbody.innerHTML += `
        <tr class="${stockBajoClass}">
            <td><input type="checkbox" class="delete-check" value="${p.id}"></td>
            <td>${p.id}</td>
            <td>${index + 1}</td>
            <td>${p.nombre}</td>
            <td>${p.codigo}</td>
            <td>${p.categoria}</td>
            <td>${p.cantidad}</td>
            <td>${p.marca}</td>
            <td>${p.precio_costo}</td>
            <td>${p.precio_venta}</td>
            <td><img src="${p.imagen}" width="50"></td>
            <td><button class="btn btn-primary btn-sm editar-btn" data-product='${JSON.stringify(p)}'>Editar</button></td>
        </tr>
    `;
});


            document.querySelectorAll(".editar-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const p = JSON.parse(btn.dataset.product);
                    document.getElementById("editId").value = p.id;
                    document.getElementById("editName").value = p.nombre;
                    document.getElementById("editCode").value = p.codigo;
                    document.getElementById("editCategory").value = p.categoria;
                    document.getElementById("editQuantity").value = p.cantidad;
                    document.getElementById("editSupplier").value = p.marca;
                    document.getElementById("editCost").value = p.precio_costo;
                    document.getElementById("editSaleCost").value = p.precio_venta;
                    document.getElementById("editImgUrl").value = p.imagen;

                    // Asegura que el select tenga la categoría correcta seleccionada
                    const categoriaSelect = document.getElementById("editCategory");
                    for (let i = 0; i < categoriaSelect.options.length; i++) {
                        if (categoriaSelect.options[i].value === p.categoria) {
                            categoriaSelect.selectedIndex = i;
                            break;
                        }
                    }

                    $('#editModal').modal('show');
                });
            });
        })
        .catch(err => console.error("❌ Error cargando productos:", err));
    }

    createBtn.addEventListener("click", () => {
        const data = {
            action: "agregar",
            nombre: document.getElementById("nombre").value,
            codigo: document.getElementById("codigo").value,
            categoria: document.getElementById("categoriaId").value,
            cantidad: document.getElementById("cantidad").value,
            marca: document.getElementById("marcaId").value,
            precio_costo: document.getElementById("precioCosto").value,
            precio_venta: document.getElementById("precioVenta").value,
            tipo: document.getElementById("tipoId").value,
            imagen: document.getElementById("imgUrl").value
        };

        fetch("../controllers/ProductoController.php", {
            method: "POST",
            body: new URLSearchParams(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert("✅ Producto agregado correctamente.");
                $('#createModal').modal('hide');
                cargarProductos();
            } else {
                alert("❌ Error al agregar el producto.");
                console.error("Resultado del backend:", result);
            }
        })
        .catch(err => {
            alert("❌ Error en la petición");
            console.error("Error:", err);
        });
    });

    editBtn.addEventListener("click", () => {
        const data = {
            action: "editar",
            id: document.getElementById("editId").value,
            nombre: document.getElementById("editName").value,
            codigo: document.getElementById("editCode").value,
            categoria: document.getElementById("editCategory").value,
            cantidad: document.getElementById("editQuantity").value,
            marca: document.getElementById("editSupplier").value,
            precio_costo: document.getElementById("editCost").value,
            precio_venta: document.getElementById("editSaleCost").value,
            imagen: document.getElementById("editImgUrl").value,
            tipo: "general"
        };

        fetch("../controllers/ProductoController.php", {
            method: "POST",
            body: new URLSearchParams(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert("✅ Producto editado correctamente.");
                $('#editModal').modal('hide');
                cargarProductos();
            } else {
                alert("❌ Error al editar el producto.");
            }
        })
        .catch(err => console.error("❌ Error editando producto:", err));
    });

    deleteBtn.addEventListener("click", () => {
        const checks = document.querySelectorAll(".delete-check:checked");
        const formData = new URLSearchParams();
        formData.append("action", "eliminar");

        checks.forEach(c => {
            formData.append("ids[]", c.value);
        });

        if (checks.length === 0) return alert("Selecciona al menos un producto para eliminar.");

        fetch("../controllers/ProductoController.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                alert("🗑️ Productos eliminados correctamente.");
                cargarProductos();
            } else {
                alert("❌ Error al eliminar productos.");
            }
        })
        .catch(err => console.error("❌ Error al eliminar productos:", err));
    });

    cargarProductos();
});
