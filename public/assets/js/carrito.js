document.addEventListener('DOMContentLoaded', function () {
    // Obtener el correo del cliente desde localStorage
    const clienteEmail = localStorage.getItem('cliente_email') || 'invitado';

    let cart = JSON.parse(localStorage.getItem('cart_' + clienteEmail)) || [];

    const cartCountElement = document.getElementById('cart-count');
    const cartItemsElement = document.getElementById('cart-items');

    function saveCart() {
        localStorage.setItem('cart_' + clienteEmail, JSON.stringify(cart));
    }

    function addToCart(product) {
        cart.push(product);
        updateCartDisplay();
        saveCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartDisplay();
        saveCart();
    }

    function updateCartDisplay() {
        cartCountElement.innerText = cart.length;
        cartItemsElement.innerHTML = '';

        cart.forEach((item, index) => {
            const itemElement = document.createElement('li');
            itemElement.className = 'list-group-item d-flex align-items-center justify-content-between';
            itemElement.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${item.imagen}" alt="${item.name}" width="50" class="mr-2 rounded">
                    <div>
                        <div>${item.name}</div>
                        <small class="text-muted">$${Number(item.price).toLocaleString('es-CO')}</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">&times;</button>
            `;
            cartItemsElement.appendChild(itemElement);
        });

        const total = cart.reduce((acc, item) => acc + (item.price * item.quantity || item.price), 0);
        document.getElementById('cart-total').innerText = total.toLocaleString('es-CO');

        const jsonProductos = JSON.stringify(cart);
        const inputJson = document.getElementById('productos_json');
        const inputTotal = document.getElementById('total_compra');

        if (inputJson) inputJson.value = jsonProductos;
        if (inputTotal) inputTotal.value = total.toFixed(2);
    }

    // Botones de añadir
    document.querySelectorAll('.btn-warning').forEach(button => {
        button.addEventListener('click', () => {
            const card = button.closest('.card');
            const product = {
                name: card.querySelector('.card-title').innerText,
                price: card.querySelector('.card-text').innerText.replace(/\D/g, ''),
                imagen: card.querySelector('img').getAttribute('src'),
                quantity: 1
            };
            addToCart(product);
        });
    });

    // Mostrar modal del carrito
    document.getElementById('cart').addEventListener('click', () => {
        updateCartDisplay();
        $('#cartModal').modal('show');
    });

    // Eliminar desde listado
    cartItemsElement.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-danger')) {
            const index = event.target.closest('li').dataset.index;
            removeFromCart(index);
        }
    });

    // Limpiar carrito si se viene de finalizar compra
    if (window.location.href.includes('subir_comprobante.php')) {
        cart = [];
        saveCart();
    }

    updateCartDisplay();
});
// Validar si el carrito está vacío antes de enviar el formulario de pago
document.getElementById('goToCheckout').addEventListener('submit', function (e) {
    const cliente = localStorage.getItem('cliente_email') || 'invitado';
    const cart = JSON.parse(localStorage.getItem('cart_' + cliente)) || [];

    if (cart.length === 0) {
        e.preventDefault(); // Detiene el envío del formulario
        alert('❌ No tienes productos en el carrito. Agrega al menos uno antes de proceder al pago.');
    }
});

