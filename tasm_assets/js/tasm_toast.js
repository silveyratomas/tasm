// tasm_toast.js - pequeño sistema de toasts
function tasm_toast(msg, type = 'info', timeout = 2500) {
    let container = document.getElementById('tasm-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'tasm-toast-container';
        container.style.position = 'fixed';
        container.style.right = '16px';
        container.style.top = '16px';
        container.style.zIndex = 9999;
        document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.className = 'tasm-toast tasm-toast-' + type;
    el.textContent = msg;
    container.appendChild(el);
    setTimeout(() => { el.style.opacity = '1'; }, 10);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(-6px)'; setTimeout(() => el.remove(), 300); }, timeout);
}

// export for other scripts
window.tasm_toast = tasm_toast;
