<template>
    <div :class="[
        'p-4 rounded-lg shadow-md transition-all duration-200',
        shift.status === 'en proceso' ? 'bg-blue-50 border border-blue-200' :
        isFirstInQueue ? 'bg-green-50 border border-green-200' : 'bg-white',
        !canBeProcessed ? 'opacity-70' : '',

    ]">
        <!-- Encabezado del turno -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-2xl font-bold">
                    Turno {{ shift.number }}
                    <span class="text-sm font-normal ml-2 px-2 py-1 rounded"
                        :class="shift.type === 'muestras' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700'">
                        {{ shift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                    </span>
                </h3>
                <p class="text-xs text-gray-500">
                    Creado: {{ formatDateTime(shift.created_at) }}
                </p>
            </div>
        </div>

        <!-- Mensaje de estado del turno -->
        <div v-if="shift.status === 'espera'"
             :class="[
                 'mb-4 p-2 rounded text-sm',
                 isFirstInQueue ? 'bg-green-100 text-green-700' : 'bg-yellow-50 text-yellow-700'
             ]">
            {{ isFirstInQueue ?
                'Turno disponible para atención' :
                `Esperando turnos anteriores de ${shift.type === 'muestras' ? 'muestras' : 'resultados'}`
            }}
        </div>

        <!-- turnos en proceso -->
        <div v-if="shift.status === 'en proceso' && shift.module"
             class="mb-4 p-3 bg-green-100 border border-green-200 rounded-lg">
            <p class="text-green-700 font-medium flex items-center">
                <span class="material-icons-outlined mr-2">location_on</span>
                Atendiendo en Módulo {{ shift.module.number }}
            </p>
        </div>

        <!--turnos en espera -->
        <div v-if="shift.status === 'espera'" class="space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <button
                    v-for="module in availableModules"
                    :key="module.id"
                    @click="selectModule(module.id)"
                    :disabled="!canBeProcessed"
                    :class="[
                        'p-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center',
                        selectedModule[shift.id] === module.id
                            ? 'bg-green-500 text-white shadow-lg scale-105'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:scale-102',
                        !canBeProcessed ? 'cursor-not-allowed opacity-50' : ''
                    ]"
                >
                    <span class="material-icons-outlined mr-2">
                        {{ selectedModule[shift.id] === module.id ? 'check_circle' : 'radio_button_unchecked' }}
                    </span>
                    Módulo {{ module.number }}
                </button>
            </div>
        </div>


        <div class="flex flex-wrap gap-2 mt-4">
            <!-- Botones para turnos en espera -->
            <button
                v-if="shift.status === 'espera'"
                @click="updateShiftStatus('en proceso')"
                :disabled="(!selectedModule[shift.id] && !shift.module_id) || !canBeProcessed"
                :class="[
                    'flex-1 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center',
                    canBeProcessed ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-400 text-white cursor-not-allowed'
                ]"
            >
                <span class="material-icons-outlined mr-2">play_arrow</span>
                {{ canBeProcessed ? 'Iniciar Atención' : 'Esperar turno anterior' }}
            </button>

            <!-- Botones para turnos en proceso -->
            <template v-if="shift.status === 'en proceso'">
                <button
                    @click="updateShiftStatus('atendido')"
                    class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600
                           transition-colors duration-200 flex items-center justify-center"
                >
                    <span class="material-icons-outlined mr-2">check_circle</span>
                    Finalizar
                </button>
                <button
                    @click="updateShiftStatus('cancelado')"
                    class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600
                           transition-colors duration-200 flex items-center justify-center"
                >
                    <span class="material-icons-outlined mr-2">cancel</span>
                    Cancelar
                </button>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    shift: {
        type: Object,
        required: true
    },
    availableModules: {
        type: Array,
        default: () => []
    },
    selectedModule: {
        type: Object,
        required: true
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    isFirstInQueue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update']);


const canBeProcessed = computed(() => {
    return props.shift.status === 'en proceso' || props.isFirstInQueue;
});

const translateStatus = (status) => ({
    'espera': 'En espera',
    'en proceso': 'En proceso',
    'atendido': 'Atendido',
    'finalizado': 'Finalizado',
    'cancelado': 'Cancelado'
}[status] || status);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};

const formatTipoTurno = (type) => ({
    'muestras': 'Muestras',
    'resultados': 'Resultados'
}[type] || type);

const selectModule = (moduleId) => {
    emit('update', {
        shiftId: props.shift.id,
        moduleId,
        status: props.shift.status
    });
};

const updateShiftStatus = (newStatus) => {
    const moduleId = props.selectedModule[props.shift.id] || props.shift.module_id;

    if (!moduleId && newStatus === 'en proceso') {
        alert('Por favor, seleccione un módulo antes de iniciar la atención');
        return;
    }

    emit('update', {
        shiftId: props.shift.id,
        moduleId,
        status: newStatus
    });
};
</script>
