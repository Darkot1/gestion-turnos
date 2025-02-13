<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Gestión de Turnos</h1>

        <!-- Mensajes de estado -->
        <div v-if="message.text"
          class="mb-6 p-4 rounded-xl shadow-md transition-all duration-300"
          :class="message.type === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'"
        >
          {{ message.text }}
        </div>

        <!-- Turnos en Espera -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Turnos en Espera</h2>
            <div class="flex gap-3">
              <span class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-xl font-medium">
                Muestras: {{ waitingShifts.filter(s => s.type === 'muestras').length }}
              </span>
              <span class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-xl font-medium">
                Resultados: {{ waitingShifts.filter(s => s.type === 'resultados').length }}
              </span>
            </div>
          </div>

          <div v-if="!waitingShifts.length"
            class="flex items-center justify-center h-48 bg-gray-50 rounded-xl">
            <p class="text-gray-500 text-lg">No hay turnos en espera</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Primeros en la cola -->
            <template v-for="shift in waitingShifts" :key="shift.id">
              <div v-if="getIsFirstInQueue(shift)"
                class="relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 shadow-md border-2 border-blue-200">
                <div class="absolute top-3 right-3">
                  <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                    Próximo
                  </span>
                </div>
                <ShiftCard
                  :shift="shift"
                  :available-modules="availableModules"
                  :selected-module="selectedModule"
                  :is-loading="isLoading"
                  :is-first-in-queue="true"
                  @update="handleUpdate"
                />
              </div>
            </template>

            <!-- Resto de turnos -->
            <template v-for="shift in waitingShifts" :key="shift.id">
              <div v-if="!getIsFirstInQueue(shift)"
                class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
                <ShiftCard
                  :shift="shift"
                  :available-modules="availableModules"
                  :selected-module="selectedModule"
                  :is-loading="isLoading"
                  :is-first-in-queue="false"
                  @update="handleUpdate"
                />
              </div>
            </template>
          </div>
        </div>

        <!-- Turnos en Proceso -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Turnos en Proceso</h2>
            <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl font-medium">
              {{ inProcessShifts.length }} turnos
            </span>
          </div>

          <div v-if="!inProcessShifts.length"
            class="flex items-center justify-center h-48 bg-gray-50 rounded-xl">
            <p class="text-gray-500 text-lg">No hay turnos en proceso</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="shift in inProcessShifts" :key="shift.id"
              class="bg-gradient-to-br from-emerald-50 to-blue-50 rounded-xl p-6 shadow-md border-2 border-emerald-200">
              <ShiftCard
                :shift="shift"
                :available-modules="availableModules"
                :selected-module="selectedModule"
                :is-loading="isLoading"
                @update="handleUpdate"
              />
            </div>
          </div>
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

        if (!response.data || !response.data.modules) {
            throw new Error('Formato de respuesta inválido');
        }

        const selectedModuleIds = Object.values(selectedModule.value);

        availableModules.value = response.data.modules.filter(module =>
            module.status === 'active' &&
            (!shifts.value.some(shift =>
                shift.module_id === module.id &&
                ['espera', 'en proceso'].includes(shift.status)
            ) || selectedModuleIds.includes(module.id))
        );

        console.log('Módulos cargados:', availableModules.value);
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
