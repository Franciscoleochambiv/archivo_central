<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>


<template>
     <Head title="Dashboard" />

     <AuthenticatedLayout>

      

      <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Listado de Documentos</h2>
      <div class="overflow-x-auto relative shadow-md sm:rounded-lg">


        <div class="relative">
          <label for="asunto">Metas: </label>
          <input
            type="text"
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 mt-3 mb-5 text-sm shadow-sm"
            v-model="meta"
            ref="meta"
            id="meta"
            name="meta"
            required
            placeholder="Metas"
            autocomplete="off"
            autofocus
            @input="buscarMetas"
          />
          <ul v-if="sugerencias.length > 0" class="absolute z-10 mt-1 w-full bg-white rounded-lg shadow-lg">
            <li v-for="sugerencia in sugerencias" :key="sugerencia.id" @click="seleccionarSugerencia(sugerencia)"
              class="cursor-pointer p-2 hover:bg-gray-100">
              {{ sugerencia.codigo}}-{{ sugerencia.descripcion}}
            </li>
          </ul>
        </div>
        



        
      <button  
         class="inline-flex items-center px-4 py-2 bg-red-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"                                                                
         style="transition: all 0.15s ease 0s;"      
         @click="exportToExcel">Exportar a Excel
      </button>

      <button  
         class="inline-flex items-center px-4 py-2 bg-red-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"                                                                
         style="transition: all 0.15s ease 0s;"      
         @click="fetchMovimientos_meta">Acumulado por Meta
      </button>


      <h1>Movimientos de Petroleo</h1>
                        
        <table id="categoria" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"> 
                <tr>
                    <th scope="col" class="py-3 px-6">
                        Id
                    </th>        
                    <th scope="col" class="py-3 px-6">
                        Cod_Meta
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Fecha
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Meta
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Cantidad
                    </th>       
                    <th scope="col" class="py-3 px-6">
                        Tipo Doc
                    </th>                               
                     <th scope="col" class="py-3 px-6" style="width: 70px;">
                        Detalle
                    </th>  
                    <th scope="col" class="py-3 px-6">
                        User Id
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Ingreso
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Egreso
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Saldo
                    </th>    
                    
                    <th scope="col" class="py-3 px-6">
                        Acciones
                    </th>  
                   
                </tr>
            </thead>
            <tbody>


            <tr v-for="(item, index) in movimientos" :key="index"  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="text-xs py-2 px-6" >
                               {{ item.id }}
                            </td>
                            <td class="text-xs py-2 px-6" >
                               {{ item.cod_meta }}
                            </td>
                            <td class="text-xs py-2 px-6" >
                               {{ item.fecha }}
                            </td>
                            <td class="text-xs py-2 px-6">
                               {{ item.meta }}                            
                            </td>
                            <td class="text-xs py-2 px-6">
                              {{ item.cantidad }}
                            </td >                           
                            <td class="text-xs py-2 px-6">
                              {{ item.tipo_doc }}
                            </td >
                            <td class="text-xs py-2 px-6">
                              {{ item.detalle}}
                            </td >
                            <td class="text-xs py-2 px-6">
                              {{ item.userid}}
                            </td >
                            <td v-if="item.tipo_doc == 1">{{ item.cantidad }}</td>
                            <td v-else></td>
                            <td v-if="item.tipo_doc == 2">{{ item.cantidad }}</td>
                            <td v-else></td>                            
                            <td>{{ item.saldo  }}</td>


                            <td class="px-6 py-1">                             
                                <div class="flex justify-end gap-4" v-if="$page.props.auth.user.email === 'hermtorreszea_21@hotmail.com'">    
                                  
                                  <button    >
                                   
                                    <a  :href="`/editm/${item.id}`"  x-data="{ tooltip: 'Edite' }" >                                                                              
                                        <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    class="w-5 h-5 stroke-red-500"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                                        
                                                    />
                                                </svg>
                                            </div>                                        
                                     </a>   
                                    </button> 
                                                                     
                                     <button   @click="borrar(item.id)" >
                                            <a x-data="{ tooltip: 'Edite' }" >                                                                              
                                                <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            class="w-5 h-5 stroke-red-500"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"                                                        
                                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                                
                                                            />
                                                        </svg>
                                                    </div>                                        
                                              </a> 
                                     </button>  
                                  </div>   
                                                    
                            </td>   

                            
                            
                            
            </tr>
           </tbody>
        </table>   
        
        



        <h1>Movimientos de Gasolina</h1>
                        
        <table id="categoria" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"> 
                <tr>
                    <th scope="col" class="py-3 px-6">
                        Id
                    </th>        
                    <th scope="col" class="py-3 px-6">
                        Cod_Meta
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Fecha
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Meta
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Cantidad
                    </th>       
                    <th scope="col" class="py-3 px-6">
                        Tipo Doc
                    </th>                               
                    <th scope="col" class="py-3 px-6" style="width: 70px;">
                        Detalle
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        User Id
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Ingreso
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Egreso
                    </th> 
                    <th scope="col" class="py-3 px-6">
                        Saldo
                    </th>                     
                    <th scope="col" class="py-3 px-6">
                        Acciones
                    </th>  
                </tr>
            </thead>
            <tbody>


            <tr v-for="(item, index) in movimientosp" :key="index"  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="text-xs py-2 px-6" >
                               {{ item.id }}
                            </td>
                            <td class="text-xs py-2 px-6" >
                               {{ item.cod_meta }}
                            </td>
                            <td class="text-xs py-2 px-6" >
                               {{ item.fecha }}
                            </td>
                            <td class="text-xs py-2 px-6">
                               {{ item.meta }}                            
                            </td>
                            <td class="text-xs py-2 px-6">
                              {{ item.cantidad }}
                            </td >                           
                            <td class="text-xs py-2 px-6">
                              {{ item.tipo_doc }}
                            </td >
                            <td class="text-xs py-2 px-6">
                              {{ item.detalle}}
                            </td >
                            <td class="text-xs py-2 px-6">
                              {{ item.userid}}
                            </td >
                            <td v-if="item.tipo_doc == 1">{{ item.cantidad }}</td>
                            <td v-else></td>
                            <td v-if="item.tipo_doc == 2">{{ item.cantidad }}</td>
                            <td v-else></td>                            
                             <td>{{ item.saldo  }}</td>
                            
                             <td class="px-6 py-1">                             
                                <div class="flex justify-end gap-4">    

                                  
                                  <button    >
                                    <a  :href="`/editm/${item.id}`"  x-data="{ tooltip: 'Edite' }" >                                                                                
                                        <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    class="w-5 h-5 stroke-red-500"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                                        
                                                    />
                                                </svg>
                                            </div>                                        
                                     </a>  
                                    </button>  
                                                                                            
                                                                     
                                     <button   @click="borrar(item.id)" >
                                            <a x-data="{ tooltip: 'Edite' }" >                                                                              
                                                <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            class="w-5 h-5 stroke-red-500"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"                                                        
                                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                                
                                                            />
                                                        </svg>
                                                    </div>                                        
                                              </a> 
                                     </button>  
                                  </div>   
                                                    
                            </td>  




                                                     
                            
            </tr>
           </tbody>
        </table>      
        


        
      </div>
     
    </div>

</AuthenticatedLayout>

  </template>
  
  <script>
  import html2pdf from 'vue-html2pdf';
  import * as XLSX from 'xlsx';
  import axios from 'axios';
  import Swal from 'sweetalert2';


  export default {
    data() {
      return {
        movimientos: [],
        movimientosp: [],
        meta:'',
        cod_meta:'',
        id_meta:'',
        sugerencias: [],  
      };
    },
    created() {
      this.fetchMovimientos();
    },
    methods: {


      async modificar(id) {



        await Swal.fire({
        title: 'Error',
        text: 'Opción no habilitada en este procedimiento, consulte con el administrador',
        icon: 'error'
        });





      },


       
        async borrar(id) {
            // Mostrar el SweetAlert de confirmación
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrarlo',
                cancelButtonText: 'Cancelar',
                cancelButtonColor: '#d33' // Color del botón de cancelar (rojo)
            });

            // Si el usuario confirma la acción
            if (result.isConfirmed) {
                // Realizar la solicitud de borrado
                await axios
                    .delete(`/api/movimientos/${id}`)
                    .then(response => {
                        // Manejar la respuesta
                        console.log(response);
                        // Recargar los movimientos después del borrado
                        this.fetchMovimientos();
                        // Mostrar un mensaje de éxito
                        Swal.fire(
                            'Borrado',
                            'El movimiento ha sido eliminado correctamente',
                            'success'
                        );
                    })
                    .catch(error => {
                        // Manejar errores
                        console.error('Error:', error);
                        // Mostrar un mensaje de error
                        Swal.fire(
                            'Error',
                            'Hubo un problema al intentar borrar el movimiento',
                            'error'
                        );
                    });
            }
        },




      buscarMetas() {
      if (this.meta.length > 0) {
        // Realizar una solicitud a la API para buscar sugerencias
        fetch(`api/metas?q=${this.meta}`)
          .then(response => response.json())
          .then(data => {
            this.sugerencias = data;
          })
          .catch(error => {
            console.error('Error al buscar sugerencias:', error);
          });
      } else {
        this.sugerencias = [];
      }
    },
    seleccionarSugerencia(sugerencia) {
      this.meta = sugerencia.descripcion;
      this.cod_meta=sugerencia.codigo;
      this.id_meta=sugerencia.id;

      //LA META HA SIDO SELECCIONADA
      //console.log(this.meta);
      this.fetchMovimientosmeta(this.id_meta)





      this.sugerencias = [];
    },


    async fetchMovimientosmeta(id) {
        try {
          const response = await fetch('/api/movimientos/'+id);
          const data = await response.json();
          this.movimientos = data;

          // Recalcular el campo "saldo" para cada registro
            let saldoAcumulado = 0;
            this.movimientos = data[0].map(item => {
                // Calcular el saldo
                item.cantidad = parseFloat(item.cantidad);
                item.cantidad = item.cantidad.toFixed(2);

                if (item.tipo_doc == 1) {
                saldoAcumulado += parseFloat(item.cantidad);
                } else if (item.tipo_doc ==2) {
                saldoAcumulado -= parseFloat(item.cantidad);
                }
                // Agregar el campo "saldo" al objeto
                return {
                ...item,
                saldo: saldoAcumulado.toFixed(2)
                };
            })


            // Recalcular el campo "saldo" para cada registro
            let saldoAcumuladop = 0;
            this.movimientosp = data[1].map(item => {
                // Calcular el saldo
                item.cantidad = parseFloat(item.cantidad);
                item.cantidad = item.cantidad.toFixed(2);

                if (item.tipo_doc == 1) {
                saldoAcumuladop += parseFloat(item.cantidad);
                } else if (item.tipo_doc ==2) {
                saldoAcumuladop -= parseFloat(item.cantidad);
                }
                // Agregar el campo "saldo" al objeto
                return {
                ...item,
                saldo: saldoAcumuladop.toFixed(2)
                };
            })
            //console.log(this.movimientos)




        } catch (error) {
          console.error('Error fetching movimientos:', error);
        }
      },


      async fetchMovimientos() {
        try {
          const response = await fetch('/api/movimientos');
          const data = await response.json();
          this.movimientos = data[0];
          this.movimientosp = data[1];


          console.log(this.movimientos[0])

          // Recalcular el campo "saldo" para cada registro en caso de la gasolina
            let saldoAcumulado = 0;
            this.movimientos = data[0].map(item => {
                // Calcular el saldo
                item.cantidad = parseFloat(item.cantidad);
                item.cantidad = item.cantidad.toFixed(2);

                if (item.tipo_doc == 1) {
                saldoAcumulado += parseFloat(item.cantidad);
                } else if (item.tipo_doc ==2) {
                saldoAcumulado -= parseFloat(item.cantidad);
                }
                // Agregar el campo "saldo" al objeto
                return {
                ...item,
                saldo: saldoAcumulado.toFixed(2)
                };
            })


          // Recalcular el campo "saldo" para cada registro en caso de la gasolina
          let saldoAcumuladop = 0;
            this.movimientosp = data[1].map(item => {
                // Calcular el saldo
                item.cantidad = parseFloat(item.cantidad);
                item.cantidad = item.cantidad.toFixed(2);

                
                if (item.tipo_doc == 1) {
                saldoAcumuladop += parseFloat(item.cantidad);
                } else if (item.tipo_doc ==2) {
                saldoAcumuladop -= parseFloat(item.cantidad);
                }
                // Agregar el campo "saldo" al objeto
                return {
                ...item,
                saldo: saldoAcumuladop.toFixed(2)
                };
            })
            
            




        } catch (error) {
          console.error('Error fetching movimientos:', error);
        }
      },


     async fetchMovimientos_meta() {
     try {
        const response = await fetch('/api/movimientos');
        const data = await response.json();
        this.movimientos = data[0];
        this.movimientosp = data[1];

        console.log(this.movimientos[0]);

        // Función para calcular totales por cod_meta
        const calcularTotalesPorMeta = (movimientos) => {
            const totalesPorMeta = {};

            movimientos.forEach(item => {
                const codMeta = item.cod_meta;

                if (!totalesPorMeta[codMeta]) {
                    totalesPorMeta[codMeta] = {
                        cod_meta: codMeta,
                        meta: item.meta,
                        ingreso: 0,
                        egreso: 0,
                        saldo: 0
                    };
                }

                item.cantidad = parseFloat(item.cantidad);
                item.cantidad = item.cantidad.toFixed(2);

                if (item.tipo_doc == 1) {
                    totalesPorMeta[codMeta].ingreso += parseFloat(item.cantidad);
                    totalesPorMeta[codMeta].saldo += parseFloat(item.cantidad);
                } else if (item.tipo_doc == 2) {
                    totalesPorMeta[codMeta].egreso += parseFloat(item.cantidad);
                    totalesPorMeta[codMeta].saldo -= parseFloat(item.cantidad);
                }
            });

            // Convertir el objeto a un array para facilitar el uso posterior
            return Object.values(totalesPorMeta).map(meta => ({
                ...meta,
                ingreso: Number(meta.ingreso.toFixed(2)),
                egreso: Number(meta.egreso.toFixed(2)),
                saldo: Number(meta.saldo.toFixed(2))
            }));
        };

        // Calcular los totales para cada tipo de combustible
        this.totalesMovimientos = calcularTotalesPorMeta(this.movimientos);
        this.totalesMovimientosp = calcularTotalesPorMeta(this.movimientosp);

        //console.log(this.totalesMovimientos)
        //console.log(this.totalesMovimientosp)
        // Exportar a Excel
        this.exportToExcel_meta(this.totalesMovimientos, this.totalesMovimientosp);

    } catch (error) {
        console.error('Error fetching movimientos:', error);
    }
},

exportToExcel_meta(totalesMovimientos, totalesMovimientosp) {
    // Crear hojas de Excel para cada tipo de combustible
    const worksheet1 = XLSX.utils.json_to_sheet(totalesMovimientos);
    const worksheet2 = XLSX.utils.json_to_sheet(totalesMovimientosp);

    // Ajustar el ancho de las columnas
    const wscols = [
        { wch: 10 }, // cod_meta
        { wch: 100 }, // meta (más ancho para mejor lectura)
        { wch: 15 }, // ingreso
        { wch: 15 }, // egreso
        { wch: 15 }  // saldo
    ];

    worksheet1['!cols'] = wscols;
    worksheet2['!cols'] = wscols;

    // Crear libro de Excel
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet1, 'Totales Movimientos Petroleo');
    XLSX.utils.book_append_sheet(workbook, worksheet2, 'Totales Movimientos Gasolina');

    // Escribir archivo de Excel
    XLSX.writeFile(workbook, 'TotalesMovimientos.xlsx');
},



      exportToPDF() {
        this.$htmlToPdf(this.$el, {
          margin: [0.5, 0.5],
          filename: 'reporte.pdf',
          html2canvas: {}
        });
      },


      exportToExcel() {
    // Convertir los datos de gasolina a un formato compatible con xlsx
    const jsonDataGasolina = this.movimientos.map(item => { 
      // Filtrar solo los movimientos de gasolina
            return {            
                'ID': Number(item.id),
                'UserID': item.userid,
                'Fecha': item.fecha,
                'Cod_Meta': item.cod_meta,
                'Meta': item.meta,
                'Cantidad': Number(item.cantidad),
                'Tipo Doc': item.tipo_doc,
                'Nro Doc': item.nro_doc,                        
                'Ingreso': item.tipo_doc ==1 ? Number(item.cantidad) : '',
                'Egreso': item.tipo_doc ==2 ? Number(item.cantidad) : '',
                'Saldo': Number(item.saldo),
                'Detalle': item.detalle
            };
        
    }).filter(item => item); // Eliminar elementos nulos

    // Convertir los datos de petróleo a un formato compatible con xlsx
    const jsonDataPetróleo = this.movimientosp.map(item => {
       // Filtrar solo los movimientos de petróleo
            return { 
                'ID': Number(item.id),           
                'UserID': item.userid,
                'Fecha': item.fecha,
                'Cod_Meta': item.cod_meta,
                'Meta': item.meta,
                'Cantidad': Number(item.cantidad),
                'Tipo Doc': item.tipo_doc,
                'Nro Doc': item.nro_doc,                        
                'Ingreso': item.tipo_doc == 1 ? Number(item.cantidad) : '',
                'Egreso': item.tipo_doc ==  2 ? Number(item.cantidad) : '',
                'Saldo': Number(item.saldo),
                'Detalle': item.detalle
            };
        
    }).filter(item => item); // Eliminar elementos nulos

    // Crear hojas de trabajo para gasolina y petróleo
    const worksheetGasolina = XLSX.utils.json_to_sheet(jsonDataGasolina);
    const worksheetPetróleo = XLSX.utils.json_to_sheet(jsonDataPetróleo);

    // Configurar el ancho de las columnas
    const columnWidths = [
        { wch: 8 }, // ID            
        { wch: 8 }, // UserID            
        { wch: 15 }, // Fecha
        { wch: 8 }, // Cod_meta            
        { wch: 20 }, // Meta
        { wch: 10 }, // Cantidad
        { wch: 8 }, // Tipo Doc
        { wch: 15 }, // Nro Doc            
        { wch: 12 }, // Ingreso
        { wch: 12 }, // Egreso
        { wch: 12 }, // Saldo
        { wch: 25 } // Detalle            
    ];
    worksheetGasolina['!cols'] = columnWidths;
    worksheetPetróleo['!cols'] = columnWidths;

    // Establecer el estilo para los títulos de las columnas
    const titleStyle = {
        alignment: {
            horizontal: 'center', // Centrar horizontalmente
            vertical: 'center', // Centrar verticalmente
            wrapText: true // Envolver texto si es necesario
        },
        font: {
            bold: true // Hacer el texto en negrita
        }
    };

    // Agregar los títulos con estilo a la hoja de cálculo de gasolina
    const headersGasolina = ['ID','UserID', 'Fecha', 'Cod_Meta','Meta', 'Cantidad', 'Tipo Doc', 'Nro Doc', 'Ingreso', 'Egreso', 'Saldo', 'Detalle'];
    headersGasolina.forEach((title, index) => {
        const cellRef = XLSX.utils.encode_cell({ r: 0, c: index });
        worksheetGasolina[cellRef] = { v: title, s: titleStyle };
    });

    // Agregar los títulos con estilo a la hoja de cálculo de petróleo
    const headersPetróleo = ['ID','UserID', 'Fecha','Cod_Meta', 'Meta', 'Cantidad', 'Tipo Doc', 'Nro Doc', 'Ingreso', 'Egreso', 'Saldo', 'Detalle'];
    headersPetróleo.forEach((title, index) => {
        const cellRef = XLSX.utils.encode_cell({ r: 0, c: index });
        worksheetPetróleo[cellRef] = { v: title, s: titleStyle };
    });

    // Crear un nuevo libro y agregar las hojas de trabajo
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheetGasolina, 'Movimientos Petroleo');
    XLSX.utils.book_append_sheet(workbook, worksheetPetróleo, 'Movimientos Gasolina');

    // Guardar el libro como un archivo Excel
    XLSX.writeFile(workbook, 'movimientos.xlsx');
}



     
    },
    directives: {
      htmlToPdf: html2pdf
    },
  };
  </script>
  