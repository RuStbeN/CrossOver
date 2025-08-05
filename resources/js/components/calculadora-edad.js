// resources/js/components/calculadora-edad.js
export class CalculadoraEdad {
    constructor(opciones = {}) {
        this.opciones = {
            selectorFechaNacimiento: '#fecha_nacimiento',
            selectorEdad: '#edad',
            edadMinima: 16,
            edadMaxima: 60,
            calcularAutomaticamente: true,
            mostrarMensajesConsola: true,
            ...opciones
        };
        
        this.inputFechaNacimiento = null;
        this.inputEdad = null;
        this.inicializar();
    }

    inicializar() {
        this.encontrarElementos();
        if (!this.inputFechaNacimiento || !this.inputEdad) {
            if (this.opciones.mostrarMensajesConsola) {
                console.warn('⚠️ CalculadoraEdad: No se encontraron los elementos necesarios');
            }
            return;
        }
        
        this.vincularEventos();
        if (this.opciones.mostrarMensajesConsola) {
            console.log('✅ CalculadoraEdad inicializada correctamente');
        }
    }

    encontrarElementos() {
        this.inputFechaNacimiento = document.querySelector(this.opciones.selectorFechaNacimiento);
        this.inputEdad = document.querySelector(this.opciones.selectorEdad);
    }

    vincularEventos() {
        // Calcular edad automáticamente cuando cambie la fecha
        if (this.opciones.calcularAutomaticamente) {
            this.inputFechaNacimiento.addEventListener('change', () => {
                this.calcularEdad();
            });
        }

        // Abrir selector de fecha al hacer clic
        this.inputFechaNacimiento.addEventListener('click', () => {
            this.abrirSelectorFecha();
        });

        // Validar edad cuando se modifique manualmente
        this.inputEdad.addEventListener('change', () => {
            this.validarEdad();
        });

        // Validar edad mientras se escribe (solo números)
        this.inputEdad.addEventListener('input', () => {
            this.validarEntradaEdad();
        });

        // Prevenir entrada de texto en el campo de fecha
        this.inputFechaNacimiento.addEventListener('keydown', (evento) => {
            // Permitir teclas de navegación, borrar, etc.
            const teclasPermitidas = [
                'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'
            ];
            
            if (!teclasPermitidas.includes(evento.key) && !evento.ctrlKey) {
                evento.preventDefault();
            }
        });
    }

    calcularEdad() {
        if (!this.inputFechaNacimiento.value) {
            this.inputEdad.value = '';
            return;
        }

        try {
            const fechaNacimiento = new Date(this.inputFechaNacimiento.value);
            const hoy = new Date();
            
            // Validar que la fecha no sea futura
            if (fechaNacimiento > hoy) {
                window.NotificationSystem?.warning('La fecha de nacimiento no puede ser en el futuro');
                this.limpiarCampos();
                return;
            }

            // Validar que la fecha no sea demasiado antigua (más de 120 años)
            const fechaLimite = new Date();
            fechaLimite.setFullYear(fechaLimite.getFullYear() - 120);
            
            if (fechaNacimiento < fechaLimite) {
                window.NotificationSystem?.warning('La fecha de nacimiento no puede ser anterior a 120 años');
                this.limpiarCampos();
                return;
            }

            let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
            const diferenciaMes = hoy.getMonth() - fechaNacimiento.getMonth();

            if (diferenciaMes < 0 || (diferenciaMes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                edad--;
            }

            // Validar edad calculada
            if (edad < 0) {
                window.NotificationSystem?.error('Fecha de nacimiento inválida');
                this.limpiarCampos();
                return;
            }

            this.inputEdad.value = edad;
            
            // Mostrar advertencias si está fuera del rango permitido
            if (edad < this.opciones.edadMinima) {
                window.NotificationSystem?.warning(`La edad mínima permitida es ${this.opciones.edadMinima} años`);
                this.resaltarCampo(this.inputEdad, 'warning');
            } else if (edad > this.opciones.edadMaxima) {
                window.NotificationSystem?.warning(`La edad máxima permitida es ${this.opciones.edadMaxima} años`);
                this.resaltarCampo(this.inputEdad, 'warning');
            } else {
                this.resaltarCampo(this.inputEdad, 'success');
            }

            if (this.opciones.mostrarMensajesConsola) {
                console.log(`📅 Edad calculada: ${edad} años`);
            }

        } catch (error) {
            console.error('❌ Error al calcular la edad:', error);
            window.NotificationSystem?.error('Error al calcular la edad');
        }
    }

    validarEdad() {
        const edad = parseInt(this.inputEdad.value);
        
        if (isNaN(edad) || this.inputEdad.value === '') {
            this.inputEdad.value = '';
            this.removerResaltado(this.inputEdad);
            return;
        }

        let edadCorregida = edad;
        let mostrarAdvertencia = false;

        if (edad < this.opciones.edadMinima) {
            edadCorregida = this.opciones.edadMinima;
            mostrarAdvertencia = true;
            window.NotificationSystem?.warning(`Edad mínima permitida: ${this.opciones.edadMinima} años`);
        } else if (edad > this.opciones.edadMaxima) {
            edadCorregida = this.opciones.edadMaxima;
            mostrarAdvertencia = true;
            window.NotificationSystem?.warning(`Edad máxima permitida: ${this.opciones.edadMaxima} años`);
        }

        this.inputEdad.value = edadCorregida;
        
        if (mostrarAdvertencia) {
            this.resaltarCampo(this.inputEdad, 'warning');
        } else {
            this.resaltarCampo(this.inputEdad, 'success');
        }
    }

    validarEntradaEdad() {
        // Permitir solo números y eliminar cualquier otro carácter
        const valorOriginal = this.inputEdad.value;
        const valorLimpio = valorOriginal.replace(/[^0-9]/g, '');
        
        if (valorOriginal !== valorLimpio) {
            this.inputEdad.value = valorLimpio;
        }

        // Limitar a 3 dígitos máximo
        if (valorLimpio.length > 3) {
            this.inputEdad.value = valorLimpio.substring(0, 3);
        }
    }

    abrirSelectorFecha() {
        // Verificar si el navegador soporta showPicker
        if (this.inputFechaNacimiento.showPicker && typeof this.inputFechaNacimiento.showPicker === 'function') {
            try {
                this.inputFechaNacimiento.showPicker();
                if (this.opciones.mostrarMensajesConsola) {
                    console.log('📅 Selector de fecha abierto con showPicker');
                }
            } catch (error) {
                if (this.opciones.mostrarMensajesConsola) {
                    console.log('⚠️ showPicker falló, pero el campo sigue siendo funcional');
                }
                // No mostrar error al usuario, el campo sigue funcionando normalmente
            }
        } else {
            if (this.opciones.mostrarMensajesConsola) {
                console.log('ℹ️ showPicker no está disponible en este navegador, usando comportamiento estándar');
            }
            // El campo input[type="date"] funcionará normalmente sin showPicker
        }
    }

    resaltarCampo(campo, tipo) {
        // Remover clases anteriores
        this.removerResaltado(campo);
        
        // Aplicar nuevo estilo según el tipo
        switch(tipo) {
            case 'success':
                campo.classList.add('border-green-500', 'focus:border-green-600');
                break;
            case 'warning':
                campo.classList.add('border-yellow-500', 'focus:border-yellow-600');
                break;
            case 'error':
                campo.classList.add('border-red-500', 'focus:border-red-600');
                break;
        }
    }

    removerResaltado(campo) {
        const clasesResaltado = [
            'border-green-500', 'focus:border-green-600',
            'border-yellow-500', 'focus:border-yellow-600',
            'border-red-500', 'focus:border-red-600'
        ];
        
        clasesResaltado.forEach(clase => {
            campo.classList.remove(clase);
        });
    }

    // Métodos públicos para control externo
    
    limpiarCampos() {
        this.inputFechaNacimiento.value = '';
        this.inputEdad.value = '';
        this.removerResaltado(this.inputFechaNacimiento);
        this.removerResaltado(this.inputEdad);
    }

    establecerFechaNacimiento(fecha) {
        if (typeof fecha === 'string') {
            this.inputFechaNacimiento.value = fecha;
        } else if (fecha instanceof Date) {
            // Formatear fecha a YYYY-MM-DD para input[type="date"]
            this.inputFechaNacimiento.value = fecha.toISOString().split('T')[0];
        }
        
        if (this.opciones.calcularAutomaticamente) {
            this.calcularEdad();
        }
    }

    obtenerEdad() {
        return parseInt(this.inputEdad.value) || null;
    }

    obtenerFechaNacimiento() {
        return this.inputFechaNacimiento.value ? new Date(this.inputFechaNacimiento.value) : null;
    }

    establecerRangoEdad(minima, maxima) {
        this.opciones.edadMinima = minima;
        this.opciones.edadMaxima = maxima;
        
        if (this.opciones.mostrarMensajesConsola) {
            console.log(`⚙️ Rango de edad actualizado: ${minima} - ${maxima} años`);
        }
    }

    activarCalculoAutomatico(activar = true) {
        this.opciones.calcularAutomaticamente = activar;
    }

    // Método para recalcular manualmente
    recalcular() {
        this.calcularEdad();
    }
}

// Función de inicialización automática
export function inicializarCalculadoraEdad(opciones = {}) {
    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new CalculadoraEdad(opciones);
        });
    } else {
        new CalculadoraEdad(opciones);
    }
}

// Auto-inicializar si existen los elementos por defecto
document.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.querySelector('#fecha_nacimiento');
    const edadInput = document.querySelector('#edad');
    
    if (fechaInput && edadInput) {
        new CalculadoraEdad();
        console.log('🎂 CalculadoraEdad auto-inicializada para los campos estándar');
    }
});