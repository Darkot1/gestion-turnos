<template>
    <div class="container mx-auto p-6 flex flex-col items-center bg-gray-100 rounded-lg shadow-lg">
        <h1 class="text-4xl font-bold mb-6 text-blue-600">Generar Turno</h1>
        <button @click="generateTurn" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 w-full max-w-xs">Generar Turno</button>
        <div v-if="turn" class="bg-white p-4 rounded-lg shadow text-center w-full max-w-xs">
            <h2 class="text-2xl font-bold mb-2 text-blue-600">Turno: {{ turn.number }}</h2>
            <p class="mb-2">Estado: {{ turn.status }}</p>
            <button @click="printTurn" class="bg-green-500 text-white px-4 py-2 rounded">Imprimir Turno</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const turn = ref(null);

const generateTurn = async () => {
    try {
        const response = await axios.post('/turns');
        turn.value = response.data.turn;
        // Add the turn to the waiting list
        // ...existing code to update the waiting list...
    } catch (error) {
        console.error('Error generando turno:', error);
    }
};

const printTurn = () => {
    if (turn.value) {
        const printWindow = window.open('', '', 'width=600,height=400');
        printWindow.document.write(`<pre>Turno: ${turn.value.number}\nEstado: ${turn.value.status}</pre>`);
        printWindow.document.close();
        printWindow.print();
    }
};
</script>

<style scoped>
/* Add your styles here */
</style>
