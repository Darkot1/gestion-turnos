<template>
    <!-- Barra superior -->
    <div class="bg-blue-900 text-black text-2xl font-bold flex justify-center p-6">
      TURNOS
    </div>
  
    <section class="w-screen h-screen bg-gray-200 flex flex-col">
      <!-- Contenido principal -->
      <div class="flex flex-1 justify-between p-6 gap-6">
        <!-- Lista de turnos -->
        <div class="w-2/3 bg-white p-6 flex shadow-2xl rounded-lg h-full">
          <div class="w-2/3 space-y-6 border-r border-gray-400 p-2">
            <p class="text-xl font-bold text-black">TURNO</p>
            <div
              v-for="(turno, index) in turnos"
              :key="index"
              class="bg-blue-500 text-black p-6 text-3xl text-center font-bold rounded-xl transition-transform duration-500 ease-in-out"
              :class="[
                index === 0 ? 'scale-110 shadow-xl' : 'scale-100',
                'hover:scale-105 hover:shadow-xl',
              ]"
            >
              {{ turno.codigo }}
            </div>
          </div>
  
          <div class="w-1/3 space-y-6 p-2">
            <p class="text-xl font-bold text-black">TAQUILLA</p>
            <div
              v-for="(turno, index) in turnos"
              :key="index"
              class="bg-blue-500 text-black p-6 text-3xl text-center font-bold rounded-xl transition-transform duration-500 ease-in-out"
              :class="[
                index === 0 ? 'scale-110 shadow-xl' : 'scale-100',
                'hover:scale-105 hover:shadow-xl',
              ]"
            >
              {{ turno.taquilla }}
            </div>
          </div>
        </div>
  
        <!-- Último turno llamado -->
        <div class="w-1/3 flex flex-col">
          <div class="bg-white p-6 shadow-xl rounded-lg flex flex-col justify-center">
            <div class="bg-gray-300 p-8 text-center rounded-xl transition-transform duration-500 ease-in-out animate-pop">
              <p class="text-2xl font-bold">TURNO &nbsp;&nbsp; TAQUILLA</p>
              <p class="text-6xl font-extrabold mt-6">{{ turnoReciente.codigo }} &nbsp;&nbsp; {{ turnoReciente.taquilla }}</p>
            </div>
          </div>
  
          <!-- CLINICA ESD alineado abajo -->
          <div class="bg-black text-white p-6 text-center text-3xl font-extrabold rounded-lg mt-6">
            CLINICA ESD
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    data() {
      return {
        turnos: [
          { codigo: "R087", taquilla: "01" },
          { codigo: "R088", taquilla: "02" },
          { codigo: "M089", taquilla: "03" },
          { codigo: "M090", taquilla: "04" },
          { codigo: "R091", taquilla: "05" },
        ],
        turnoReciente: { codigo: "R091", taquilla: "05" },
      };
    },
  };
  </script>
  
  <style>
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  @keyframes pop {
    from {
      transform: scale(0.9);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }
  
  .animate-fadeIn {
    animation: fadeIn 0.5s ease-in-out;
  }
  
  .animate-pop {
    animation: pop 0.5s ease-out;
  }
  </style>
  