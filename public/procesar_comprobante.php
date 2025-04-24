<?php
require_once '../database/database.php';
session_start();

$conn = Database::conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orden_id'], $_POST['accion'])) {
    $ordenId = $_POST['orden_id'];
    $accion = $_POST['accion'];

    if ($accion === 'aprobar') {
        $nuevoEstado = 'aprobado';
    } elseif ($accion === 'rechazar') {
        $nuevoEstado = 'rechazado';
    } else {
        $_SESSION['mensaje_estado'] = '❌ Acción inválida.';
        header("Location: Ventas.php");
        exit();
    }

    // Actualizar estado de la orden
    $stmt = $conn->prepare("UPDATE ordenes SET estado = ? WHERE id = ?");
    if ($stmt->execute([$nuevoEstado, $ordenId])) {
        $_SESSION['mensaje_estado'] = '✅ Estado actualizado con éxito.';

        // Obtener datos de la orden (productos, correo y nombre del cliente)
        $orden = $conn->prepare("SELECT nombre_cliente, email, productos FROM ordenes WHERE id = ?");
        $orden->execute([$ordenId]);
        $datos = $orden->fetch(PDO::FETCH_ASSOC);

        if ($datos) {
            $clienteEmail = $datos['email'];
            $clienteNombre = $datos['nombre_cliente'];
            $productosComprados = json_decode($datos['productos'], true); // array de productos con name y quantity

            // ✏️ Descontar del inventario y eliminar si llega a 0
            if ($nuevoEstado === 'aprobado' && is_array($productosComprados)) {
                foreach ($productosComprados as $producto) {
                    $nombre = $producto['name'];
                    $cantidadVendida = intval($producto['quantity']);

                    // Actualizar cantidad en inventario
                    $actualizar = $conn->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE nombre = ?");
                    $actualizar->execute([$cantidadVendida, $nombre]);

                    // Verificar si quedó en 0
                    $verificar = $conn->prepare("SELECT id, cantidad FROM productos WHERE nombre = ? LIMIT 1");
                    $verificar->execute([$nombre]);
                    $productoInfo = $verificar->fetch(PDO::FETCH_ASSOC);

                    if ($productoInfo && intval($productoInfo['cantidad']) <= 0) {
                        $eliminar = $conn->prepare("DELETE FROM productos WHERE id = ?");
                        $eliminar->execute([$productoInfo['id']]);
                    }
                }
            }

            // ✉️ Enviar notificación por correo
            $asunto = "Estado de tu compra en MotoRepuestos Melo";
            $mensaje = "Hola $clienteNombre,\n\n";

            if ($nuevoEstado === 'aprobado') {
                $mensaje .= "✅ Tu compra ha sido APROBADA. Muchas gracias por confiar en nosotros.\n";
                $_SESSION['notificacion'] = '✅ Tu compra ha sido aprobada.';
            } elseif ($nuevoEstado === 'rechazado') {
                $mensaje .= "❌ Tu compra fue RECHAZADA. Puedes volver a intentarlo subiendo nuevamente el comprobante.\n";
                $_SESSION['notificacion'] = '❌ Tu compra fue rechazada. Puedes volver a intentarlo.';
            }

            $mensaje .= "\nPuedes ver el detalle ingresando a tu cuenta.\n\nAtentamente,\nMotoRepuestos Melo";

            $headers = "From: noreply@motorepuestosmelo.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8";

            mail($clienteEmail, $asunto, $mensaje, $headers);
        }

    } else {
        $_SESSION['mensaje_estado'] = '❌ Error al actualizar el estado.';
    }

    header("Location: Ventas.php");
    exit();
}
?>
