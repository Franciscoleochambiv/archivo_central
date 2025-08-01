<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

      
      <main class="py-6 px-4 sm:p-6 md:py-10 md:px-8">
  <div class="max-w-4xl mx-auto grid grid-cols-1 lg:max-w-5xl lg:gap-x-20 lg:grid-cols-2">
    <div class="relative p-3 col-start-1 row-start-1 flex flex-col-reverse rounded-lg bg-gradient-to-t from-black/75 via-black/0 sm:bg-none sm:row-start-2 sm:p-0 lg:row-start-1">
      <h1 class="mt-1 text-lg font-semibold text-white sm:text-slate-900 md:text-2xl dark:sm:text-white">Sistema de Archivo Central </h1>
      <p class="text-sm leading-4 font-medium text-white sm:text-slate-500 dark:sm:text-slate-400">Entire house</p>
    </div>
    

    <div class="bg-white p-4   mt-4 mb-4 ml-4 mr-4 rounded-lg ">

            <div v-if="estado">                
                
            </div>


        <div v-else class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ">
          
          <div class="ml-2 animate-spin rounded-full h-20 w-20 border-t-8 border-blue-500">    
          </div>
        </div>
      </div>
   
    <dl class="mt-4 text-xs font-medium flex items-center row-start-2 sm:mt-1 sm:row-start-3 md:mt-2.5 lg:row-start-2">
      <dt class="sr-only">Reviews</dt>
      <dd class="text-indigo-600 flex items-center dark:text-indigo-400">
        <svg width="24" height="24" fill="none" aria-hidden="true" class="mr-1 stroke-current dark:stroke-indigo-500">
          <path d="m12 5 2 5h5l-4 4 2.103 5L12 16l-5.103 3L9 14l-4-4h5l2-5Z"  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span>4.89 <span class="text-slate-400 font-normal">(128)</span></span>
      </dd>
      <dt class="sr-only">Location</dt>
      <dd class="flex items-center">
        <svg width="2" height="2" aria-hidden="true" fill="currentColor" class="mx-3 text-slate-300">
          <circle cx="1" cy="1" r="1" />
        </svg>
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 text-slate-400 dark:text-slate-500" aria-hidden="true">
          <path d="M18 11.034C18 14.897 12 19 12 19s-6-4.103-6-7.966C6 7.655 8.819 5 12 5s6 2.655 6 6.034Z" />
          <path d="M14 11a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
        </svg>
        Cusco Peru
      </dd>
    </dl>
    <div class="mt-4 col-start-1 row-start-3 self-center sm:mt-0 sm:col-start-2 sm:row-start-2 sm:row-span-2 lg:mt-6 lg:col-start-1 lg:row-start-3 lg:row-end-4">
      <button type="button" class="bg-red-600 text-white text-sm leading-6 font-medium py-2 px-3 rounded-lg">Disponible</button>
    </div>
    <p class="mt-4 text-sm leading-6 col-start-1 sm:col-span-2 lg:mt-6 lg:row-start-4 lg:col-span-1 dark:text-slate-400">
     
  <br>

      
</p>



<!-- Ajusta el tamaño del logo de Yape -->
<img src="/asset/yape.jpg" alt="Logo de Yape" class="mx-auto max-w-full h-auto w-20 h-20">  
<br>

    
    <button type="button" @click="mostrarModalPago" :class="botonActivo ? 'bg-red-600' : 'bg-gray-400'" :disabled="!botonActivo" class="bg-blue-400 text-white text-sm leading-6 font-medium py-2 px-3 rounded-lg">COSTO MENSUAL: 50</button>

   
  </div>
</main>


       

    </AuthenticatedLayout>
</template>
<script>

const api = import.meta.env.VITE_API;
import Swal from 'sweetalert2'


export default {
  props: ['auth'],
  data() {
    return {
      
      botonActivo :false,
      mensajeBoton :'',
      telefono :'',
      otp : '',
      monto :600,
      estado:true,

      emailValue: '', // Inicializa el valor del input vacío     
      importacion: true,
      email: '',    
      saldos: {
        ingresos: 0,
        egresos: 0
      },     
      form:{
            id_meta:'',
            cod_meta:'',
            meta :'',
            fecha :'',
            cantidad :'',
            tipo_doc :'',
            tipo_combustible :'',
            nro_doc :'',
            detalle :'',
            userid:'',
            
          },    
          sugerencias: [],  
    };
  },
  async mounted() {
    // Establece el valor del input al valor del nombre del usuario
  //  this.obtenerSaldos();


    if (this.auth && this.auth.user && this.auth.user.name) {
          this.emailValue = this.auth.user.email;
    }
    console.log(this.auth.user.id)
 
  await this.verificarEstadoBotonPago();

        
          if (!this.$page.props.auth.user) {
             // Redirigir al login si no hay un usuario autenticado
             window.location.href = '/login';
          }
         
   
  },
  methods: {

    async mostrarModalPago() {
  const { value: formValues } = await Swal.fire({
    title: 'Pago por Yape: S/ '+this.monto/100,
    html: `
      <div class="relative">
        <input
          type="text"
          class="w-54 rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm mb-3"
          placeholder="Número de contacto"
          id="telefono"
        />
      </div>
      <div class="relative">
        <input
          type="text"
          class="w-54 rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm mb-3"
          placeholder="Código de Aprobación"
          id="otp"
        />
      </div>
      <div class="relative">
        <input
          type="text"
          class="w-54 rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
          placeholder="DNI (8 caracteres)"
          id="dni"
        />
      </div>
    `,
    focusConfirm: false,
    preConfirm: () => {
      const telefono = document.getElementById('telefono').value;
      const otp = document.getElementById('otp').value;
      const dni = document.getElementById('dni').value;

      // Validaciones personalizadas
      if (!telefono) {
        Swal.showValidationMessage('Por favor, ingresa el número de contacto');
        return false; // Detener el envío si no pasa la validación
      }
      if (!otp || otp.length !== 6) {
        Swal.showValidationMessage('Por favor, ingresa el código de aprobación');
        return false;
      }
      if (!dni || dni.length !== 8) {
        Swal.showValidationMessage('El DNI debe tener exactamente 8 caracteres');
        return false;
      }

      // Si pasa la validación, retornamos los valores
      return { telefono, otp, dni };
    }
  });

      if (formValues) {
      // Procesar el pago con los valores obtenidos
      try {

         this.estado=false;

        const response = await axios.post('/api/pagos/procesar', {
          telefono: formValues.telefono,
          otp: formValues.otp,
          dni: formValues.dni,
          monto: this.monto // Reemplaza por el monto que corresponda
        });

        console.log(response);

        // Verificar si la respuesta contiene un error devuelto por la API
        if (response.data.code === "YPCHK0010") {
          // Muestra el mensaje de error de la API
          Swal.fire(
            'Error',
            response.data.user_message || 'Hubo un error procesando el pago.',
            'error'
          );
        } else {
            if (response.data.object === "token") {

              Swal.fire({
                title: 'Éxito',
                text: "Token de Yape generado: " + response.data.id +"...se procede a realizar el cargo ",
                icon: 'success',
                timer: 5000, // Duración en milisegundos (3 segundos)
                showConfirmButton: false, // Opcional, para no mostrar el botón de confirmación
            });  

                

              try{

                  const response_pago = await axios.post('/api/pagos/generarCargo', {              
                    amount: this.monto,
                    currency_code: "PEN",
                    email: response.data.email,
                    source_id: response.data.id,
                    description: "PAGO MENSUAL "+response.data.client.ip,
                    dni: formValues.dni
                    });
                    
                  console.log(response_pago.data);

                  // Verifica si `outcome` y `outcome.code` existen
                  if (response_pago.data && response_pago.data.outcome && response_pago.data.outcome.code) {
                      if (response_pago.data.outcome.code==="AUT0000"){

                          //grabar la informacion en la tabla pagos
                          //registrarPago
                            const response_registro = await axios.post('/api/pagos/registrarPago', {              
                            monto: this.monto,
                            token:response.data.id,
                            usuario_id:this.auth.user.id,                    
                            });

     
                            

                          

                            Swal.fire(
                                'Éxito',
                                response_pago.data.outcome.merchant_message+"  "+response_pago.data.outcome.user_message ,
                                'success'
                              );                    
                      }
                  }    

               else{
                     Swal.fire(
                      'Error en la transacción',
                      response_pago.data.user_message || 'No se pudo realizar el cargo. Inténtalo nuevamente.',
                      'error'
                  );
                }  
               }catch (error_pago) {
                    // Maneja errores de la segunda petición (carga)
                    console.log(error_pago)
                    Swal.fire(
                      'Error en la transacción',
                      error_pago.response_pago.data.user_message || 'No se pudo realizar el cargo. Inténtalo nuevamente.',
                      'error'
                    );
                  }               

            }  //fin del if response token 
            else{ 
              Swal.fire(
                'Error',
                response.data.user_message || 'No se pudo procesar el token. Inténtalo nuevamente.',
                'error'
              );
            }         
        }
        this.estado=true
      } catch (error) {
        // Maneja errores de conexión o errores inesperados
       console.log(error)        
        Swal.fire(
          'Error',
          error || 'No se pudo procesar el token. Inténtalo nuevamente.',
          'error'
        );
      }
    }
},

    
    async verificarEstadoBotonPago() {
            try {
                const response = await axios.get('/api/pagos/estado-boton');
                console.log(response.data)
                this.botonActivo = response.data.activo;
                this.mensajeBoton= response.data.mensaje;

                // Guardar los valores en el localStorage
                localStorage.setItem('botonActivo', JSON.stringify(response.data.activo)); // Almacena booleano como string
                localStorage.setItem('mensajeBoton', response.data.mensaje); // Mensaje ya es una cadena

            } catch (error) {
                console.error('Error al verificar el estado del botón de pago:', error);
            }
        },

        // Método para procesar el pago
        async procesarPago () {
            try {
                const response = await axios.post('/api/pagos/procesar', {
                    telefono: telefono.value,
                    otp: otp.value,
                    monto: monto.value
                });
                alert('Pago realizado con éxito. Token: ' + response.data.token);
            } catch (error) {
                console.error('Error al procesar el pago:', error);
                alert('No se pudo procesar el pago: ' + error.response.data.error);
            }
        },

        toggleEstado() {
        // Cambia el estado del botón (opcional si necesitas un cambio manual)
        this.botonActivo = !this.botonActivo;
        },

 
   

  },


};
</script>

<style>
  .custom-paragraph {
    background-color: #eb5767;
  }
</style>


