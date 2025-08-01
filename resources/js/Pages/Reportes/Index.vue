<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Reportes Documentales" />
  <AuthenticatedLayout>
    <div class="flex h-screen bg-gray-50">
      <!-- Panel Izquierdo: Selección de Reportes y Filtros -->
      <div class="w-1/4 p-6 border-r border-gray-300 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Seleccionar Reporte</h1>

        <div class="mb-6">
          <label class="block text-sm font-semibold text-gray-600 mb-2">Tipo de Reporte</label>
          <select v-model="reporteSeleccionado" class="text-sm w-full px-4 py-2 border border-red-300 rounded-lg focus:outline-none focus:border-red-500 hover:shadow">
            <option v-for="rep in tiposReporte" :key="rep.value" :value="rep.value">{{ rep.label }}</option>
          </select>
        </div>

        <!-- Filtros comunes -->
        <div class="grid grid-cols-1 gap-4 mb-6">
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">Oficina</label>
            <select v-model="filtros.oficina_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-red-500">
              <option value="">Todas las oficinas</option>
              <option v-for="o in oficinas" :key="o.id" :value="o.id">{{ o.nombre }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">Entidad</label>
            <select v-model="filtros.entidad_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-red-500">
              <option value="">Todas las entidades</option>
              <option v-for="e in entidades" :key="e.id" :value="e.id">{{ e.nombre }}</option>
            </select>
          </div>
        </div>

        <button @click="fetchReporte" class="w-full bg-red-400 hover:bg-red-600 text-white py-3 rounded-md font-semibold shadow-md mb-2">
          Cargar Reporte
        </button>
        <button @click="exportarReporte" class="w-full bg-gray-400 hover:bg-gray-700 rounded-md flex items-center justify-center py-2 text-xs text-white">
          <span>Exportar Excel</span>
        </button>
      </div>

      <!-- Panel Derecho: Tabla de resultados -->
      <div class="flex-1 p-6 overflow-auto">
        <table class="w-full text-sm text-left text-gray-500 bg-white rounded-lg shadow-sm">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
              <th v-for="h in headers" :key="h" class="py-2 px-4">{{ h }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in dataReportes" :key="idx" class="border-b hover:bg-gray-50">
              <td v-for="value in Object.values(item)" :key="value" class="py-2 px-4">{{ value }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import axios from 'axios';
import ExcelJS from 'exceljs';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      tiposReporte: [
        { label: 'General de Expedientes', value: 'oficina' },
        { label: 'Por Oficina', value: 'oficina' },
      
      ],
      filtros: {
        oficina_id: '',
        entidad_id: '1'
      },
      oficinas: [],
      entidades: [],
      dataReportes: [],
      headers: []
    };
  },
  methods: {
    async cargarMaestros() {
      const respOf = await axios.get('/api/oficinaexcel');
      this.oficinas = respOf.data;
      const respEnt = await axios.get('/api/entidades');
      this.entidades = respEnt.data;
    },
    async fetchReporte() {
      try {
        const resp = await axios.get(`/api/reportes/${this.reporteSeleccionado}`, { params: this.filtros });

        console.log(resp.data);

        this.dataReportes = resp.data;
        this.headers = Object.keys(this.dataReportes[0] || {});
      } catch (e) {
        Swal.fire('Error', 'No se pudo cargar el reporte', 'error');
      }
    },

    async exportarReporte() {

                        try {  
                        const resp = await axios.get(`/api/reportes/${this.reporteSeleccionado}`, { params: this.filtros });
                        
                        console.log("estamos en exportar excel ")  

                        const data = resp.data;
                        if (!data || data.length === 0) {
                          Swal.fire('Sin datos', 'No hay información para exportar', 'info');
                          return;
                        }

                        const workbook = new ExcelJS.Workbook();
                        const worksheet = workbook.addWorksheet('Reporte');


                        // Obtener la fecha y hora actuales del sistema
                        const fechaActual = new Date();
                        const formattedDateTime = `${fechaActual.getDate().toString().padStart(2, '0')}/${
                          (fechaActual.getMonth() + 1).toString().padStart(2, '0')}/${fechaActual.getFullYear()} ${
                          fechaActual.getHours().toString().padStart(2, '0')}:${
                          fechaActual.getMinutes().toString().padStart(2, '0')}:${
                          fechaActual.getSeconds().toString().padStart(2, '0')}`;

                        // Encabezado de la planilla
                        worksheet.addRow(['Municipalidad Distrital de Colquemarca ']);
                        worksheet.addRow(['Fecha de Impresion: ', formattedDateTime]);
                        worksheet.addRow([]);

                        const titleRow = worksheet.addRow(['REPORTE DE EXPEDIENTES REGISTRADOS']);

// Combinar las celdas desde A hasta V (columnas 1 a 22)
worksheet.mergeCells(`A${titleRow.number}:V${titleRow.number}`);

// Estilo del título
titleRow.getCell(1).alignment = { horizontal: 'center' };
titleRow.getCell(1).font = { bold: true, size: 14 };
                        
                        
                        // Aplicar estilo al título
                        titleRow.eachCell(cell => {
                          cell.font = { bold: true, size: 12 }; // Negrita y tamaño de letra más grande
                          //cell.alignment = { horizontal: 'center' }; // Centrar el texto
                        });
                        // Combinar celdas para que el título abarque varias columnas (si es necesario)


                        worksheet.addRow([]);

                      // 🎨 Estilos
                      const headerFill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: 'F2DCDB' }
                      };

                      const border = {
                        top: { style: 'thin' },
                        left: { style: 'thin' },
                        bottom: { style: 'thin' },
                        right: { style: 'thin' }
                      };

                      // 🔠 Crear encabezados
                      const headers = Object.keys(data[0]);
                      const headerRow = worksheet.addRow(headers);

                      headerRow.eachCell(cell => {
                        cell.fill = headerFill;
                        cell.font = { bold: true };
                        cell.alignment = { horizontal: 'center', wrapText: true };
                        cell.border = border;
                      });

                      // 📄 Agregar datos
                      data.forEach(row => {
                        worksheet.addRow(headers.map(key => row[key]));
                      });

                      // 📏 Ajustar tamaño de columnas
                      worksheet.columns.forEach(col => {
                        col.width = 20;
                      });

                      // 💾 Descargar
                      const buffer = await workbook.xlsx.writeBuffer();
                      const blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                      });

                      const link = document.createElement('a');
                      link.href = URL.createObjectURL(blob);
                      link.download = `Reporte_${this.reporteSeleccionado}.xlsx`;
                      link.click();

                    } catch (error) {
                      console.error('Error al generar Excel:', error);
                      Swal.fire('Error', 'No se pudo generar el reporte', 'error');
                    }

                        
                      }
    },

  
  mounted() {
    this.cargarMaestros();
  }
};
</script>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
}
</style>
