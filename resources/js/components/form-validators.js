// resources/js/components/form-validators.js
export class FormValidator {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        this.options = {
            validateOnBlur: true,
            validateOnInput: false,
            showSuccessMessages: false,
            ...options
        };
        
        this.rules = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        
        this.bindEvents();
        console.log('✅ FormValidator initialized for:', this.form);
    }

    bindEvents() {
        // Validar al enviar el formulario
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });

        // Validar campos individualmente
        if (this.options.validateOnBlur) {
            this.form.addEventListener('blur', (e) => {
                if (e.target.matches('input, select, textarea')) {
                    this.validateField(e.target);
                }
            }, true);
        }

        if (this.options.validateOnInput) {
            this.form.addEventListener('input', (e) => {
                if (e.target.matches('input, textarea')) {
                    this.validateField(e.target);
                }
            });
        }
    }

    // Agregar reglas de validación
    addRule(fieldName, rules) {
        this.rules[fieldName] = rules;
        return this;
    }

    // Validar todo el formulario
    validateForm() {
        let isValid = true;
        const errors = [];

        Object.keys(this.rules).forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const fieldErrors = this.validateField(field, false);
                if (fieldErrors.length > 0) {
                    isValid = false;
                    errors.push(...fieldErrors);
                }
            }
        });

        if (!isValid) {
            window.NotificationSystem?.showErrors(errors);
        }

        return isValid;
    }

    // Validar un campo específico
    validateField(field, showNotification = true) {
        const fieldName = field.name;
        const value = field.value.trim();
        const rules = this.rules[fieldName];
        const errors = [];

        if (!rules) return errors;

        // Validación requerido
        if (rules.required && !value) {
            errors.push(`El campo ${this.getFieldLabel(field)} es obligatorio`);
        }

        // Si el campo está vacío y no es requerido, no validar más reglas
        if (!value && !rules.required) {
            this.markFieldAsValid(field);
            return errors;
        }

        // Longitud mínima
        if (rules.minLength && value.length < rules.minLength) {
            errors.push(`${this.getFieldLabel(field)} debe tener al menos ${rules.minLength} caracteres`);
        }

        // Longitud máxima
        if (rules.maxLength && value.length > rules.maxLength) {
            errors.push(`${this.getFieldLabel(field)} no puede tener más de ${rules.maxLength} caracteres`);
        }

        // Email
        if (rules.email && !this.isValidEmail(value)) {
            errors.push(`Ingrese un email válido`);
        }

        // Número
        if (rules.numeric && !this.isNumeric(value)) {
            errors.push(`${this.getFieldLabel(field)} debe ser un número`);
        }

        // Rango numérico
        if (rules.min && parseFloat(value) < rules.min) {
            errors.push(`${this.getFieldLabel(field)} debe ser mayor o igual a ${rules.min}`);
        }

        if (rules.max && parseFloat(value) > rules.max) {
            errors.push(`${this.getFieldLabel(field)} debe ser menor o igual a ${rules.max}`);
        }

        // Regex personalizado
        if (rules.pattern && !rules.pattern.test(value)) {
            errors.push(rules.patternMessage || `${this.getFieldLabel(field)} no tiene el formato correcto`);
        }

        // Validación personalizada
        if (rules.custom && typeof rules.custom === 'function') {
            const customError = rules.custom(value, field);
            if (customError) {
                errors.push(customError);
            }
        }

        // Mostrar resultado
        if (errors.length > 0) {
            this.markFieldAsInvalid(field);
            if (showNotification) {
                window.NotificationSystem?.showErrors(errors);
            }
        } else {
            this.markFieldAsValid(field);
            if (showNotification && this.options.showSuccessMessages) {
                window.NotificationSystem?.success(`${this.getFieldLabel(field)} válido`);
            }
        }

        return errors;
    }

    // Marcar campo como válido visualmente
    markFieldAsValid(field) {
        field.classList.remove('border-red-500', 'border-red-400');
        field.classList.add('border-green-500');
        
        // Remover mensaje de error si existe
        const errorMsg = field.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }

    // Marcar campo como inválido visualmente
    markFieldAsInvalid(field) {
        field.classList.remove('border-green-500');
        field.classList.add('border-red-500');
    }

    // Obtener etiqueta del campo
    getFieldLabel(field) {
        const label = this.form.querySelector(`label[for="${field.id}"]`);
        if (label) {
            return label.textContent.replace('*', '').trim();
        }
        
        return field.placeholder || field.name || 'Este campo';
    }

    // Validadores auxiliares
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isNumeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }

    // Limpiar todas las validaciones visuales
    clearValidations() {
        const fields = this.form.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            field.classList.remove('border-red-500', 'border-green-500', 'border-red-400');
            
            const errorMsg = field.parentNode.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
    }
}

// Función helper para crear validador rápidamente
export function createValidator(formSelector, rules = {}) {
    const validator = new FormValidator(formSelector);
    
    Object.keys(rules).forEach(fieldName => {
        validator.addRule(fieldName, rules[fieldName]);
    });
    
    return validator;
}