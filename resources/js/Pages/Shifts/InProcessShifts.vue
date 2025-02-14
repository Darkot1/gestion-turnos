<template>
  <!-- Nav actualizado -->
  <nav class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xl">
    <div class="max-w-full mx-auto px-8">
      <div class="flex justify-between h-24 items-center">
        <div class="flex items-center space-x-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
          </svg>
          <div>
            <h1 class="text-4xl font-bold text-white">Clínica ESD</h1>
            <p class="text-blue-100 text-lg">Sistema de Turnos</p>
          </div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm px-8 py-4 rounded-xl border border-white/20">
          <div class="text-6xl font-digital font-bold text-white">
            {{ currentTime }}
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-8 flex">
    <!-- Panel Izquierdo: Turnos Anteriores -->
    <div class="w-1/2 p-8">
      <div class="w-full max-w-4xl mx-auto">
        <h2 class="text-6xl font-extrabold text-gray-800 mb-12">Turnos Anteriores</h2>

        <div class="space-y-6">
          <div v-for="shift in previousShifts" :key="shift.id"
            class="w-full group relative px-10 py-8 overflow-hidden rounded-3xl bg-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="relative flex items-center justify-between gap-6">
              <div class="flex items-center gap-8">
                <span class="text-7xl font-bold text-gray-800">{{ shift.number }}</span>
                <div>
                  <p class="text-4xl font-medium text-gray-700">{{ shift.user.name }}</p>
                </div>
              </div>
              <div class="text-4xl font-bold text-blue-600">
                Módulo {{ shift.module?.number }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel Derecho: Turno en Llamada -->
    <div class="w-1/2 p-8 flex items-center justify-center">
      <div v-if="currentShift" class="w-full max-w-3xl text-center space-y-12">
        <h2 class="text-6xl font-extrabold text-gray-800">Turno en Llamada</h2>

        <div class="w-full group relative p-16 overflow-hidden rounded-3xl bg-white shadow-xl">
          <div class="space-y-8">
            <div class="animate-pulse text-[12rem] font-bold text-blue-600">
              {{ currentShift.number }}
            </div>

            <div class="text-7xl font-medium text-gray-700">
              {{ currentShift.user.name }}
            </div>

            <div :class="`inline-block px-3 py-1 rounded-lg text-sm font-medium ${
              currentShift.type === 'muestras' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'
            }`">
              {{ currentShift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
            </div>

            <div class="text-7xl font-bold text-blue-600">
              Módulo {{ currentShift.module?.number }}
            </div>
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
    return props.shifts
        .filter(shift => shift.id !== currentShift.value?.id)
        .slice(0, 6);
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
        const spanishVoice = voices.find(voice => voice.lang.includes('es-CO'));
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

watch(() => props.shifts, (newShifts) => {
    if (!newShifts.length) {
        currentShift.value = null;
        return;
    }

    const latestProcessShift = newShifts[0];


    if (latestProcessShift && (!currentShift.value || currentShift.value.id !== latestProcessShift.id)) {
        currentShift.value = latestProcessShift;
        if (latestProcessShift.user) {
            speak(`Turno ${latestProcessShift.number}, ${latestProcessShift.user.name}, por favor dirigirse al módulo ${latestProcessShift.module?.number}`);
        }
    }
}, { deep: true, immediate: true });
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&display=swap');

.font-digital {
    font-family: 'Roboto Mono', monospace;
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
    width: 12px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 8px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}
</style>
