<template>
    <div class="container mx-auto p-6 flex flex-col items-center bg-gray-100 rounded-lg shadow-lg">
        <h1 class="text-4xl font-bold mb-6 text-blue-600">Detalle del Turno</h1>
        <div class="bg-white p-4 rounded-lg shadow text-center w-full max-w-xs">
            <h2 class="text-2xl font-bold mb-2 text-blue-600">Turno: {{ turn.number }}</h2>
            <p class="mb-2">Estado: {{ turn.status }}</p>
            <button @click="printTurn" class="bg-green-500 text-white px-4 py-2 rounded">Imprimir Turno</button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const turn = ref(null);

const loadTurn = async () => {
    try {
        const response = await axios.get(`/turns/${route.params.turnId}`);
        turn.value = response.data.turn;
    } catch (error) {
        console.error('Error cargando turno:', error);
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

onMounted(async () => {
    await loadTurn();
});
</script>

<style scoped>
/* Add your styles here */
</style>
