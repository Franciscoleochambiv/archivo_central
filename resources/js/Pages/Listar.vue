<template>

   <div class="relative min-h-screen bg-gray-100 flex flex-col justify-center items-center px-4">
    <!-- Encabezado -->
    <img src="/asset/lolo.jpeg" alt="Logo de la Municipalidad" class="max-w-xs mx-auto mb-4" />
    <h2 class="text-xl font-semibold text-gray-800 mb-2 text-center">Solicitar Acceso</h2>  

    <!-- Caja principal -->
    <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full text-center mt-6">
      <!-- Reloj -->     

   <div v-if="fase === 'solicitar'">
      <!-- Token -->
   
        <h3 class="text-xl font-semibold text-red-800 mb-4">🔒 Ingresar Los datos para Obtener Acceso</h3>
        <input
          v-model="dni"
          placeholder="DNI"
          class="border px-4 py-2 rounded w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

        <input
          v-model="email"
          placeholder="Correo Electrónico"
          class="border px-4 py-2 rounded w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

        <button
         @click="solicitarToken"
          class="bg-red-400 text-white px-6 py-2 rounded hover:bg-red-700 transition w-full"
        >
         Solicitar token
        </button>


          <!-- Spinner -->
        <div v-if="buscando" class="flex justify-center mt-4">
          <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
          </svg>
        </div>

     </div>

    <div v-if="fase === 'validar'">
         <h3 class="text-xl font-semibold text-red-800 mb-4">🔒 Validar Token</h3>
        <input
          v-model="token"
          placeholder="Token recibido"
          class="border px-4 py-2 rounded w-full mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

       <button
          @click="validarToken"         
          class="bg-red-400 text-white px-6 py-2 rounded hover:bg-red-700 transition w-full"
        >
         Validar
        </button>       
    </div>

    <div v-if="fase === 'consulta'">
        <label class="block text-sm font-semibold text-gray-600 mb-2">Fecha Inicio</label>
        <input
          type="date"
          v-model="fechaInicio"
          class="text-sm w-full max-w-lg px-4 py-2 border border-red-300 rounded-lg focus:outline-none focus:border-red-500 hover:shadow mx-1"
        />
        <label class="block text-sm font-semibold text-gray-600 mb-2">Fecha Fin</label>
        <input
          type="date"
          v-model="fechaFin"
          class="text-sm w-full max-w-lg px-4 py-2 border border-red-300 rounded-lg focus:outline-none focus:border-red-500 hover:shadow mx-1"
        />
   
      <!-- Botón para Cargar Datos -->
         <h3 class="text-xl font-semibold text-red-800 mb-4"> </h3>
        <div class="flex space-x-4 mb-6">
      <button
        @click="consultar"
        class="w-full bg-red-400 hover:bg-red-600 text-white py-3 rounded-md font-semibold shadow-md mb-6 mb-4"
      >
        Cargar Reporte
      </button>  
      </div>
      
        <div class="flex space-x-4 mb-6">
        <button
          @click="exportarAsistenciaDiariaExcel"
          class="w-full bg-gray-400 hover:bg-gray-700 rounded-md flex items-center space-x-2 py-2 text-xs text-white"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M3 21h18M3 12h18" />
          </svg>
          <span>Exportar Excel</span>
        </button>
       
      </div>


    </div>

    
           
    </div>

    <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 max-w-6xl w-full relative">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Reporte de Horarios</h3>

    <!-- Cerrar -->
    <button @click="mostrarModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-red-600">
      ✕
    </button>

   <div class="overflow-x-auto">

    <table id="categoria" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" v-for="header in headers" :key="header" class="py-3 px-6">{{ header }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in resultados" :key="index" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <td v-for="value in Object.values(item)" :key="value" class="text-xs py-2 px-6">{{ value }}</td>
            
          </tr>
        </tbody>
      </table>
     </div> 
        <!-- Paginador -->
      <div class="pagination-controls mt-4 flex justify-center space-x-2">
        <button @click="fetchReporte(currentPage - 1)" :disabled="currentPage === 1" class="px-4 py-2 bg-red-100 dark:bg-red-800/50 text-gray-800 rounded hover:bg-gray-400">
          Anterior
        </button>

        <span v-if="showEllipsisBefore" class="px-4 py-2">...</span>

        <button
          v-for="page in visiblePages"
          :key="page"
          @click="fetchReporte(page)"
          :class="['px-4 py-2 rounded', currentPage === page ? 'bg-red-400 text-white' : 'bg-gray-300']"
        >
          {{ page }}
        </button>

        <span v-if="showEllipsisAfter" class="px-4 py-2">...</span>

        <button @click="fetchReporte(currentPage + 1)" :disabled="currentPage === totalPages" class="px-4 py-2 bg-red-100 dark:bg-red-800/50 text-gray-800 rounded hover:bg-gray-400">
          Siguiente
        </button>
      </div>

       </div>
</div>



    <!-- Footer -->
    <div class="mt-10 text-sm text-gray-500 text-center">
      Sfsystem 
    </div>
  </div>

</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import ExcelJS from 'exceljs';

export default {
  data() {
    return {
      fase: 'solicitar',
     // fase: 'consulta',
      buscando:false,
      dni: '',
      email: '',
      token: '',
      dniVerificado: '',
      //dniVerificado: '30961113',
      filteredResults:[],
      fechaInicio: '',
      fechaFin: '',
      resultados: [],
      currentPage: 1,
      totalPages: 1,
      mostrarModal: false,

    };
  },
  computed:{
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
      return this.visiblePages[0] > 1;
    },
    showEllipsisAfter() {
      return this.visiblePages[this.visiblePages.length - 1] < this.totalPages;
    }

  },
  methods: {
    fetchReporte(page) {
    this.consultar(page);
    },

    async solicitarToken() {
      try {
        this.buscando=true
        await axios.post('/api/stoken', {
          dni: this.dni,
          email: this.email,
        });

        //alert('Token enviado al correo');

          Swal.fire({
            title: '¡Correcto!',
            text: 'Token Enviado al correo exitosamente',
            icon: 'success',
            confirmButtonColor: '#16a34a', // verde
            });




        this.buscando=false
        this.fase = 'validar';
      } catch (e) {
        //alert('Error solicitando token');
        Swal.fire({
            title: '¡Error!',
            text: 'Error Solicitando token '+e,
            icon: 'error',
            confirmButtonColor: '#f43f5e',
          });
        
        
      }
    },
    async validarToken() {
      try {
        const res = await axios.post('/api/vtoken', { token: this.token });
        this.dniVerificado = res.data.dni;
        this.fase = 'consulta';
      } catch (e) {

           Swal.fire({
            title: '¡Error!',
            text: 'Token Invalido '+e,
            icon: 'error',
            confirmButtonColor: '#f43f5e',
          });
        
      }
    },
    async consultar(page = 1) {
      try {
        const response = await axios.get(`/api/reporte-personal?page=${page}`, {
          params: {
            dni: this.dniVerificado,
            inicio: this.fechaInicio,
            fin: this.fechaFin,
          },
        });




        this.resultados = response.data.data;
        this.currentPage = response.data.current_page;
        this.totalPages = response.data.last_page;

        this.headers = Object.keys(response.data.data[0] || {});

         this.mostrarModal = true;


        console.log(response.data)
      } catch (e) {

        Swal.fire({
            title: '¡Error!',
            text: 'Error al cargar reporte '+e,
            icon: 'error',
            confirmButtonColor: '#f43f5e',
          });
        
      }
    },

   async exportarAsistenciaDiariaExcel() {
      try {
        // Llamada a la API para obtener los datos de asistencia diaria
        
         const response = await axios.get(`/api/reporte-personal-excel`, {
          params: {
            dni: this.dniVerificado,
            inicio: this.fechaInicio,
            fin: this.fechaFin,
          },
        });


       // const response = await axios.get(url, { params });

        


        const asistencias = response.data;
        console.log(asistencias)


        if (asistencias.length === 0) {
          throw new Error('No se encontraron registros de asistencia diaria.');
        }

        // Crear un nuevo libro de Excel
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Asistencia Diaria');

        // Obtener la fecha y hora actuales del sistema
        const fechaActual = new Date();
        const formattedDateTime = `${fechaActual.getDate().toString().padStart(2, '0')}/${
          (fechaActual.getMonth() + 1).toString().padStart(2, '0')}/${fechaActual.getFullYear()} ${
          fechaActual.getHours().toString().padStart(2, '0')}:${
          fechaActual.getMinutes().toString().padStart(2, '0')}:${
          fechaActual.getSeconds().toString().padStart(2, '0')}`;

        // Encabezado de la planilla
        worksheet.addRow(['Municipalidad Distrital de Colquemarca']);
        worksheet.addRow(['Fecha: ', formattedDateTime]);
        worksheet.addRow([]);
        worksheet.addRow(['Tipo: ', this.tipoContrato]);
        worksheet.mergeCells('A3:M3');
        const headerCell = worksheet.getCell('A3');
        headerCell.value = 'REPORTE DE ASISTENCIA DIARIA DESDE: '+this.fechaInicio+" "+"HASTA:"+this.fechaFin;
        headerCell.alignment = { horizontal: 'center' };
        headerCell.font = { bold: true, size: 14 };

        // Encabezado de columnas
        const headerRow = worksheet.addRow([
          'FECHA',
          'DEVICE_USER_ID',
          'NOMBRE_COMPLETO',
          'MORNING_IN',
          'LUNCH_OUT',
          'AFTERNOON_IN',
          'AFTERNOON_OUT',
          'LATITUD',
          'LONGITUD',
          'Morning_in_out_of_range',
          'Lunch_out_out_of_range',
          'afternoon_in_out_of_range',
          'afternoon_out_out_of_range',

        ]);

        // Aplicar estilo al encabezado
        headerRow.eachCell((cell) => {
          cell.fill = {
            type: 'pattern',
            pattern: 'solid',
            fgColor: { argb: 'F2DCDB' }, // Color rosado claro
          };
          cell.font = { bold: true };
          cell.alignment = { 
            horizontal: 'center',
            vertical: 'middle',
            wrapText: true, // Habilitar ajuste de texto en múltiples líneas
           };
          cell.border = {
            top: { style: 'thin' },
            left: { style: 'thin' },
            bottom: { style: 'thin' },
            right: { style: 'thin' },
          };
        });

        // Ajustar el ancho de las columnas
        worksheet.columns = [
          { width: 12 },
          { width: 15 },
          { width: 30 },
          { width: 15 },
          { width: 15 },
          { width: 15 },
          { width: 15 },
          { width: 12 },
          { width: 12 },
          { width: 8 },
          { width: 8 },
          { width: 8 },
          { width: 8 },
        ];

        // Añadir los datos
        asistencias.forEach((asistencia) => {
          const row = worksheet.addRow([
            asistencia.fecha,
            asistencia.device_user_id,
            asistencia.nombre_completo,
            asistencia.morning_in || 'N/A',
            asistencia.lunch_out || 'N/A',
            asistencia.afternoon_in || 'N/A',
            asistencia.afternoon_out || 'N/A',
            asistencia.latitud || 'N/A',
            asistencia.longitud || 'N/A',
            asistencia.morning_in_out_of_range || 'N/A',
            asistencia.lunch_out_out_of_range || 'N/A',
            asistencia.afternoon_in_out_of_range || 'N/A',
            asistencia.afternoon_out_out_of_range || 'N/A',

          ]);

          // Aplicar formato a las celdas
          row.eachCell((cell) => {
            cell.border = {
              top: { style: 'thin' },
              left: { style: 'thin' },
              bottom: { style: 'thin' },
              right: { style: 'thin' },
            };
            cell.alignment = { wrapText: true };
          });
        });

        // Guardar el archivo como Excel
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `Asistencia_Diaria.xlsx`;
        link.click();
      } catch (error) {

        Swal.fire({
            title: '¡Error!',
            text: 'Error al exportar el reporte de asistencia diaria../ No hay registros '+error,
            icon: 'error',
            confirmButtonColor: '#f43f5e',
          });
        

        //console.error('Error al exportar el reporte de asistencia diaria:', error);
        //alert('Error al exportar el reporte de asistencia diaria.');
      }
    },



  },
};
</script>
