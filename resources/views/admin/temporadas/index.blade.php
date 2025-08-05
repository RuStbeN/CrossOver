@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingTemporada: null,
        formData: {
            nombre: '',
            fecha_inicio: '',
            fecha_fin: '',
            horario_inicio: '08:00',
            horario_fin: '18:00',
            dias_juego: [], // Array de números de días seleccionados
            dias_horarios: [], // Array para horarios específicos
            descripcion: '',
            activo: '1'
        },
        diasSemana: [
            { numero: 1, corto: 'Lun', completo: 'Lunes' },
            { numero: 2, corto: 'Mar', completo: 'Martes' },
            { numero: 3, corto: 'Mié', completo: 'Miércoles' },
            { numero: 4, corto: 'Jue', completo: 'Jueves' },
            { numero: 5, corto: 'Vie', completo: 'Viernes' },
            { numero: 6, corto: 'Sáb', completo: 'Sábado' },
            { numero: 7, corto: 'Dom', completo: 'Domingo' }
        ],
        resetForm() {
            this.formData = {
                nombre: '',
                fecha_inicio: '',
                fecha_fin: '',
                horario_inicio: '08:00',
                horario_fin: '18:00',
                dias_juego: [], // Resetear como array vacío
                dias_horarios: [],
                descripcion: '',
                activo: '1'
            };
            this.editMode = false;
            this.editingTemporada = null;
        },
        editTemporada(temporada) {
            // Convertir fechas al formato YYYY-MM-DD
            const fechaInicio = new Date(temporada.fecha_inicio).toISOString().split('T')[0];
            const fechaFin = new Date(temporada.fecha_fin).toISOString().split('T')[0];
            
            // Convertir los días hábiles a array de números
            const diasHabiles = temporada.dias_habiles ? 
                temporada.dias_habiles.map(dia => parseInt(dia.dia_semana)) : [];
                
            // Cargar horarios específicos si existen
            const diasHorarios = temporada.dias_habiles ? temporada.dias_habiles
                .filter(dia => dia.horario_inicio && dia.horario_fin)
                .map(dia => ({
                    dia: parseInt(dia.dia_semana),
                    horario_inicio: dia.horario_inicio,
                    horario_fin: dia.horario_fin,
                    usar_horario_especifico: true
                })) : [];
            
            this.formData = {
                nombre: temporada.nombre,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                horario_inicio: temporada.horario_inicio,
                horario_fin: temporada.horario_fin,
                dias_juego: diasHabiles, // Array de números
                dias_horarios: diasHorarios,
                descripcion: temporada.descripcion || '',
                activo: temporada.activo ? '1' : '0'
            };
            this.editMode = true;
            this.editingTemporada = temporada;
            this.showForm = true;
        },
        cancelEdit() {
            this.resetForm();
            this.showForm = false;
        },
        isDiaSelected(diaNumero) {
            return this.formData.dias_juego.includes(parseInt(diaNumero));
        },
        toggleDia(diaNumero) {
            const diaNum = parseInt(diaNumero);
            const index = this.formData.dias_juego.indexOf(diaNum);
            if (index > -1) {
                // Remover el día
                this.formData.dias_juego.splice(index, 1);
                // También remover horario específico si existe
                const horarioIndex = this.formData.dias_horarios.findIndex(h => h.dia === diaNum);
                if (horarioIndex > -1) {
                    this.formData.dias_horarios.splice(horarioIndex, 1);
                }
            } else {
                // Agregar el día
                this.formData.dias_juego.push(diaNum);
            }
        }
    }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Formulario de creación/edición -->
        <div x-show="showForm" x-transition class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Temporada' : 'Registrar Nueva Temporada'"></h2>
            </div>

            <form :action="editMode ? `/temporadas/${editingTemporada.id}` : '{{ route('temporadas.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre (se mantiene igual) -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            x-model="formData.nombre"
                            required
                            maxlength="150"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Nombre de la temporada"
                        >
                    </div>

                    <!-- Fila para Fechas y Horarios -->
                    <div class="col-span-1 md:col-span-2 flex flex-wrap gap-6" x-data="{
                        abrirSelector(el) {
                            // Forzar la apertura del datepicker o timepicker
                            el.showPicker();
                        }
                    }">
                        <!-- Grupo Fechas -->
                        <div class="flex-1 min-w-[200px] flex gap-6">
                            <!-- Fecha Inicio -->
                            <div class="flex-1">
                                <label for="fecha_inicio" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                    Fecha Inicio <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="fecha_inicio"
                                    name="fecha_inicio"
                                    x-model="formData.fecha_inicio"
                                    @click="abrirSelector($el)"
                                    required
                                    class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent cursor-pointer"
                                >
                            </div>

                            <!-- Fecha Fin -->
                            <div class="flex-1">
                                <label for="fecha_fin" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                    Fecha Fin <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="fecha_fin"
                                    name="fecha_fin"
                                    x-model="formData.fecha_fin"
                                    @click="abrirSelector($el)"
                                    required
                                    class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent cursor-pointer"
                                >
                            </div>
                        </div>

                        <!-- Grupo Horarios -->
                        <div class="flex-1 min-w-[200px] flex gap-6">
                            <!-- Horario Inicio -->
                            <div class="flex-1">
                                <label for="horario_inicio" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                    Horario Inicio <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="time"
                                    id="horario_inicio"
                                    name="horario_inicio"
                                    x-model="formData.horario_inicio"
                                    @click="abrirSelector($el)"
                                    required
                                    class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent cursor-pointer"
                                >
                            </div>

                            <!-- Horario Fin -->
                            <div class="flex-1">
                                <label for="horario_fin" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                    Horario Fin <span class="text-red-400">*</span>
                                </label>
                                <input
                                    type="time"
                                    id="horario_fin"
                                    name="horario_fin"
                                    x-model="formData.horario_fin"
                                    @click="abrirSelector($el)"
                                    required
                                    class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent cursor-pointer"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Días hábiles con horarios específicos - Versión final -->
                    <div class="col-span-1 md:col-span-2" x-data="{
                        // Función para obtener el horario específico de un día o el general
                        getDiaHorarioInicio(diaNumero) {
                            const diaData = this.formData.dias_horarios.find(d => d.dia === diaNumero);
                            return diaData?.horario_inicio || this.formData.horario_inicio;
                        },
                        getDiaHorarioFin(diaNumero) {
                            const diaData = this.formData.dias_horarios.find(d => d.dia === diaNumero);
                            return diaData?.horario_fin || this.formData.horario_fin;
                        },
                        // Función para actualizar horario específico de un día
                        updateDiaHorario(diaNumero, tipo, valor) {
                            let diaData = this.formData.dias_horarios.find(d => d.dia === diaNumero);
                            if (!diaData) {
                                diaData = { 
                                    dia: diaNumero, 
                                    horario_inicio: this.formData.horario_inicio, 
                                    horario_fin: this.formData.horario_fin,
                                    usar_horario_especifico: false
                                };
                                this.formData.dias_horarios.push(diaData);
                            }
                            diaData[tipo] = valor;
                        },
                        // Función para activar/desactivar horario específico
                        toggleHorarioEspecifico(diaNumero) {
                            let diaData = this.formData.dias_horarios.find(d => d.dia === diaNumero);
                            if (!diaData) {
                                diaData = { 
                                    dia: diaNumero, 
                                    horario_inicio: this.formData.horario_inicio, 
                                    horario_fin: this.formData.horario_fin,
                                    usar_horario_especifico: false
                                };
                                this.formData.dias_horarios.push(diaData);
                            }
                            diaData.usar_horario_especifico = !diaData.usar_horario_especifico;
                        },
                        // Verificar si un día tiene horario específico
                        tieneHorarioEspecifico(diaNumero) {
                            const diaData = this.formData.dias_horarios.find(d => d.dia === diaNumero);
                            return diaData?.usar_horario_especifico || false;
                        }
                    }">
                        <label class="block text-sm font-medium mb-3 dark:text-gray-300 text-dark-800">
                            Días de juego <span class="text-red-400">*</span>
                        </label>

                        <!-- Selector de días - Versión corregida -->
                        <div class="mb-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-7 gap-3">
                                <template x-for="dia in diasSemana" :key="dia.numero">
                                    <div>
                                        <div 
                                            @click="toggleDia(dia.numero)"
                                            class="flex flex-col items-center justify-center p-3 border-2 rounded-lg cursor-pointer transition-all select-none h-16"
                                            :class="isDiaSelected(dia.numero) ? 
                                                'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-md' : 
                                                'border-gray-200 dark:border-dark-600 hover:bg-gray-50 dark:hover:bg-dark-700 hover:border-gray-300 dark:hover:border-dark-500'"
                                        >
                                            <span 
                                                class="block text-lg font-semibold transition-colors"
                                                :class="isDiaSelected(dia.numero) ? 
                                                    'text-primary-700 dark:text-primary-300' : 
                                                    'text-gray-800 dark:text-gray-200'"
                                                x-text="dia.corto"
                                            ></span>
                                            <span 
                                                class="block text-xs transition-colors"
                                                :class="isDiaSelected(dia.numero) ? 
                                                    'text-primary-600 dark:text-primary-400' : 
                                                    'text-gray-500 dark:text-gray-400'"
                                                x-text="dia.completo"
                                            ></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Inputs hidden para enviar solo los días seleccionados -->
                            <template x-for="dia in formData.dias_juego" :key="dia">
                                <input type="hidden" name="dias_juego[]" :value="dia">
                            </template>
                            
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Selecciona los días en que se jugarán los partidos
                            </p>
                            
                            <!-- Debug info (opcional, para verificar) -->
                            <div class="mt-2 text-xs text-gray-400" x-show="formData.dias_juego.length > 0">
                                <span>Días seleccionados: </span>
                                <template x-for="(dia, index) in formData.dias_juego" :key="dia">
                                    <span>
                                        <span x-text="diasSemana.find(d => d.numero === dia)?.completo"></span><span x-show="index < formData.dias_juego.length - 1">, </span>
                                    </span>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Lista de días seleccionados con horarios -->
                        <div class="space-y-4">
                            <template x-for="dia in diasSemana" :key="dia.numero">
                                <div x-show="isDiaSelected(dia.numero)" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    class="border border-gray-200 dark:border-dark-600 rounded-lg p-4 bg-gray-50 dark:bg-dark-700">
                                    
                                    <!-- Header del día -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                                                <span class="text-sm font-bold text-primary-700 dark:text-primary-300" x-text="dia.corto"></span>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white" x-text="dia.completo"></h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    <span x-show="!tieneHorarioEspecifico(dia.numero)">
                                                        Usando horario general: <span x-text="formData.horario_inicio + ' - ' + formData.horario_fin"></span>
                                                    </span>
                                                    <span x-show="tieneHorarioEspecifico(dia.numero)" class="text-primary-600 dark:text-primary-400">
                                                        Horario específico
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Toggle para horario específico -->
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600 dark:text-gray-300">Horario específico</span>
                                            <button 
                                                type="button"
                                                @click="toggleHorarioEspecifico(dia.numero)"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                :class="tieneHorarioEspecifico(dia.numero) ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-600'"
                                            >
                                                <span 
                                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                    :class="tieneHorarioEspecifico(dia.numero) ? 'translate-x-6' : 'translate-x-1'"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Horarios específicos (solo se muestran si está activado) -->
                                    <div x-show="tieneHorarioEspecifico(dia.numero)" 
                                        x-transition
                                        class="grid grid-cols-2 gap-4">
                                        
                                        <!-- Horario inicio específico -->
                                        <div>
                                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                Hora inicio
                                            </label>
                                            <input
                                                type="time"
                                                :value="getDiaHorarioInicio(dia.numero)"
                                                @input="updateDiaHorario(dia.numero, 'horario_inicio', $event.target.value)"
                                                class="w-full bg-white dark:bg-dark-800 px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            >
                                        </div>
                                        
                                        <!-- Horario fin específico -->
                                        <div>
                                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                                Hora fin
                                            </label>
                                            <input
                                                type="time"
                                                :value="getDiaHorarioFin(dia.numero)"
                                                @input="updateDiaHorario(dia.numero, 'horario_fin', $event.target.value)"
                                                class="w-full bg-white dark:bg-dark-800 px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                            >
                                        </div>
                                    </div>
                                    
                                    <!-- Inputs hidden para enviar los datos de horarios -->
                                    <template x-if="tieneHorarioEspecifico(dia.numero)">
                                        <div>
                                            <input type="hidden" :name="'dias_horarios[' + dia.numero + '][horario_inicio]'" :value="getDiaHorarioInicio(dia.numero)">
                                            <input type="hidden" :name="'dias_horarios[' + dia.numero + '][horario_fin]'" :value="getDiaHorarioFin(dia.numero)">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>


                    <!-- Estado Activo -->
                    <div class="col-span-1">
                        <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Estado <span class="text-orange-500 text-lg font-semibold">*</span>
                        </label>
                        <select
                            id="activo"
                            name="activo"
                            x-model="formData.activo"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="1">Activa</option>
                            <option value="0">Inactiva</option>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Descripción
                        </label>
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            x-model="formData.descripcion"
                            rows="3"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Descripción de la temporada (opcional)"
                        ></textarea>
                    </div>
                </div>

                <!-- Botones de acción (se mantienen igual) -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="cancelEdit()"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                    >
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                        x-text="editMode ? 'Actualizar Temporada' : 'Registrar Temporada'"
                    >
                    </button>
                </div>
            </form>

        </div>

        <!-- Lista de temporadas -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
            @include('admin.temporadas.partials.lista.listado_temporadas')
        </div>

    </div>

    </main>

@endsection

