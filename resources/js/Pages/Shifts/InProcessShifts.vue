<template>
    <div class="min-h-screen bg-blue-50">
        <div class="container mx-auto p-8">
            <!-- Header con reloj -->
            <div class="text-right mb-6">
                <div class="text-6xl font-digital text-blue-900">{{ currentTime }}</div>
            </div>

            <div class="flex gap-8 h-screen">
                <!-- Columna izquierda: Turnos anteriores -->
                <div class="w-1/3 bg-white rounded-xl shadow-xl p-8">
                    <h2 class="text-4xl font-bold mb-8 text-blue-800">Turnos Anteriores</h2>
                    <div class="space-y-6 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <div v-for="shift in previousShifts" :key="shift.id"
                            class="p-6 rounded-xl shadow-md transition-all bg-white border-l-8"
                            :class="getTypeColor(shift.type)">
                            <h3 class="text-4xl font-bold mb-2">Turno {{ shift.number }}</h3>
                            <div class="space-y-3">
                                <p class="text-3xl text-gray-700">{{ shift.user.name }}</p>
                                <p class="text-3xl font-semibold text-blue-800">
                                    Módulo {{ shift.module?.number }}
                                </p>
                                <p class="text-2xl font-medium" :class="getTypeTextColor(shift.type)">
                                    {{ shift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Turno en llamada -->
                <div class="w-2/3">
                    <div v-if="currentShift"
                        class="h-[calc(100vh-120px)] rounded-xl shadow-xl p-16 flex flex-col justify-center items-center bg-white"
                        :class="getTypeBgColor(currentShift.type)">
                        <div class="text-center animate-fade-in">
                            <h2 class="text-9xl font-bold mb-12 text-blue-900">
                                Turno {{ currentShift.number }}
                            </h2>
                            <p class="text-7xl mb-10 text-gray-800">
                                {{ currentShift.user.name }}
                            </p>
                            <p class="text-8xl font-bold mb-8 text-blue-800">
                                Módulo {{ currentShift.module?.number }}
                            </p>
                            <p class="text-5xl font-medium" :class="getTypeTextColor(currentShift.type)">
                                {{ currentShift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                            </p>
                        </div>
                    </div>
                    <div v-else class="h-[calc(100vh-120px)] bg-white rounded-xl shadow-xl p-16 flex justify-center items-center">
                        <p class="text-5xl text-gray-400">No hay turnos en llamada</p>
                    </div>
                </div>
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
    return [...props.shifts]
        .filter(shift => shift.id !== currentShift.value?.id)
        .slice(-8);
});

const currentTime = ref('');

const updateTime = () => {
    currentTime.value = new Date().toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};


onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
});

const getTypeColor = (type) => ({
    'muestras': 'border-purple-500',
    'resultados': 'border-blue-500'
}[type] || 'border-gray-400');

const getTypeTextColor = (type) => ({
    'muestras': 'text-purple-600',
    'resultados': 'text-blue-600'
}[type] || 'text-gray-600');

const getTypeBgColor = (type) => ({
    'muestras': 'bg-purple-50',
    'resultados': 'bg-blue-50'
}[type] || 'bg-white');

const speak = (text) => {
    const synth = window.speechSynthesis;
    synth.cancel();

    const loadVoices = () => {
        return new Promise(resolve => {
            let voices = synth.getVoices();
            if (voices.length !== 0) {
                resolve(voices);
            } else {
                speechSynthesis.addEventListener('voiceschanged', () => {
                    voices = synth.getVoices();
                    resolve(voices);
                });
            }
        });
    };

    loadVoices().then(voices => {

        const spanishVoice = voices.find(voice =>
            voice.lang.includes('es-CO')
        );

        const utterance = new SpeechSynthesisUtterance(text);
        if (spanishVoice) {
            utterance.voice = spanishVoice;
        }
        utterance.lang = 'es-MEX';
        utterance.rate = 0.90;
        utterance.pitch = 1;
        utterance.volume = 1;

        synth.speak(utterance);
    });
};


// Detectar cambios en los turnos
watch(() => props.shifts, (newShifts, oldShifts) => {
    if (!newShifts.length) {
        currentShift.value = null;
        return;
    }

    // Encontrar el turno más reciente en proceso
    const latestProcessShift = [...newShifts]
        .reverse()
        .find(shift => shift.status === 'en proceso');

    if (latestProcessShift && (!currentShift.value || currentShift.value.id !== latestProcessShift.id)) {
        currentShift.value = latestProcessShift;

        speak(`Turno ${latestProcessShift.number}, ${latestProcessShift.user.name}, por favor dirigirse al módulo ${latestProcessShift.module?.number}`);
    }
}, { deep: true, immediate: true });
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

.font-digital {
    font-family: 'Roboto', monospace;
}

.animate-fade-in {
    animation: fadeIn 2s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.98);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

::-webkit-scrollbar {
    width: 16px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 8px;
    border: 3px solid #f1f5f9;
}

::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
