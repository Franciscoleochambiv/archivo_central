<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>



        <div class="mx-auto max-w-screen-xl w-full px-4 py-2 sm:px-6 lg:px-8">
        
  <div class="mx-auto max-w-full">

  
    

    <h1 class="text-center text-2xl font-bold text-black-600 sm:text-3xl">Modificacion - Control de Combustible</h1>

    <form @submit.prevent="submit" enctype="multipart/form-data" class="mb-0 mt-3 space-y-4 rounded-lg p-4 shadow-2xl sm:p-6 lg:p-8">
      
      <p class="text-center custom-paragraph  text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
          Usuario    - {{ $page.props.auth.user.name }}
      </p>              

              <div class="flex">

                <div class="relative flex-row w-1/3 ">
                  Gasolina - Ingresos
                  <input
                    id="egresos"
                    type="text"
                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                    v-model="saldos.ingresos_gasolina"                      
                    readonly     
                  />          
                </div>


                <div class="relative flex-row w-1/3 pl-2">
                  Gasolina - Egresos
                  <input
                    type="text"
                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                    v-model="saldos.egresos_gasolina"                      
                    id="egresos" 
                    
                    readonly       
                  />          
                </div>

                <div class="relative flex-row w-1/3 pl-2">
                  Gasolina  - Saldos
                  <input
                    type="text"
                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                    v-model="saldos.saldos_gasolina"                      
                    id="saldos" 
                    
                    readonly       
                  />          
                </div>
              </div> 

              <div class="flex">

                  <div class="relative flex-row w-1/3 ">
                    Petroleo - Ingresos
                    <input
                      id="egresos"
                      type="text"
                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                      v-model="saldos.ingresos_petroleo"                      
                      readonly     
                    />          
                  </div>


                  <div class="relative flex-row w-1/3 pl-2">
                    Petroleo - Egresos
                    <input
                      type="text"
                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                      v-model="saldos.egresos_petroleo"                      
                      id="egresos" 
                      
                      readonly       
                    />          
                  </div>

                  <div class="relative flex-row w-1/3 pl-2">
                    Petroleo - Saldos
                    <input
                      type="text"
                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"                                
                      v-model="saldos.saldos_petroleo"                      
                      id="saldos" 
                      
                      readonly       
                    />          
                  </div>
              </div> 





     


      <p class="text-center custom-paragraph  text-lg font-medium bg-indigo-600 text-white rounded-full py-2 px-4">
          Movimientos - Modificacion
      </p>

      <div>

        <div class="relative">
          <label for="asunto">Metas:</label>
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
            v-model="form.meta"
            ref="meta"
            id="meta"
            name="meta"
            required
            placeholder="Metas"
            autocomplete="off"
            autofocus
            @input="buscarMetasm"
          />
          <ul v-if="sugerencias.length > 0" class="absolute z-10 mt-1 w-full bg-white rounded-lg shadow-lg">
            <li v-for="sugerencia in sugerencias" :key="sugerencia.id" @click="seleccionarSugerencia(sugerencia)"
              class="cursor-pointer p-2 hover:bg-gray-100">
              {{ sugerencia.codigo}}-{{ sugerencia.descripcion}}
            </li>
          </ul>
        </div>
        

       
      </div>

      <div class="flex">

        <div class="relative flex-row w-1/2 ">
          <input
            type="date"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"            
            placeholder="Nro de Vale"
            v-model="form.fecha"  
            ref="input" 
            id="fecha" 
            name="fecha" 
            required       
          />          
        </div>
     

       <div class="relative flex-row w-1/2 pl-2">
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"            
            placeholder="Cantidad"
            v-model="form.cantidad"  
            ref="input" 
            id="cantidad" 
            name="cantidad" 
            required       
          />          
        </div>
    </div> 

  <div class="flex">


    <div class="relative flex-row w-1/3">  
    <select
        name="HeadlineAct"
        id="HeadlineAct"
        class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
        v-model="form.tipo_combustible"  
        ref="input" 
        placeholder="Escoja el tipo de Movimientos"
        required          
    >
        <option value="0">GASOLINA</option>
        <option value="1">PETROLEO</option>
             
    </select>
    </div>

      <div class="relative flex-row w-1/3">  
        <select
          name="HeadlineAct"
          id="HeadlineAct"
          class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
          v-model="form.tipo_doc"  
          ref="input" 
          placeholder="Escoja el tipo de Movimientos"
          required          
        >
          
          <option value="1">INGRESO</option>
          <option value="2">SALIDA</option>          
        </select>
    </div>

       <div class="relative flex-row w-1/3 pl-2">
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"            
            placeholder="Nro de Vale"
            v-model="form.nro_doc"  
            ref="input" 
            id="nro_doc" 
            name="nro_doc" 
            required       
          />          
        </div>
    </div>   

        <div class="relative">
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"            
            placeholder="Observaciones "
            v-model="form.detalle"  
            ref="input" 
            id="detalle" 
            name="detalle" 
            required       
          />          
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
        Grabar Modificacion
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
      saldos: {
        ingresos: 0,
        egresos: 0
      },     
      form:{
            id:'',
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
  mounted() {
    // Establece el valor del input al valor del nombre del usuario
    this.obtenerSaldos();
    if (this.auth && this.auth.user && this.auth.user.name) {
          this.emailValue = this.auth.user.email;
    }
    if (this.$refs.asunto) {
      this.$refs.asunto.focus();
    }

    // Obtener el ID de la URL
      
      
      const id = this.$page.props.id;


    if (id) {
        axios.get(`/api/movi/${id}`)
            .then(response => {
                // Asigna los detalles del movimiento al formulario para la edición
                //this.form = response.data;

                this.form.cantidad=response.data[0].cantidad
                this.form.fecha=response.data[0].fecha
                this.form.tipo_doc=response.data[0].tipo_doc
                this.form.tipo_combustible=response.data[0].tipo_combustible
                this.form.nro_doc=response.data[0].nro_doc
                this.form.detalle=response.data[0].detalle
                this.form.id_meta=response.data[0].id_meta
                this.form.cod_meta=response.data[0].cod_meta
                this.form.meta=response.data[0].meta
                this.form.id=response.data[0].id

                console.log(response.data)
            })
            .catch(error => {
                console.error('Error al obtener los detalles del movimiento:', error);
            });
    }



  },
  methods: {

    obtenerSaldos() {
      axios.get('/api/saldos')
        .then(response => {
          this.saldos = response.data;
        })
        .catch(error => {
          console.error('Error al obtener los saldos:', error);
        });
    },


    buscarMetasm() {
     // console.log(this.form.meta)
    
      if (this.form.meta.length > 0) {
        // Realizar una solicitud a la API para buscar sugerencias

        //console.log(this.form.meta)


        fetch(`/api/metas?q=${this.form.meta}`)
          .then(response => response.json())
          .then(data => {
            this.sugerencias = data;

           // console.log(this.sugerencias)
          })
          .catch(error => {
            console.error('Error al buscar sugerencias:', error);
          });
      } else {
        this.sugerencias = [];
      }
     
    },


    seleccionarSugerencia(sugerencia) {
      this.form.meta = sugerencia.descripcion;
      this.form.cod_meta=sugerencia.codigo;
      this.form.id_meta=sugerencia.id;
      this.sugerencias = [];
    },


    //Grbacion del formulario
    submit(event) {
          event.preventDefault();    
          // Crear un nuevo objeto FormData
          this.importacion= false
          this.form.userid=JSON.stringify(this.auth.user.id);


          //console.log(this.form);

          

          axios.put('/api/movimientos/'+this.form.id, this.form)
          .then(response => {
            // Maneja la respuesta de la API según sea necesario
            //console.log(response.data);
            //limpiar el formulario
            // Después de una grabación exitosa, resetea los valores del formulario
            this.importacion= true
            this.form.id_meta='';
            this.form.fecha='';
            this.form.meta = '';
            this.form.cantidad = '';
            //this.form.tipo_doc = 1;
            //this.form.tipo_combustible = 0;
            this.form.nro_doc = '';
            this.form.detalle = '';
            this.form.userid = '';
            
            this.$nextTick(() => {
            this.$refs.meta.focus();
            });

            this.obtenerSaldos();
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

              this.$inertia.visit('/listado', { method: 'get' }); // Esto redirigirá a la ruta principal


              

          })
          .catch(error => {
            // Maneja el error en caso de que ocurra
            this.importacion= true
            this.form.meta = '';
            this.form.tipo_doc = '';
            this.form.nro_doc = '';
            this.form.detalle = '';
            this.form.userid = '';
            //console.error(error);
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