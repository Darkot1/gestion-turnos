<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6 flex flex-col items-center bg-gray-100 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold mb-6 text-blue-600">Gestión de Módulos</h1>

            <div v-if="message.text"
                :class="[
                    'mb-4 p-4 rounded-lg',
                    message.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                ]">
                {{ message.text }}
            </div>

            <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Agregar y Asignar Módulo</h2>
                <form @submit.prevent="handleSubmit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <div class="mb-4">
                        <label for="number" class="block text-sm font-medium text-gray-700">Número</label>
                        <input type="text" id="number" v-model="form.number" class="mt-1 block w-full" required>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select id="status" v-model="form.status" class="mt-1 block w-full" required>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                            <option value="busy">Ocupado</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label>
                            Asignar a:
                            <select v-model="assignedUser" class="mt-1 block w-full">
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }} ({{ user.role }})
                                </option>
                            </select>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Agregar y Asignar</button>
                    </div>
                </form>
            </div>

            <div class="w-full max-w-4xl mt-8 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Lista de Módulos</h2>
                <div v-if="!modules.length" class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                    No hay módulos disponibles
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="module in modules" :key="module.id" class="bg-gray-50 p-4 rounded-lg shadow">
                        <h3 class="text-xl font-bold mb-2 text-blue-600">Módulo {{ module.number }}</h3>
                        <p class="mb-2">Estado: {{ module.status }}</p>
                        <button @click="handleDelete(module.id)" class="bg-red-500 text-white px-4 py-2 rounded">Eliminar</button>
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

const form = ref({
    number: '',
    status: 'active'
});
const modules = ref([]);
const message = ref({ text: '', type: 'success' });

const loadModules = async () => {
    try {
        const response = await axios.get('/modules');
        modules.value = response.data.modules;
    } catch (error) {
        console.error('Error cargando módulos:', error);
        message.value = {
            text: 'Error al cargar los módulos disponibles',
            type: 'error'
        };
    }
};

const handleSubmit = async () => {
    try {
        const response = await axios.post('/modules', form.value);
        modules.value.push(response.data.module);
        form.value.number = '';
        form.value.status = 'active';
        message.value = {
            text: 'Módulo agregado y asignado correctamente',
            type: 'success'
        };
    } catch (error) {
        console.error('Error agregando módulo:', error);
        message.value = {
            text: 'Error al agregar el módulo',
            type: 'error'
        };
    }
};

const handleDelete = async (moduleId) => {
    try {
        await axios.delete(`/modules/${moduleId}`);
        modules.value = modules.value.filter(module => module.id !== moduleId);
        message.value = {
            text: 'Módulo eliminado correctamente',
            type: 'success'
        };
    } catch (error) {
        console.error('Error eliminando módulo:', error);
        message.value = {
            text: 'Error al eliminar el módulo',
            type: 'error'
        };
    }
};

const assignedUser = ref(null);
const users = ref([
    { id: 1, name: 'John Doe', role: 'employee' },
    { id: 2, name: 'Jane Smith', role: 'admin' },
    // Add more users as needed
]);

const assignModule = () => {
    if (assignedUser.value) {
        const user = users.value.find(user => user.id === assignedUser.value);
        if (user.role === 'employee') {
            alert(`Módulo asignado a ${user.name}`);
        } else {
            alert('Solo los usuarios con el rol "employee" pueden ser asignados a este módulo.');
        }
    } else {
        alert('Por favor, seleccione un usuario para asignar el módulo.');
    }
};

onMounted(async () => {
    await loadModules();
});
</script>
