<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Gestión de Módulos</h1>

        <form @submit.prevent="addModule" class="mb-6">
            <div class="mb-4">
                <label class="block font-bold">Número del Módulo:</label>
                <input v-model="moduleForm.number" type="text" required class="border p-2 w-full rounded" />
            </div>

            <div class="mb-4">
                <label class="block font-bold">Estado:</label>
                <select v-model="moduleForm.status" required class="border p-2 w-full rounded">
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="busy">Ocupado</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                Agregar Módulo
            </button>
        </form>

        <div v-if="message.text" :class="message.type === 'success' ? 'text-green-600' : 'text-red-600'" class="font-semibold mb-4">
            {{ message.text }}
        </div>

        <h2 class="text-2xl font-bold mt-6">Lista de Módulos</h2>
        <div v-if="!modules.length" class="text-gray-500">No hay módulos registrados.</div>
        <ul v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <li v-for="mod in modules" :key="mod.id" class="p-4 bg-white rounded shadow">
                <div class="flex justify-between items-center">
                    <div>
                        <strong>Módulo {{ mod.number }}</strong>
                        <span :class="{
                            'text-green-600': mod.status === 'active',
                            'text-red-600': mod.status === 'inactive',
                            'text-yellow-600': mod.status === 'busy'
                        }" class="ml-2">
                            {{ translateStatus(mod.status) }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-if="mod.status !== 'active'"
                            @click="updateModuleStatus(mod, 'active')"
                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                            Activar
                        </button>
                        <button
                            v-if="mod.status !== 'inactive'"
                            @click="updateModuleStatus(mod, 'inactive')"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            Desactivar
                        </button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
const props = defineProps({
    modules: {
        type: Array,
        default: () => []
    }
});

const moduleForm = ref({
    number: "",
    status: "active"
});

const modules = ref(props.modules);
const message = ref({ text: "", type: "success" });

const translateStatus = (status) => {
    const translations = {
        'active': 'Activo',
        'inactive': 'Inactivo',
        'busy': 'Ocupado'
    };
    return translations[status] || status;
};

const addModule = async () => {
    try {
        const response = await axios.post("/modules", moduleForm.value);
        modules.value.push(response.data.module);
        moduleForm.value = { number: "", status: "active" };
        message.value = { text: "Módulo agregado correctamente", type: "success" };
    } catch (error) {
        message.value = {
            text: error.response?.data?.errors ? Object.values(error.response.data.errors).flat().join('\n') : "Error al crear el módulo",
            type: "error"
        };
    }
};

const updateModuleStatus = async (module, newStatus) => {
    try {
        const response = await axios.put(`/modules/${module.id}`, {
            status: newStatus
        });

        const index = modules.value.findIndex(m => m.id === module.id);
        if (index !== -1) {
            modules.value[index] = response.data.module;
        }

        message.value = {
            text: "Estado del módulo actualizado correctamente",
            type: "success"
        };
    } catch (error) {
        message.value = {
            text: "Error al actualizar el estado del módulo",
            type: "error"
        };
    }
};
</script>
