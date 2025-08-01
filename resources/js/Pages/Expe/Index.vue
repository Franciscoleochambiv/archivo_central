<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Cargos" />
    <AuthenticatedLayout>
      <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Listado de Registros</h2>
        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
          
          <button>
            <a :href="`/addexpe/`" x-data="{ tooltip: 'Añadir' }">
              <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-5 h-5 stroke-red-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
            </a>
          </button>
  
          <button @click="exportToExcel">
            <a>
              <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-5 h-5 stroke-red-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 14.5a.75.75 0 01-.75-.75V4.75c0-.414.336-.75.75-.75h14.5c.414 0 .75.336.75.75v9.622c0 .407-.325.74-.73.75H4.75zM6 6v9M10 6v9M14 6v9M18 6v5" />
                </svg>
              </div>
            </a>
          </button>


          <div class="relative">
        <label for="meta">Registros Documentales: </label>
        <input
          type="text"
          class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 mt-3 mb-5 text-sm shadow-sm"
          v-model="searchTerm"
          id="cargos"
          name="cargos"
          required
          placeholder="Buscar Registros Generales"
          autocomplete="off"
          autofocus
          @input="buscarMetas"
        />
      </div>

  

          <h1>Registros</h1>
  
          <table id="categoria" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>
                <th scope="col" class="py-3 px-6">Id</th>                
                <th scope="col" class="py-3 px-6">Oficina</th>                                 
                <th scope="col" class="py-3 px-6">Nro Archivo</th>                
                <th scope="col" class="py-3 px-6">Unidad</th>                
                <th scope="col" class="py-3 px-6">Nro Comprobantes</th>                
                <th scope="col" class="py-3 px-6">Estado</th>                
                <th scope="col" class="py-3 px-6">Ubicacion</th>                
                <th scope="col" class="py-3 px-6">Accion</th>                

              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in movimientos.data" :key="index" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="text-xs py-2 px-6">{{ item.id }}</td>
                <td class="text-xs py-2 px-6">{{ item.oficina_id }}</td>
                <td class="text-xs py-2 px-6">{{ item.nro_archivo }}</td>
                <td class="text-xs py-2 px-6">{{ item.unidad_conservacion }}</td>
                <td class="text-xs py-2 px-6">{{ item.nro_comprobantes }}</td>
                <td class="text-xs py-2 px-6">{{ item.estado_archivador }}</td>
                <td class="text-xs py-2 px-6">{{ item.ubicacion_actual }}</td>

                <td class="px-6 py-1">
                  <div class="flex justify-end gap-4">
                    <button>
                      <a :href="`/editexpe/${item.id}`" x-data="{ tooltip: 'Editar' }">
                        <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-5 h-5 stroke-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                          </svg>
                        </div>
                      </a>
                    </button>
                    <button @click="borrar(item.id)">
                      <a x-data="{ tooltip: 'Eliminar' }">
                        <div class="h-10 w-10 bg-red-100 dark:bg-red-800/50 flex items-center justify-center rounded-full">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-5 h-5 stroke-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                          </svg>
                        </div>
                      </a>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
  
          <!-- Paginación -->
          <div class="pagination-controls mt-4 flex justify-center space-x-2">
            <button @click="fetchMetas(movimientos.prev_page_url)" :disabled="!movimientos.prev_page_url" class="px-4 py-2 bg-red-100 dark:bg-red-800/50 text-gray-800 rounded hover:bg-gray-400">Anterior</button>
  
            <!-- Páginas -->
           
  
            <span v-if="showEllipsisBefore" class="px-4 py-2">...</span>
  
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="fetchMetas(`/api/expedientes?page=${page}`)"
              :class="{'px-4 py-2 bg-red-400 dark:bg-red-800/50 rounded hover:bg-red-600/10 text-white': currentPage === page, 'bg-red-300 text-white-800': currentPage !== page}" 
              class="px-4 py-2 bg-red-300 dark:bg-red-800/50 rounded hover:bg-red-600/50"
            >
              {{ page }}
            </button>
  
            <span v-if="showEllipsisAfter" class="px-4 py-2">...</span>
  
           
  
            <button @click="fetchMetas(movimientos.next_page_url)" :disabled="!movimientos.next_page_url" class="px-4 py-2 bg-red-100 dark:bg-red-800/50 rounded hover:bg-gray-400">Siguiente</button>
          </div>
  
        </div>
      </div>
    </AuthenticatedLayout>
 
  
</template>

<script>
//import axios from 'axios';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';

export default {
  data() {
    return {
      movimientos: { data: [], prev_page_url: null, next_page_url: null, current_page: 1, last_page: 1 },
      currentPage: 1,
      searchTerm: '',
      isFetching: false,
    };
  },
  computed: {
    totalPages() {
      return this.movimientos.last_page;
    },
    visiblePages() {
      const pages = [];
      const startPage = Math.max(1, this.currentPage - 2);
      const endPage = Math.min(this.totalPages, this.currentPage + 2);

      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      return pages;
    },
    showEllipsisBefore() {
      return this.visiblePages[0] > 2;
    },
    showEllipsisAfter() {
      return this.visiblePages[this.visiblePages.length - 1] < this.totalPages - 1;
    }
  },
  methods: {
    async fetchMetas(url = '/api/expedientes?page=1') {   

      try {
        const response = await axios.get(url, { params: { q: this.searchTerm } });
        this.movimientos = response.data;
        this.currentPage = this.movimientos.current_page;
      } catch (error) {
        console.error('Error fetching metas:', error);
      }
     
    },

    buscarMetas() {
      // Cancela el timeout anterior si existe
      if (this.debounceTimeout) {
        clearTimeout(this.debounceTimeout);
      }
      
      // Establece un nuevo timeout para la búsqueda
      this.debounceTimeout = setTimeout(() => {
        this.fetchMetas(); // Llama a fetchMetas con el término de búsqueda actual
      }, 200); // 500 milisegundos de espera
    },


    async borrar(id) {
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, borrarlo',
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#d33'
      });

      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/expediente/${id}`);
          this.fetchMetas();
          Swal.fire('Borrado', 'El movimiento ha sido eliminado correctamente', 'success');
        } catch (error) {
          console.error('Error:', error);
          Swal.fire('Error', 'Hubo un problema al intentar borrar el movimiento', 'error');
        }
      }
    },

    async exportToExcel() {


         const response = await fetch('/api/obraexcel');

         //console.log(response);
          const data = await response.json();
  


      const jsonData = data.map(item => ({
        'ID': item.id,        
        'Descripcion': item.nombre,
        'Latitud': item.latitud,
        'Longitud': item.longitud,
        'Radio': item.radio,
        'Direccion': item.direccion,
        

      }));

      const worksheet = XLSX.utils.json_to_sheet(jsonData);
      const columnWidths = [
        { wch: 8 },        
        { wch: 40 },
        { wch: 12 },
        { wch: 12 },
        { wch: 8 },
        { wch: 30 },
        

      ];
      worksheet['!cols'] = columnWidths;

      const titleStyle = {
        alignment: {
          horizontal: 'center',
          vertical: 'center',
          wrapText: true
        },
        font: { bold: true }
      };

      const headers = ['ID', 'Descripcion','Latitud','Longitud','Radio','Direccion'];
      headers.forEach((title, index) => {
        const cellRef = XLSX.utils.encode_cell({ r: 0, c: index });
        worksheet[cellRef] = { v: title, s: titleStyle };
      });

      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, 'expedientes');
      XLSX.writeFile(workbook, 'expedientes.xlsx');
    }
  },
  mounted() {
    this.fetchMetas();
  }
};
</script>
  
  <style scoped>
  .pagination-controls button {
    cursor: pointer;
  }
  
  .pagination-controls button[disabled] {
    cursor: not-allowed;
    opacity: 0.6;
  }
  
  .pagination-controls span {
    padding: 0 1rem;
  }
  </style>
  