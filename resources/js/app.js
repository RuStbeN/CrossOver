// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';

import '../css/app.css';

// Importar nuestros sistemas
import './utils/notification-system.js';
import './components/calculadora-edad.js';

// Otras importaciones según necesites
// import { createValidator } from './components/form-validators.js';


window.Alpine = Alpine;
Alpine.start();


// Inicialización global cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 App.js loaded successfully');
    
    // Aquí puedes inicializar otros componentes globales
    // Ejemplo de validador para formularios de registro
    /*
    if (document.querySelector('#registration-form')) {
        createValidator('#registration-form', {
            name: { required: true, minLength: 2 },
            email: { required: true, email: true },
            edad: { required: true, numeric: true, min: 16, max: 60 }
        });
    }
    */
});


// Exponer utilidades globalmente para uso en Blade templates
window.App = {
    // Función para inicializar componentes específicos desde Blade
    initComponent(componentName, options = {}) {
        switch(componentName) {
            case 'age-calculator':
                import('./components/calculadora-edad.js').then(module => {
                    new module.AgeCalculator(options);
                });
                break;
            case 'form-validator':
                import('./components/form-validators.js').then(module => {
                    return new module.FormValidator(options.selector, options.config);
                });
                break;
            default:
                console.warn(`Component ${componentName} not found`);
        }
    },
    
    // Función para mostrar notificaciones desde cualquier lugar
    notify: {
        success: (message) => window.NotificationSystem?.success(message),
        error: (message) => window.NotificationSystem?.error(message),
        warning: (message) => window.NotificationSystem?.warning(message),
        info: (message) => window.NotificationSystem?.info(message),
    }
};