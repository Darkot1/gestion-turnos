<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Gestión de Turnos</h1>

            <div v-if="message.text"
                :class="[
                    'mb-4 p-4 rounded-lg',
                    message.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]">
                {{ message.text }}
            </div>

            <!-- Turnos en Espera -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Turnos en Espera</h2>
                    <div class="flex gap-2">
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">
                            Muestras: {{ waitingShifts.filter(s => s.type === 'muestras').length }}
                        </span>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full">
                            Resultados: {{ waitingShifts.filter(s => s.type === 'resultados').length }}
                        </span>
                    </div>
                </div>
                <div v-if="!waitingShifts.length" class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                    No hay turnos en espera
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- mostrar los primer turnos de cada tipo -->
                    <template v-for="shift in waitingShifts" :key="shift.id">
                        <ShiftCard
                            v-if="getIsFirstInQueue(shift)"
                            :shift="shift"
                            :available-modules="availableModules"
                            :selected-module="selectedModule"
                            :is-loading="isLoading"
                            :is-first-in-queue="true"
                            @update="handleUpdate"
                        />
                    </template>
                    <template v-for="shift in waitingShifts" :key="shift.id">
                        <ShiftCard
                            v-if="!getIsFirstInQueue(shift)"
                            :shift="shift"
                            :available-modules="availableModules"
                            :selected-module="selectedModule"
                            :is-loading="isLoading"
                            :is-first-in-queue="false"
                            @update="handleUpdate"
                        />
                    </template>
                </div>
            </div>

            <!-- Turnos en Proceso -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Turnos en Proceso</h2>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        {{ inProcessShifts.length }} turnos
                    </span>
                </div>
                <div v-if="!inProcessShifts.length" class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                    No hay turnos en proceso
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <ShiftCard
                        v-for="shift in inProcessShifts"
                        :key="shift.id"
                        :shift="shift"
                        :available-modules="availableModules"
                        :selected-module="selectedModule"
                        :is-loading="isLoading"
                        @update="handleUpdate"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ShiftCard from '@/Components/ShiftCard.vue';
import axios from 'axios';

const props = defineProps({
    shifts: {
        type: Array,
        default: () => []
    },
    firstShifts: {
        type: Object,
        required: true
    }
});

const shifts = ref(props.shifts);
const message = ref({ text: '', type: 'success' });
const selectedStatus = ref({});
const selectedModule = ref({});
const availableModules = ref([]);
const isLoading = ref(false);

// Computed properties para filtrar los turnos
const waitingShifts = computed(() =>
    shifts.value.filter(shift => shift.status === 'espera')
);

const inProcessShifts = computed(() =>
    shifts.value.filter(shift => shift.status === 'en proceso')
);

// Computed property para identificar el primer turno en espera
const getIsFirstInQueue = (shift) => {
    if (shift.status === 'en proceso') return true;

    // Verificar si este turno es uno de los primeros de su tipo
    const firstOfType = props.firstShifts[shift.type];
    return firstOfType && firstOfType.id === shift.id;
};

const loadModules = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/modules');
        // matener los módulos seleccionados en los turnos en espera
        const selectedModuleIds = Object.values(selectedModule.value);
        availableModules.value = response.data.modules.filter(module =>
            module.status === 'active' &&
            (!shifts.value.some(shift =>
                shift.module_id === module.id &&
                ['espera', 'en proceso'].includes(shift.status)
            ) || selectedModuleIds.includes(module.id))
        );
    } catch (error) {
        console.error('Error cargando módulos:', error);
        message.value = {
            text: 'Error al cargar los módulos disponibles',
            type: 'error'
        };
    } finally {
        isLoading.value = false;
    }
};

const handleUpdate = async ({ shiftId, moduleId, status }) => {
    try {
        // Si solo es selección de módulo, actualizamos localmente sin llamar al servidor
        if (status === 'espera') {
            selectedModule.value[shiftId] = moduleId;
            return;
        }

        // Si es cambio de estado, entonces sí llamamos al servidor
        await axios.put(`/shifts/${shiftId}/status`, {
            status,
            module_id: moduleId
        });

        const updatedShift = shifts.value.find(s => s.id === shiftId);
        if (updatedShift) {
            updatedShift.status = status;
            updatedShift.module_id = moduleId;
        }

        if (!['espera', 'en proceso'].includes(status)) {
            shifts.value = shifts.value.filter(s => s.id !== shiftId);
            await loadModules(); // Solo recargamos módulos cuando el turno cambia a estado final
        }

        message.value = {
            text: 'Turno actualizado correctamente',
            type: 'success'
        };
    } catch (error) {
        message.value = {
            text: 'Error al actualizar el turno',
            type: 'error'
        };
    }
};

onMounted(async () => {
    await loadModules();
    shifts.value.forEach(shift => {
        selectedStatus.value[shift.id] = shift.status;
        selectedModule.value[shift.id] = shift.module_id || '';
    });
});
</script>
