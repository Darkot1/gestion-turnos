<script setup>
import { ref } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const name = ref("");
const document = ref("");
const error = ref("");
const success = ref("");

// Teclado numerico
const keys = ref(["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"]);

const addNumber = (num) => {
  if (document.value.length < 12) {
    document.value += num;
  }
};

const deleteNumber = () => {
  document.value = document.value.slice(0, -1);
};

const registerUser = async () => {
  if (!name.value || !document.value) {
    error.value = "Por favor, ingresa nombre y cédula.";
    return;
  }

  try {
    const response = await axios.post("/users", {
      name: name.value,
      document: document.value,
    });

    success.value = response.data.message;
    name.value = "";
    document.value = "";
    error.value = "";

    router.visit(response.data.redirect);
  } catch (err) {
    error.value = err.response?.data?.message || "Error al registrar usuario.";
  }
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6 flex items-center justify-center">
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl overflow-hidden">
      <div class="flex flex-col md:flex-row">
        <!-- Formulario -->
        <div class="w-full md:w-2/3 p-8 md:p-12">
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
              <input
                v-model="name"
                type="text"
                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 outline-none"
                placeholder="Ingrese su nombre completo"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Número de Identidad</label>
              <input
                v-model="document"
                type="text"
                class="w-full px-4 py-3 rounded-xl bg-gray-50 border-2 border-gray-200 text-center text-lg font-medium"
                readonly
              />
            </div>

            <button
              @click="registerUser"
              class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 focus:ring-4 focus:ring-blue-200"
            >
              Confirmar Registro
            </button>

            <!-- Mensajes -->
            <div v-if="error || success" class="mt-4">
              <p v-if="error" class="text-red-600 bg-red-50 px-4 py-3 rounded-lg text-sm">
                {{ error }}
              </p>
              <p v-if="success" class="text-emerald-600 bg-emerald-50 px-4 py-3 rounded-lg text-sm">
                {{ success }}
              </p>
            </div>
          </div>
        </div>

        <!-- Teclado Numérico -->
        <div class="w-full md:w-1/3 bg-gray-50 p-8">
          <div class="grid grid-cols-3 gap-3">
            <button
              v-for="num in keys.slice(0, 9)"
              :key="num"
              @click="addNumber(num)"
              class="aspect-square rounded-2xl bg-white border-2 border-gray-100 text-xl font-bold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-blue-200 active:bg-blue-50 transition-all duration-150"
            >
              {{ num }}
            </button>
            <button
              @click="addNumber('0')"
              class="col-span-2 rounded-2xl bg-white border-2 border-gray-100 text-xl font-bold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-blue-200 active:bg-blue-50 transition-all duration-150"
            >
              0
            </button>
            <button
              @click="deleteNumber"
              class="aspect-square rounded-2xl bg-red-500 text-white font-bold hover:bg-red-600 active:bg-red-700 transition-colors duration-150"
            >
              ←
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
