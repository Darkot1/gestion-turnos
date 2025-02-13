<template>
  <!-- Nav  -->
  <nav class="bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20 items-center">
        <div class="flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
          </svg>
          <div>
            <h1 class="text-3xl font-bold text-white">Clínica ESD</h1>
            <p class="text-blue-100 text-sm">Sistema de Turnos</p>
          </div>
        </div>
        <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl border border-white/20">
          <div class="text-4xl font-digital font-bold text-white">
            {{ currentTime }}
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6 flex">
    <!-- Panel Izquierdo: Turnos Anteriores -->
    <div class="w-1/2 p-6">
      <div class="w-full max-w-3xl mx-auto">
        <h2 class="text-4xl font-extrabold text-gray-800 mb-8">Turnos Anteriores</h2>

        <div class="space-y-4">
          <div v-for="shift in previousShifts" :key="shift.id"
            class="w-full group relative px-8 py-6 overflow-hidden rounded-2xl bg-white shadow-lg hover:shadow-xl transition-all duration-300">
            <div class="relative flex items-center justify-between gap-4">
              <div class="flex items-center gap-4">
                <span class="text-5xl font-bold text-gray-800">{{ shift.number }}</span>
                <div class="space-y-1">
                  <span :class="`inline-block px-4 py-2 rounded-xl text-sm font-medium ${
                    shift.type === 'muestras' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'
                  }`">
                    {{ shift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
                  </span>
                  <p class="text-lg text-gray-600">{{ shift.user.name }}</p>
                </div>
              </div>
              <div class="text-2xl font-bold text-blue-600">
                Módulo {{ shift.module?.number }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Panel Derecho: Turno en Llamada -->
    <div class="w-1/2 p-6 flex items-center justify-center">
      <div v-if="currentShift" class="w-full max-w-2xl text-center space-y-8">
        <h2 class="text-4xl font-extrabold text-gray-800">Turno en Llamada</h2>

        <div class="w-full group relative p-12 overflow-hidden rounded-2xl bg-white shadow-xl">
          <div class="space-y-6">
            <div class="animate-pulse text-9xl font-bold text-blue-600">
              {{ currentShift.number }}
            </div>

            <div :class="`inline-block px-6 py-3 rounded-xl text-2xl font-medium ${
              currentShift.type === 'muestras' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'
            }`">
              {{ currentShift.type === 'muestras' ? 'Muestras' : 'Resultados' }}
            </div>

            <div class="text-3xl text-gray-700">
              {{ currentShift.user.name }}
            </div>

            <div class="text-5xl font-bold text-blue-600">
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
    return [...props.shifts]
        .filter(shift => shift.id !== currentShift.value?.id)
        .slice(-6);
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
