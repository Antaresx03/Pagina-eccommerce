<!-- Modal: Agregar Producto -->
<div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="controllers/agregar_producto.php" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Código</label>
            <input type="text" name="codigo" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Categoría</label>
            <input type="text" name="categoria" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Cantidad</label>
            <input type="number" name="cantidad" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Marca</label>
            <input type="text" name="marca" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Precio Costo</label>
            <input type="number" step="0.01" name="precio_costo" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Precio Venta</label>
            <input type="number" step="0.01" name="precio_venta" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Tipo</label>
            <input type="text" name="tipo" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Imagen</label>
            <input type="file" name="imagen" class="form-control">
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Agregar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Eliminar Producto -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="controllers/eliminar_producto.php" method="POST">
      <input type="hidden" name="id" id="deleteId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar Producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ¿Estás seguro que deseas eliminar este producto?
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Eliminar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>
