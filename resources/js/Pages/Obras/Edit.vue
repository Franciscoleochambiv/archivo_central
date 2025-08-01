<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>
<template>
  <Head title="Onp" />
  <AuthenticatedLayout>
    <div class="mx-auto max-w-screen-xl w-full px-4 py-2 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-full">
        <h1 class="text-center text-2xl font-bold text-black-600 sm:text-3xl">Obras</h1>
        <form @submit.prevent="submit" enctype="multipart/form-data" class="mb-0 mt-3 space-y-4 rounded-lg p-4 shadow-2xl sm:p-6 lg:p-8">
          <p class="text-center custom-paragraph text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
            Usuario - {{ $page.props.auth.user.name }}
          </p>
          <p class="text-center custom-paragraph text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
            Obras
          </p>
          <div>
            <div class="relative">
              <label for="nombre">Nombre:</label>
              <input
                type="text"
                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
                placeholder="Nombre"
                v-model="form.nombre"
                ref="nombre"
                id="nombre"
                name="nombre"
                required
              />
            </div>

            <!-- Agrupación de Latitud, Longitud y Radio -->
            <div class="flex flex-wrap gap-4 mt-4">
              <div class="relative flex-1 min-w-[250px]">
                <label for="latitud">Latitud:</label>
                <input
                  type="text"
                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 text-sm shadow-sm"
                  placeholder="Latitud"
                  v-model="form.latitud"
                  ref="latitud"
                  id="latitud"
                  name="latitud"
                  required
                />
              </div>
              <div class="relative flex-1 min-w-[250px]">
                <label for="longitud">Longitud:</label>
                <input
                  type="text"
                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 text-sm shadow-sm"
                  placeholder="Longitud"
                  v-model="form.longitud"
                  ref="longitud"
                  id="longitud"
                  name="longitud"
                  required
                />
              </div>
              <div class="relative flex-1 min-w-[250px]">
                <label for="radio">Radio:</label>
                <input
                  type="text"
                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 text-sm shadow-sm"
                  placeholder="Radio"
                  v-model="form.radio"
                  ref="radio"
                  id="radio"
                  name="radio"
                  required
                />
              </div>
            </div>

            <div class="relative mt-4">
              <label for="direccion">Dirección:</label>
              <input
                type="text"
                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
                placeholder="Dirección"
                v-model="form.direccion"
                ref="direccion"
                id="direccion"
                name="direccion"
                required
              />
            </div>
          </div>

          <div v-if="importacion"></div>
          <div v-else class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="ml-2 absolute animate-spin rounded-full h-10 w-10 border-t-8 border-red-500"></div>
          </div>

          <button
            type="submit"
            class="block w-full custom-paragraph rounded-lg bg-indigo-600 px-5 py-3 text-sm font-medium text-white"
          >
            Grabar
          </button>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


<script>
import Swal from 'sweetalert2';

export default {
  props: ['auth'],
  data() {
    return {
      emailValue: '',      
      importacion: true,
      email: '',  
      form: {
        id: '', // Asegúrate de inicializar el ID aquí
        nombre: '',     
        latitud: 0,
        longitud: 0,
        radio: 0,
        direccion: '',                    
      },              
    };
  },
  mounted() {
    if (this.auth && this.auth.user && this.auth.user.email) {
      this.emailValue = this.auth.user.email;
    }

    const id = this.$page.props.id;

    if (id) {
      axios.get(`/api/obra/${id}`)
        .then(response => {
          // Asigna los detalles al formulario para la edición
          this.form.nombre = response.data[0].nombre;
          this.form.id = response.data[0].id; // Asegúrate de asignar el ID aquí
          this.form.latitud = Number(response.data[0].latitud);
          this.form.longitud = Number(response.data[0].longitud);
          this.form.radio = Number(response.data[0].radio);
          this.form.direccion = response.data[0].direccion;
        })
        .catch(error => {
          console.error('Error al obtener los detalles:', error);
        });
    }
  },
  methods: {
    // Envío del formulario
    submit(event) {
      event.preventDefault();

      this.importacion = false;
      this.form.userid = JSON.stringify(this.auth.user.id);

      console.log(this.$page.props.id);

      // Usa el ID del formulario para la solicitud
      axios.put(`/api/obramodifica/${this.$page.props.id}`, this.form)
        .then(response => {
          this.importacion = true;
          // Limpia el formulario tras el éxito
          this.form.nombre = '';            
          this.form.latitud = 0;
          this.form.longitud = 0;
          this.form.radio = 0;
          this.form.direccion = '';

          this.$nextTick(() => {
            if (this.$refs.nombre_cargo) this.$refs.nombre_cargo.focus();
          });

          Swal.fire({
            title: 'Mensaje',
            text: 'Documento Grabado',
            icon: 'success',
            timer: 1000,
            timerProgressBar: true,
            showConfirmButton: false,
          });

          // Redirección después del éxito
          this.$inertia.visit('/listobras', { method: 'get' });
        })
        .catch(error => {
          this.importacion = true;
          // Limpia el formulario tras el error
          this.form.nombre = '';            
          this.form.latitud = 0;
          this.form.longitud = 0;
          this.form.radio = 0;
          this.form.direccion = '';

          const errorMessage = error.response?.data?.message || error.message || 'Error desconocido';
          const errorDetails = error.response?.data || {};

          Swal.fire({
            icon: 'error',
            title: 'Error en la grabación del documento',
            text: `Ocurrió un error al intentar grabar: ${errorMessage}`,
            footer: `<pre>${JSON.stringify(errorDetails, null, 2)}</pre>`,
            confirmButtonText: 'Aceptar',
          }).then((result) => {
            if (result.isConfirmed) {
              console.error('Detalles del error:', error);
            }
          });
        });
    },
  },
};
</script>


<style>
  .custom-paragraph {
    background-color: #eb5767;
  }
</style>