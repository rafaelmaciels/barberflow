﻿﻿﻿import './bootstrap';
import 'bootstrap';

window.addEventListener('DOMContentLoaded', () => {
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    }
});
