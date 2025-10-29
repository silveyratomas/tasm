// JS mínimo para interacción del carrito (prefijos tasm_)
document.addEventListener('click', function (e) {
    if (e.target.matches('.tasm-add-btn')) {
        const id = e.target.getAttribute('data-id');
        tasm_addToCart(id, e.target);
    }
});

function tasm_addToCart(id, btn) {
    btn.disabled = true;
    btn.textContent = 'Anadiendo...';
    fetch('tasm_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add&id=' + encodeURIComponent(id)
    }).then(r => r.json()).then(js => {
        if (js.ok) {
            const c = document.getElementById('tasm-cart-count');
            if (c) c.textContent = js.count;
            btn.textContent = 'Anadido';
            // usar toast para feedback si esta disponible
            if (window.tasm_toast) window.tasm_toast('Producto anadido al carrito', 'success');
            setTimeout(() => { btn.textContent = 'Anadir'; btn.disabled = false; }, 800);
        } else {
            if (window.tasm_toast) window.tasm_toast('Error: ' + (js.msg || 'sin detalles'), 'error');
            else alert('Error: ' + (js.msg || 'sin detalles'));
            btn.disabled = false; btn.textContent = 'Añadir';
        }
    }).catch(err => { alert('Error de red'); btn.disabled = false; btn.textContent = 'Añadir'; });
}
