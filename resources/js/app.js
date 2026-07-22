﻿﻿import './bootstrap';
import 'bootstrap';

// Função para aplicar o tema no HTML e atualizar ícones
function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    const themeIcon = document.getElementById('theme-icon');
    if (themeIcon) {
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    }

    // Configuração do Tema
    const savedTheme = localStorage.getItem('barberflow-theme') || 'light';
    applyTheme(savedTheme);

    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('barberflow-theme', newTheme);
            applyTheme(newTheme);
        });
    }
});
