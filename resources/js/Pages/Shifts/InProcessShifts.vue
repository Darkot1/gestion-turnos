<template>
    <div class="h-screen w-screen bg-gray-100 flex flex-col items-center justify-center overflow-hidden">
        <!-- Navbar fijo arriba -->
        <nav class="fixed top-0 left-0 w-full bg-blue-700 text-white text-center text-4xl font-bold py-4 shadow-lg animate-slide-down">
            TURNOS
        </nav>

        <!-- Contenido -->
        <div class="flex flex-col items-center justify-center flex-grow w-full h-full p-8 pt-20">
            <!-- Reloj -->
            <div class="text-6xl font-mono text-blue-900 mb-6 animate-pulse">
                {{ currentTime }}
            </div>

            <div class="grid grid-cols-3 gap-6 w-full h-[80vh] max-w-7xl">
                <!-- Turnos en proceso -->
                <div class="col-span-1 bg-white rounded-xl shadow-lg p-6 overflow-y-auto max-h-full">
                    <h2 class="text-3xl font-bold text-black mb-2 bg-blue-600 p-4 rounded-xl shadow-2xl">Turnos en proceso</h2>
                    <transition-group name="list" tag="div">
                        <div v-for="shift in previousShifts" :key="shift.id"    
                            class="p-5 mb-4 rounded-xl shadow-md border-l-8 transition-transform transform hover:scale-105 cursor-pointer animate-fade-in"
                            :class="[getTypeColor(shift.type), getBorderColor(shift.type)]">
                            <h3 class="text-2xl font-semibold">Turno {{ shift.number }}</h3>
                            <p class="text-gray-800 text-xl">{{ shift.user.name }}</p>
                            <p class="text-2xl font-bold text-blue-800">Módulo {{ shift.module?.number }}</p>
                            <p class="text-lg font-semibold" :class="getTypeTextColor(shift.type)">
                                {{ shift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                            </p>
                        </div>
                    </transition-group>
                </div>

                <!-- Turno actual -->
                <div class="col-span-2 flex flex-col justify-center items-center bg-white rounded-xl shadow-2xl p-12 border-r-8 border-blue-600 animate-bounce-in">
                    <h2 class="text-3xl font-bold text-black mb-4 bg-blue-600 p-4 rounded-xl shadow-2xl">Turno actual</h2>
                    <transition name="fade" mode="out-in">
                        <div v-if="currentShift" class="text-center animate-scale-in">
                            <h2 class="text-7xl font-bold text-blue-900 mb-6">Turno: {{ currentShift.number }}</h2>
                            <p class="text-5xl text-gray-800 mb-6">{{ currentShift.user.name }}</p>
                            <p class="text-6xl font-bold text-blue-800 mb-4">Módulo: {{ currentShift.module?.number }}</p>
                            <p class="text-3xl font-medium" :class="getTypeTextColor(currentShift.type)">
                                {{ currentShift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                            </p>
                        </div>
                        <div v-else class="text-3xl text-gray-400">No hay turnos en llamada</div>
                    </transition>
                </div>
            </div>

            <!-- Clínica ESD -->
            <div class="col-span-1 flex flex-col justify-center items-center bg-gray-300 rounded-xl shadow-lg p-6 mt-5">
                <h2 class="text-4xl font-bold text-blue-900">Clínica ESD</h2>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    shifts: {
        type: Array,
        default: () => []
    }
});

const currentShift = ref(null);
const previousShifts = computed(() => {
    return [...props.shifts].filter(shift => shift.id !== currentShift.value?.id).slice(-8);
});

const currentTime = ref('');
const updateTime = () => {
    currentTime.value = new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', hour12: true });
};

onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
});

const getTypeColor = (type) => ({
    'muestras': 'bg-purple-100',
    'resultados': 'bg-blue-100'
}[type] || 'bg-gray-200');

const getBorderColor = (type) => ({
    'muestras': 'border-purple-500',
    'resultados': 'border-blue-500'
}[type] || 'border-gray-400');

const getTypeTextColor = (type) => ({
    'muestras': 'text-purple-800',
    'resultados': 'text-blue-800'
}[type] || 'text-gray-800');

watch(() => props.shifts, (newShifts) => {
    const latestShift = [...newShifts].reverse().find(shift => shift.status === 'en proceso');
    if (latestShift && (!currentShift.value || currentShift.value.id !== latestShift.id)) {
        currentShift.value = latestShift;
    }
}, { deep: true, immediate: true });
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 1.5s ease-in-out;
}

.animate-scale-in {
    animation: scaleIn 0.8s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 1s ease-in-out;
}

.animate-slide-down {
    animation: slideDown 0.8s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes scaleIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes bounceIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>