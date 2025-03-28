document.addEventListener('DOMContentLoaded', () => {
    // Confirmar eliminación
    const deleteLinks = document.querySelectorAll('a[href*="eliminar"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm('¿Estás seguro de eliminar este recurso?')) {
                e.preventDefault(); // Evitar la acción si el usuario cancela
            }
        });
    });

    // Validar formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let valid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('error'); // Agregar clase de error
                } else {
                    input.classList.remove('error');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos requeridos.');
            }
        });
    });

    // Mensajes interactivos (simulación)
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.display = 'none'; // Ocultar mensaje después de 5 segundos
        }, 5000);
    });
});

// Validación del formulario de registro
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#register-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            const password = document.querySelector('#password').value;
            const confirmPassword = document.querySelector('#confirm-password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
            }
        });
    }
});
