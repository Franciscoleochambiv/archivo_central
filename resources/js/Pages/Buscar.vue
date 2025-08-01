<template>
    <Head title="Buscar Expedientes" />

    <div class="relative flex justify-center min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div class="w-full max-w-7xl p-6 lg:p-8">
            <img src="/asset/lolo.jpeg" alt="Logo de la Municipalidad" class="mx-auto max-w-full h-auto mb-10" />

            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                <!-- Formulario -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Buscar Expedientes</h3>
                 <div class="w-full flex justify-center">
                    <form @submit.prevent="buscar" class="flex flex-wrap items-end gap-4">
                    <div class="flex flex-col">
                        <label class="text-sm text-gray-600 dark:text-gray-300">Oficina</label>
                        <select v-model="filtros.oficina_id" class="border-gray-300 rounded-md w-48">
                            <option value="">Todas las oficinas</option>
                            <option v-for="o in oficinas" :key="o.id" :value="o.id">{{ o.nombre }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm text-gray-600 dark:text-gray-300">Serie documental</label>
                        <input v-model="filtros.serie" type="text" class="border-gray-300 rounded-md w-48" placeholder="Ej: 001-500" />
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm text-gray-600 dark:text-gray-300">N° de comprobantes</label>
                        <input v-model="filtros.comprobante" type="text" class="border-gray-300 rounded-md w-48" placeholder="Ej: 50" />
                    </div>

                    <div>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            Buscar
                        </button>
                    </div>
                </form>
               </div> 

              

                <!-- Resultados -->
               
                    

                    <div v-if="resultados.length">
                        <div v-for="r in resultados" :key="r.id" class="space-y-2 text-sm dark:text-gray-200">    


                            
                            <hr class="my-3 border-t" />   
                           <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            
                            <p class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"><strong>Oficina:</strong> {{ r.oficina.nombre }}</p>
                            <p><strong>ID:</strong> {{ r.id}}</p>
                            <p><strong>Periodo:</strong> {{ r.periodo }}</p>
                            <p><strong>Año Elaboración:</strong> {{ r.anio_elaboracion }}</p>
                            <p><strong>Sección:</strong> {{ r.seccion }}</p>
                            <p><strong>Fechas Extremos:</strong> {{ r.fechas_extremos }}</p>
                            <p><strong>Item:</strong> {{ r.item }}</p>
                            <p><strong>Nro. Archivo:</strong> {{ r.nro_archivo }}</p>
                            <p><strong>Unidad de Conservación:</strong> {{ r.unidad_conservacion }}</p>
                            <p><strong>Serie Documental:</strong> {{ r.serie_documental }}</p>
                            <p><strong>Nro. Comprobantes:</strong> {{ r.nro_comprobantes }}</p>
                            <p><strong>Ubicación Estante:</strong> {{ r.ubicacion_estante }}</p>
                            <p><strong>Valor Serie Documental:</strong> {{ r.valor_serie_documental }}</p>
                            <p><strong>Folios:</strong> {{ r.folios }}</p>
                            <p><strong>Soporte Papel:</strong> {{ r.soporte_papel }}</p>
                            <p><strong>Es Copia Original:</strong> {{ r.es_copia_original ? 'Sí' : 'No' }}</p>
                            <p><strong>Año Extremo Inicio:</strong> {{ r.anio_extremo_inicio }}</p>
                            <p><strong>Año Extremo Fin:</strong> {{ r.anio_extremo_fin }}</p>
                            <p><strong>Color:</strong> {{ r.color || 'N/A' }}</p>
                            <p><strong>Observaciones:</strong> {{ r.observaciones || 'Ninguna' }}</p>
                            <p><strong>Estado Archivador:</strong> {{ r.estado_archivador }}</p>
                            <p class=" text-green-600 font-semibold"><strong >Ubicación Actual:</strong> {{ r.ubicacion_actual }}</p>


                            <!-- Documentos Adjuntos -->
                            <div>
                                <h4 class=" text-green-600 font-semibold">Documentos Adjuntos:</h4>
                                
                                <ul v-if="r.documentos && r.documentos.length">
                                    <li v-for="doc in r.documentos" :key="doc.id">
                                        📎 <a :href="`/storage/${doc.ruta}`" target="_blank" class="text-blue-500 underline">{{ doc.nombre }}</a>
                                    </li>
                                </ul>
                                <p v-else>No hay documentos adjuntos.</p>
                            </div>

                            <!-- Movimientos -->
                            <div class="mt-4">
                                <h4 class="font-semibold">Movimientos:</h4>
                                <ul v-if="r.movimientos && r.movimientos.length">
                                    <li v-for="m in r.movimientos" :key="m.id">
                                        📌 {{ m.tipo }} por {{ m.usuario }} — {{ m.fecha }} <span v-if="m.comentario">({{ m.comentario }})</span>
                                    </li>
                                </ul>
                                <p v-else>No hay movimientos registrados.</p>
                            </div>
                           </div>                      
                        </div>
                    </div>

                    <div v-else class="text-gray-500 dark:text-gray-400 text-sm">No se encontraron resultados.</div>
                </div>
            </div>  
        </div>
    </div>
</template>

<script>
export default {
    name: 'BuscarExpedientes',
    data() {
        return {
            filtros: {
                oficina_id: '',
                serie: '',
                comprobante: ''
            },
            oficinas: [],
            resultados: []
        };
    },
    methods: {
        async buscar() {
            const res = await axios.get('/api/expedientes/buscar', {
                params: this.filtros
            });
            this.resultados = res.data;

            console.log(this.resultados)
        },
        async obtenerOficinas() {
            const res = await axios.get('/api/oficinaexcel');
            this.oficinas = res.data;
        }
    },
    mounted() {
        this.obtenerOficinas();
    }
}
</script>
