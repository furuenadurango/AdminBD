/**
 * App.js - Lógica principal del lado del cliente
 * QueHayPaHacer System
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('QueHayPaHacer UI Initialized');

    // Micro-animación para el formulario de login al enfocar inputs
    const inputs = document.querySelectorAll('.form-input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.3s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Validación básica del formulario de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                // Simple feedback visual
                const card = document.querySelector('.auth-card');
                card.style.animation = 'none';
                card.offsetHeight; // trigger reflow
                card.style.animation = 'shake 0.4s ease-in-out';
                
                // Si pudieramos agregar toast notifications sería aquí
                console.warn("Por favor llena todos los campos.");
            }
        });
    }
});

// Definir keyframe para el shake dinámicamente si es necesario
const style = document.createElement('style');
style.innerHTML = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        50% { transform: translateX(10px); }
        75% { transform: translateX(-10px); }
    }
`;
document.head.appendChild(style);
