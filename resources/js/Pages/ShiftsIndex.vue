<template>
  <!-- Nav -->
  <nav class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-center h-20 items-center">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-white">¿Qué tipo de turno necesitas?</h1>
          <div class="flex items-center justify-center gap-2 mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <p class="text-blue-100">Seleccione el tipo de servicio</p>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="min-h-[70vh] bg-gradient-to-br from-blue-50 to-indigo-100 p-6 flex items-center justify-center">
    <div class="w-full max-w-4xl">
      <div class="flex flex-col md:flex-row gap-6 justify-center items-center">
        <!-- Botón Muestras -->
        <button
          @click="requestShift('muestras')"
          class="w-64 group relative px-8 py-6 overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-blue-600 transition-transform duration-300 transform translate-y-full group-hover:translate-y-0"></div>
          <span class="relative flex flex-col items-center gap-3 text-blue-600 group-hover:text-white transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <span class="text-xl font-bold">Muestras</span>
          </span>
        </button>

        <!-- Botón Resultados -->
        <button
          @click="requestShift('resultados')"
          class="w-64 group relative px-8 py-6 overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
        >
          <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 to-emerald-600 transition-transform duration-300 transform translate-y-full group-hover:translate-y-0"></div>
          <span class="relative flex flex-col items-center gap-3 text-emerald-600 group-hover:text-white transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-xl font-bold">Resultados</span>
          </span>
        </button>
      </div>

      <!-- Mensajes -->
      <div class="mt-8 text-center">
        <p v-if="successMessage" class="text-emerald-600 bg-emerald-50 px-4 py-3 rounded-lg font-medium animate-fade-in">
          {{ successMessage }}
        </p>
        <p v-if="errorMessage" class="text-red-600 bg-red-50 px-4 py-3 rounded-lg font-medium animate-fade-in">
          {{ errorMessage }}
        </p>
      </div>
    </div>
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
    setTimeout(() => {
      router.visit("/register-user");
      successMessage.value = "";
    }, 1000);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Error al generar turno.";
    setTimeout(() => {
      errorMessage.value = "";
    }, 3000);
  }
};
</script>
