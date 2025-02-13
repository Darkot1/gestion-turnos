<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
      <div class="max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-4xl font-extrabold text-gray-800">Gestión de Módulos</h1>
          <button
            @click="showCreateModal = true"
            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Módulo
          </button>
        </div>

        <!-- Lista de Módulos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="module in modules" :key="module.id"
            class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-2xl font-bold text-gray-800">Módulo {{ module.number }}</h3>
              </div>
              <span :class="`px-3 py-1 rounded-xl text-sm font-medium ${
                module.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'
              }`">
                {{ module.status === 'active' ? 'Activo' : 'Inactivo' }}
              </span>
            </div>

            <div class="flex gap-2">
              <button
                @click="editModule(module)"
                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200"
              >
                Editar
              </button>
              <button
                @click="toggleModuleStatus(module)"
                class="flex-1 px-4 py-2 rounded-xl transition-colors duration-200"
                :class="module.status === 'active'
                  ? 'bg-red-100 text-red-700 hover:bg-red-200'
                  : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
              >
                {{ module.status === 'active' ? 'Desactivar' : 'Activar' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Modal para crear/editar módulo -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
          <div class="bg-white rounded-2xl p-6 w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
              {{ editingModule ? 'Editar Módulo' : 'Nuevo Módulo' }}
            </h2>

            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Módulo</label>
                <input
                  v-model="moduleForm.number"
                  type="text" 
                  class="w-full px-4 py-2 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200"
                  required
                />
              </div>

              <div class="flex gap-3 pt-4">
                <button
                  type="button"
                  @click="closeModal"
                  class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
                >
                  {{ editingModule ? 'Guardar Cambios' : 'Crear Módulo' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    modules: {
        type: Array,
        required: true
    }
});

const modules = ref(props.modules);
const showCreateModal = ref(false);
const editingModule = ref(null);
const moduleForm = ref({
    number: '',
    status: 'active'
});

const handleSubmit = async () => {
    try {
        if (editingModule.value) {
            await axios.put(`/modules/${editingModule.value.id}`, {
                number: String(moduleForm.value.number), // Convertir a string
                status: moduleForm.value.status
            });
        } else {
            await axios.post('/modules', {
                number: String(moduleForm.value.number), // Convertir a string
                status: 'active'
            });
        }

        showCreateModal.value = false;
        window.location.reload();
    } catch (error) {
        console.error('Error:', error.response?.data || error);
        // Opcional: Mostrar mensaje de error al usuario
        alert(error.response?.data?.message || 'Error al procesar la solicitud');
    }
};

const editModule = (module) => {
    editingModule.value = module;
    moduleForm.value = {
        number: module.number,
        status: module.status
    };
    showCreateModal.value = true;
};

const toggleModuleStatus = async (module) => {
    try {
        const newStatus = module.status === 'active' ? 'inactive' : 'active';
        await axios.put(`/modules/${module.id}`, {
            ...module,
            status: newStatus
        });
        window.location.reload();
    } catch (error) {
        console.error('Error:', error);
    }
};

const resetForm = () => {
    editingModule.value = null;
    moduleForm.value = {
        number: '',
        status: 'active'
    };
};

// Llamar a resetForm cuando se cierra el modal
const closeModal = () => {
    showCreateModal.value = false;
    resetForm();
};
</script>
