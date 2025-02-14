<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Turnos Llamados</h1>

            <div v-if="!calledShifts.length" class="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
                No hay turnos llamados
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <ShiftCard
                    v-for="shift in calledShifts"
                    :key="shift.id"
                    :shift="shift"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ShiftCard from '@/Components/ShiftCard.vue';
import axios from 'axios';

const calledShifts = ref([]);

onMounted(async () => {
    try {
        const response = await axios.get('/shifts/called');
        calledShifts.value = response.data.shifts;
    } catch (error) {
        console.error('Error cargando turnos llamados:', error);
    }
});
</script>
