<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>



        <div class="mx-auto max-w-screen-xl w-full px-4 py-2 sm:px-6 lg:px-8">
        
  <div class="mx-auto max-w-full">

  
    

    <h1 class="text-center text-2xl font-bold text-black-600 sm:text-3xl">Oficina</h1>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="mb-0 mt-3 space-y-4 rounded-lg p-4 shadow-2xl sm:p-6 lg:p-8">
      
      <p class="text-center custom-paragraph  text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
          Usuario    - {{ $page.props.auth.user.name }}
      </p>              

             


      <p class="text-center custom-paragraph  text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
          Oficina
      </p>

      <div>

        <div class="relative">
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"            
            placeholder="Nombre "
            v-model="form.nombre"  
            ref="nombre" 
            id="nombre" 
            name="nombre" 
            autocomplete="off"
            required    
            
          />          
        </div>
  

        <div class="relative">
          <label for="asunto">Seccion:</label>
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
            v-model="form.seccion"
            ref="seccion"
            id="seccion"
            name="seccion"
            required
            placeholder="seccion"
            autocomplete="off"
         
            
          />
         
        </div>
        

       
      </div>
      


      <div v-if="importacion">                
                                                    
      </div>
      <div v-else class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="ml-2 absolute animate-spin rounded-full h-10 w-10 border-t-8 border-red-500">
                                                                <!-- Contenido del spinner -->
            </div>
                          
                          
      </div>




      <button
        type="submit"
        class="block w-full custom-paragraph  rounded-lg bg-indigo-600 px-5 py-3 text-sm font-medium text-white"
      >
        Grabar
      </button>


     

     
    </form>
  </div>
</div>

    </AuthenticatedLayout>
</template>
<script>

const api = import.meta.env.VITE_API;
import Swal from 'sweetalert2'


export default {
  props: ['auth'],
  data() {
    return {
      emailValue: '', // Inicializa el valor del input vacío     
      importacion: true,
      email: '',    
              
      form:{
            id:'',
            nombre:'',
            seccion :'',            
            
          },              
    };
  },
  mounted() {
    // Establece el valor del input al valor del nombre del usuario
   // this.obtenerSaldos();
    if (this.auth && this.auth.user && this.auth.user.name) {
          this.emailValue = this.auth.user.email;
    }
    if (this.$refs.codigo) {
      this.$refs.codigo.focus();
    }
    const id = this.$page.props.id;


    if (id) {
        axios.get(`/api/oficina/${id}`)
            .then(response => {
                // Asigna los detalles del movimiento al formulario para la edición
                //this.form = response.data;

                this.form.nombre=response.data[0].nombre
                this.form.seccion=response.data[0].seccion
                this.form.id=response.data[0].id

                console.log(response.data)
            })
            .catch(error => {
                console.error('Error al obtener los detalles del movimiento:', error);
            });
    }


  },
  methods: {
    



    //Grbacion del formulario
    submit(event) {
          event.preventDefault();    
          // Crear un nuevo objeto FormData
          this.importacion= false
          this.form.userid=JSON.stringify(this.auth.user.id);


          //console.log(this.form);

          

          axios.put('/api/oficinamodifica/'+this.id, this.form)
          .then(response => {
            // Maneja la respuesta de la API según sea necesario
            //console.log(response.data);
            //limpiar el formulario
            // Después de una grabación exitosa, resetea los valores del formulario
            this.importacion= true
            this.form.nombre='';
            this.form.seccion='';
            
            this.$nextTick(() => {
            this.$refs.nombre.focus();
            });

           // this.obtenerSaldos();
            Swal.fire({
                title: 'Mensaje',
                text: 'Documento Grabado',
                icon: 'success',
                timer: 1000, // Tiempo en milisegundos (en este caso, 5 segundos)
                timerProgressBar: true,
                showConfirmButton: false // No muestra el botón de confirmación
              })

              //luego de grabar del registro procederemos a enviar el correo de registro
              //this.email= this.auth.user.email;

              this.$inertia.visit('/listadom', { method: 'get' }); // Esto redirigirá a la ruta principal


              

          })
          .catch(error => {
            // Maneja el error en caso de que ocurra
            this.importacion= true
            this.form.nombre = '';
            this.form.seccion = '';
            
            Swal.fire({
                    icon: 'error',
                    title: 'Error en la grabacion del documento',
                    text: 'Ocurrio un error al intentar grabar el registro revise el tamaño de archivo',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('El usuario aceptó el mensaje de error.');
                    }
                });
          });


      


    }, //finde procedimiento de envio del formulario



  },


};
</script>

<style>
  .custom-paragraph {
    background-color: #eb5767;
  }
</style>