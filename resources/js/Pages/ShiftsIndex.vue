<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 p-6">
        <h1 class="text-3xl font-bold mb-6">Selecciona el tipo de turno</h1>

        <div class="flex space-x-4">
            <button
                @click="requestShift('muestras')"
                class="bg-blue-500 text-white p-4 rounded-lg text-xl font-bold hover:bg-blue-700 transition"
            >
                Muestras
            </button>
            <button
                @click="requestShift('resultados')"
                class="bg-green-500 text-white p-4 rounded-lg text-xl font-bold hover:bg-green-700 transition"
            >
                Resultados
            </button>
        </div>

        <p v-if="successMessage" class="text-green-600 font-semibold mt-4">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-red-600 font-semibold mt-4">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3"; 

const successMessage = ref("");
const errorMessage = ref("");

const requestShift = async (type) => {
    try {
        const response = await axios.post("/shifts", { type });
        successMessage.value = response.data.message;
        errorMessage.value = "";

        setTimeout(() => {
            router.visit("/register-user");
        }, 1000);
    } catch (error) {
        errorMessage.value = error.response?.data?.message || "Error al generar turno.";
        successMessage.value = "";
    }
};
</script>
