<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6 flex flex-col items-center bg-gray-100 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold mb-6 text-blue-600">Gestión de Turnos</h1>

            <div v-if="message.text"
                :class="[message.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']"
                class="mb-4 p-4 rounded-lg">
                {{ message.text }}
            </div>

            <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Generar Turno</h2>
                <button @click="generateTurn" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 w-full">Generar Turno</button>
            </div>

            <div class="w-full max-w-4xl mt-8 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Lista de Turnos</h2>
                <div v-if="!shifts.length" class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                    No hay turnos disponibles
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="shift in shifts" :key="shift.id" class="bg-gray-50 p-4 rounded-lg shadow">
                        <h3 class="text-xl font-bold mb-2 text-blue-600">Turno {{ shift.number }}</h3>
                        <p class="mb-2">Estado: {{ shift.status }}</p>
                        <button @click="handleDelete(shift.id)" class="bg-red-500 text-white px-4 py-2 rounded">Eliminar</button>
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

const shifts = ref([]);
const message = ref({ text: '', type: 'success' });

const loadShifts = async () => {
    try {
        const response = await axios.get('/shifts');
        shifts.value = response.data.shifts;
    } catch (error) {
        console.error('Error cargando turnos:', error);
        message.value = {
            text: 'Error al cargar los turnos disponibles',
            type: 'error'
        };
    }
};

const generateTurn = async () => {
    try {
        const response = await axios.post('/turns');
        shifts.value.push(response.data.turn);
        message.value = {
            text: 'Turno generado correctamente',
            type: 'success'
        };
    } catch (error) {
        console.error('Error generando turno:', error);
        message.value = {
            text: 'Error al generar el turno',
            type: 'error'
        };
    }
};

const handleDelete = async (shiftId) => {
    try {
        await axios.delete(`/shifts/${shiftId}`);
        shifts.value = shifts.value.filter(shift => shift.id !== shiftId);
        message.value = {
            text: 'Turno eliminado correctamente',
            type: 'success'
        };
    } catch (error) {
        console.error('Error eliminando turno:', error);
        message.value = {
            text: 'Error al eliminar el turno',
            type: 'error'
        };
    }
};

onMounted(async () => {
    await loadShifts();
});
</script>
