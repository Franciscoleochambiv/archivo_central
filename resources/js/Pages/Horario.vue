<template>
  

  <div class="relative min-h-screen bg-gray-100 flex flex-col justify-center items-center px-4">
    <!-- Encabezado -->
    <img src="/asset/lolo.jpeg" alt="Logo de la Municipalidad" class="max-w-xs mx-auto mb-4" />
    <h2 class="text-xl font-semibold text-gray-800 mb-2 text-center">Control de Asistencia</h2>

    <!-- Resumen flotante -->
    <div v-if="resumenAsistencia.length" class="absolute top-4 right-4 bg-white border rounded p-4 shadow w-64 text-left">
      <h3 class="text-sm font-bold text-gray-700 mb-2">📝 Resumen de hoy</h3>
      <ul class="text-sm text-gray-800 space-y-1">
        <li v-for="(item, index) in resumenAsistencia" :key="index">
          <strong>{{ item.etiqueta }}:</strong> {{ item.hora }}
        </li>
      </ul>
    </div>

    <!-- Caja principal -->
    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full text-center mt-6">
      <!-- Reloj -->

      <h1 class="text-5xl font-bold text-red-400 mb-8">{{ horaActual }}</h1>


      <!-- Token -->
      <div v-if="!tokenValidado">
        <h3 class="text-xl font-semibold text-red-800 mb-4">🔒 Ingresar token de acceso</h3>
        <input
          v-model="tokenEntrada"
          placeholder="Token de acceso"
          class="border px-4 py-2 rounded w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <button
          @click="guardarToken"
          class="bg-red-400 text-white px-6 py-2 rounded hover:bg-red-700 transition w-full"
        >
          Guardar
        </button>
      </div>

      <!-- DNI y botón -->
      <div v-else>
       <input
          ref="inputDni"
          v-model="dni"
          placeholder="Ingrese DNI"
          maxlength="8"
          class="w-full text-center border border-gray-300 px-4 py-3 rounded text-xl mb-4 
                focus:outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-400"
          @keyup.enter="buscarUsuario"
        />


        <div v-if="nombreCompleto" class="text-xl font-semibold text-gray-800 mb-4">
          <span class="text-red-500 mr-2 hover:shadow-md hover:shadow-red-200"></span>

          {{ nombreCompleto }}
        </div>

        <!-- Spinner -->
        <div v-if="buscando" class="flex justify-center mt-4">
          <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
          </svg>
        </div>

       <button
          ref="btnRegistrar"
          v-if="usuarioValido"
          @click="registrarIngreso"
          class="px-4 py-2 bg-red-200 dark:bg-red-800/50 rounded hover:bg-red-600 
                focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 
                transition"
        >
          Registrar Ingreso
        </button>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-10 text-sm text-gray-500 text-center">
      Sfsystem 
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  props: {
    clientToken: String
  },
  data() {
    return {
      horaActual: '',
      dni: '',
      nombreCompleto: '',
      usuarioValido: false,
      tokenEntrada: '',
      tokenValidado: false,
      buscando: false,
      resumenAsistencia: []
    };
  },
  created() {
     this.$nextTick(() => {
    if (this.tokenValidado && this.$refs.inputDni) {
      this.$refs.inputDni.focus();
    }})

    this.actualizarHora();
    setInterval(this.actualizarHora, 1000);

    const tokenCookie = this.obtenerCookie('token_ingreso');
    //console.log('Cookie encontrada:', tokenCookie);
    //console.log('Token esperado:', this.clientToken);
    this.tokenValidado = tokenCookie !== null && tokenCookie === this.clientToken;

    

  },
  methods: {
    actualizarHora() {
      const ahora = new Date();
      this.horaActual = ahora.toLocaleTimeString('es-PE', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    },


   obtenerCookie(nombre) {
  const cookies = document.cookie.split(';');
  for (let c of cookies) {
    const [key, value] = c.trim().split('=');
    if (key === nombre) return value;
  }
  return null;
},


    guardarToken() {
      const expira = new Date();
      expira.setFullYear(expira.getFullYear() + 1);
      document.cookie = `token_ingreso=${this.tokenEntrada}; path=/; expires=${expira.toUTCString()}; SameSite=Strict`;
      console.log("Token guardado:", this.tokenEntrada);
      Swal.fire({
        icon: 'success',
        title: 'Token guardado correctamente',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        location.reload();
      });
    },
    async buscarUsuario() {
      this.usuarioValido = false;
      this.buscando = true;
      this.nombreCompleto = '';

      if (this.dni.length !== 8) {
        this.buscando = false;
        Swal.fire({
          icon: 'warning',
          title: 'DNI inválido',
          text: 'Debe tener exactamente 8 dígitos.'
        });
        
        return;
      }

      try {
        const res = await fetch(`/api/personaldni/${this.dni}`);
        const data = await res.json();
        this.nombreCompleto = `${data.nombres} ${data.apellidos}`;
        this.device_user_id = data.device_user_id;
        this.usuarioValido = true;
        this.$nextTick(() => {
          this.$refs.btnRegistrar?.focus();
        });

         

        const resumenRes = await fetch('/api/asistencia/resumen', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            device_user_id: this.device_user_id,
            fecha: new Date().toISOString().split('T')[0]
        })
        });

        const resumenData = await resumenRes.json();

        console.log(resumenData)
        this.resumenAsistencia = resumenData.resumen || [];



      } catch (error) {
        
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se encontró device id del usuario.'
        }).then(() => {
        location.reload();
      });
        console.error(error);
      } finally {
        this.buscando = false;
      }
    },
    async registrarIngreso() {
      try {
        const payload = {
          device_user_id: this.device_user_id
        };

        const res = await fetch('/api/asistencia/registra', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CLIENT-TOKEN': this.clientToken
          },
          body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (res.ok) {
          Swal.fire({
            icon: 'success',
            title: 'Registrado',
            text: result.message || 'Ingreso registrado exitosamente',
            timer: 2500
              }).then(() => {
            location.reload();
          });;

         
         } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message || 'Error al registrar ingreso'
          }).then(() => {
        location.reload();
      });
        }

        

        this.usuarioValido = false;
        this.dni = '';
        this.nombreCompleto = '';
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo conectar al servidor.'
        });
         this.$nextTick(() => {
              this.$refs.inputDni?.focus();
            });
        console.error(error);
      }
    }
  }
};
</script>

<style scoped>
body {
  font-family: 'Segoe UI', sans-serif;
}
</style>
