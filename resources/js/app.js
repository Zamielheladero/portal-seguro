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

/* ---------- Diseño AgroPoste ---------- */

// Menú móvil
document.querySelectorAll('[data-menu-toggle]').forEach((toggle) => {
    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') !== 'true';
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        menu.hidden = !open;
        toggle.querySelector('[data-icon-open]').toggleAttribute('hidden', open);
        toggle.querySelector('[data-icon-close]').toggleAttribute('hidden', !open);
    });
});

// Filtros del catálogo: panel en móvil y envío automático en escritorio
const filters = document.querySelector('[data-filters]');
document.querySelectorAll('[data-filter-open]').forEach((button) =>
    button.addEventListener('click', () => filters?.classList.add('open')),
);
document.querySelectorAll('[data-filter-close]').forEach((button) =>
    button.addEventListener('click', () => filters?.classList.remove('open')),
);

document.querySelectorAll('form[data-autosubmit]').forEach((form) => {
    document.querySelectorAll(`[form="${form.id}"]`).forEach((field) => {
        if (field.type === 'search' || field.type === 'submit') {
            return;
        }
        field.addEventListener('change', () => {
            // En móvil el usuario confirma con "Ver N productos".
            if (!filters?.classList.contains('open')) {
                form.requestSubmit();
            }
        });
    });
});

// Selector de cantidad (+/- de a 10)
document.querySelectorAll('[data-qty]').forEach((wrapper) => {
    const input = wrapper.querySelector('input');
    const step = Number(wrapper.dataset.step || 1);
    const clamp = (value) => Math.min(Number(input.max || Infinity), Math.max(Number(input.min || 1), value));
    wrapper.querySelector('[data-qty-minus]').addEventListener('click', () => {
        input.value = clamp(Number(input.value || 0) - step);
    });
    wrapper.querySelector('[data-qty-plus]').addEventListener('click', () => {
        input.value = clamp(Number(input.value || 0) + step);
    });
});

// Descargar resumen: el navegador permite guardarlo como PDF
document.querySelectorAll('[data-print]').forEach((button) =>
    button.addEventListener('click', () => window.print()),
);
