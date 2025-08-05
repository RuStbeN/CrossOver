// resources/js/utils/notification-system.js
export class NotificationSystem {
    constructor() {
        this.isReady = false;
        this.queue = [];
        this.init();
    }

    init() {
        // Esperar a que Alpine.js esté disponible
        this.waitForAlpine();
    }

    waitForAlpine() {
        const checkAlpine = () => {
            if (window.showNotification && typeof window.showNotification === 'function') {
                this.isReady = true;
                this.processQueue();
                this.checkServerMessages();
            } else {
                setTimeout(checkAlpine, 100);
            }
        };
        checkAlpine();
    }

    // Procesar cola de notificaciones pendientes
    processQueue() {
        while (this.queue.length > 0) {
            const notification = this.queue.shift();
            this.showNotification(notification.type, notification.message, notification.delay);
        }
    }

    // Método principal para mostrar notificaciones
    show(type, message, delay = 500) {
        if (!this.isReady) {
            this.queue.push({ type, message, delay });
            return;
        }

        this.showNotification(type, message, delay);
    }

    showNotification(type, message, delay) {
        setTimeout(() => {
            if (window.showNotification) {
                window.showNotification(type, message);
            }
        }, delay);
    }

    // Métodos de conveniencia
    success(message, delay = 500) {
        this.show('success', message, delay);
    }

    error(message, delay = 500) {
        this.show('error', message, delay);
    }

    warning(message, delay = 500) {
        this.show('warning', message, delay);
    }

    info(message, delay = 500) {
        this.show('info', message, delay);
    }

    // Mostrar múltiples errores (validación)
    showErrors(errors, delay = 500) {
        errors.forEach((error, index) => {
            this.error(error, delay + (index * 100)); // Escalonar las notificaciones
        });
    }

    // Verificar mensajes del servidor automáticamente
    checkServerMessages() {
        // Buscar datos en meta tags
        const errorsElement = document.querySelector('meta[name="laravel-errors"]');
        const successElement = document.querySelector('meta[name="laravel-success"]');
        const errorElement = document.querySelector('meta[name="laravel-error"]');
        const warningElement = document.querySelector('meta[name="laravel-warning"]');
        const infoElement = document.querySelector('meta[name="laravel-info"]');

        if (errorsElement && errorsElement.content) {
            try {
                const errors = JSON.parse(errorsElement.content);
                this.showErrors(errors);
            } catch (e) {
                console.error('Error parsing laravel errors:', e);
            }
        }

        if (successElement && successElement.content) {
            this.success(successElement.content);
        }

        if (errorElement && errorElement.content) {
            this.error(errorElement.content);
        }

        if (warningElement && warningElement.content) {
            this.warning(warningElement.content);
        }

        if (infoElement && infoElement.content) {
            this.info(infoElement.content);
        }
    }

    // Limpiar meta tags después de procesarlos (opcional)
    clearServerMessages() {
        const metaTags = [
            'meta[name="laravel-errors"]',
            'meta[name="laravel-success"]',
            'meta[name="laravel-error"]',
            'meta[name="laravel-warning"]',
            'meta[name="laravel-info"]'
        ];

        metaTags.forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                element.remove();
            }
        });
    }
}

// Crear instancia global
const notificationSystem = new NotificationSystem();

// Exponer globalmente
window.NotificationSystem = notificationSystem;

export default notificationSystem;