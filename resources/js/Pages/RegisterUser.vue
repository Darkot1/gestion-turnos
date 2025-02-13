<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const name = ref('');
const document = ref('');
const error = ref('');
const success = ref('');

// Teclado numerico
const keys = ref(['1', '2', '3', '4', '5', '6', '7', '8', '9', '0']);

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
        error.value = 'Por favor, ingresa nombre y cédula.';
        return;
    }

    try {
        const response = await axios.post('/users', {
            name: name.value,
            document: document.value
        });

        success.value = response.data.message;
        name.value = '';
        document.value = '';
        error.value = '';

        router.visit(response.data.redirect);
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al registrar usuario.';
    }
};
</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100 p-6">
        <div class="w-full max-w-4xl p-8 bg-white rounded-2xl shadow-lg flex flex-col md:flex-row">
            <!-- Formulario -->
            <div class="w-full md:w-2/3 p-4 flex flex-col justify-center">
                <label class="block mb-3 text-lg font-semibold">Nombre Completo</label>
                <input v-model="name" type="text"
                    class="w-full p-4 border rounded-xl text-lg focus:outline-none focus:ring-4 focus:ring-blue-400" />

                <label class="block mt-5 mb-3 text-lg font-semibold">Numero de identidad</label>
                <input v-model="document" type="text"
                    class="w-full p-4 border rounded-xl text-lg text-center bg-gray-200 cursor-default" readonly />

                <button @click="registerUser" class="mt-8 w-full bg-blue-500 text-white p-4 rounded-xl text-xl font-bold">
                    Pedir Turno
                </button>
            </div>

            <!-- Teclado Numerico -->
            <div class="w-full md:w-1/3 flex flex-col items-center justify-center">
                <div class="grid grid-cols-3 gap-2">
                    <button v-for="num in keys.slice(0, 9)" :key="num" @click="addNumber(num)"
                        class="p-4 text-lg font-bold bg-gray-300 rounded-lg hover:bg-gray-400 active:bg-gray-500 transition">
                        {{ num }}
                    </button>
                    <button @click="addNumber('0')" class="p-4 text-lg font-bold bg-gray-300 rounded-lg col-span-2 hover:bg-gray-400 active:bg-gray-500 transition">
                        0
                    </button>
                    <button @click="deleteNumber" class="p-4 text-lg font-bold bg-red-500 text-white rounded-lg hover:bg-red-900 active:bg-red-900 transition">
                        Borrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
