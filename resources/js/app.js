import './bootstrap';

/*
 * SEC-07: confirmación antes de enviar formularios peligrosos (p. ej. eliminar).
 * Reemplaza los onclick="return confirm(...)" en línea, que la
 * Content-Security-Policy bloquea. Uso: <form data-confirm="¿Seguro?">
 */
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (form instanceof HTMLFormElement && form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});
