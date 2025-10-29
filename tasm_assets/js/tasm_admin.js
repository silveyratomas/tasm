// tasm_admin.js - confirmacion al eliminar productos en admin
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.classList && form.classList.contains('tasm-delete-form')) {
        const ok = confirm('Confirmar eliminar este producto? Esta accion no se puede deshacer.');
        if (!ok) e.preventDefault();
    }
});
