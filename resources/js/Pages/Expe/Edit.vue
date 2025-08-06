<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Edicion" />
    <AuthenticatedLayout>
      <div class="mx-auto max-w-screen-xl w-full px-4 py-2 sm:px-6 lg:px-8">
        
      <div class="mx-auto max-w-full">     

    <h2 class="text-center text-2xl font-bold text-black-600 sm:text-3xl">Edición de Expediente</h2>      

    <div class="max-w-5xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        
        <form @submit.prevent="actualizar" enctype="multipart/form-data">
               <p class="text-center custom-paragraph  text-lg font-medium bg-red-400 text-white rounded-full py-2 px-4">
                   Usuario    - {{ $page.props.auth.user.name }}
              </p>    
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Oficina</label>
                    <select v-model="form.oficina_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                        <option v-for="o in oficinas" :value="o.id" :key="o.id">{{ o.nombre }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Entidad</label>
                    <select v-model="form.entidad_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                        <option v-for="ent in entidades" :value="ent.id" :key="ent.id">{{ ent.nombre }}</option>
                    </select>
                </div>

                <input v-model="form.periodo" placeholder="Periodo" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.anio_elaboracion" placeholder="Año elaboración" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.seccion" placeholder="Sección" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input type="date" v-model="form.fechas_extremos" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.nro_archivo" placeholder="Nro archivador" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.unidad_conservacion" placeholder="Unidad conservación" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.serie_documental" placeholder="Serie documental" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.nro_comprobantes" placeholder="Nro de Documento" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.ubicacion_estante" placeholder="Ubicación estante" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <select v-model="form.valor_serie_documental" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                    <option value="T">Temporal</option>
                    <option value="P">Permanente</option>
                </select>
                <input v-model="form.folios" placeholder="Folios" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.soporte_papel" placeholder="Soporte papel" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <select value=0  v-model="form.es_copia_original" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                    <option :value="1">ORIGINAL</option>
                    <option :value="0">COPIA</option>
                </select>
                <input v-model="form.anio_extremo_inicio" placeholder="Año extremo inicio" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.anio_extremo_fin" placeholder="Año extremo fin" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <input v-model="form.color" placeholder="Color" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm" />
                <textarea v-model="form.observaciones" placeholder="Observaciones" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm"></textarea>
                <select v-model="form.estado_archivador" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                    <option value="B">Bueno</option>
                    <option value="M">Mal estado</option>
                    <option value="R">Regular</option>
                </select>
                <select v-model="form.ubicacion_actual" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-2 pe-12 text-sm shadow-sm">
                    <option value="A.C">Archivo Central</option>
                    <option value="PRESTAMO">Préstamo</option>
                    <option value="DEVUELTO">Devuelto</option>
                </select>

               <div class="col-span-full">
                    <label class="text-sm text-gray-600 mb-1 block">Documentos adjuntos actuales</label>
                    <ul class="mb-2 space-y-1">
                        <li v-for="doc in form.documentos" :key="doc.id" class="flex items-center justify-between bg-gray-100 p-2 rounded">
                            <span class="text-sm text-gray-700">{{ doc.nombre }}</span>
                            <a
                                :href="`/storage/${doc.ruta}`"
                                class="text-blue-600 hover:underline text-sm"
                                target="_blank"
                                rel="noopener"
                            >
                                Descargar
                            </a>
                        </li>
                    </ul>

                    <label class="text-sm text-gray-600">Cambiar / agregar documentos</label>
                    <input type="file" multiple @change="handleArchivos" />

                    
                </div>

            </div>

            <div class="mt-4">
                <button class="bg-red-400 hover:bg-red-700 text-white px-4 py-2 rounded" type="submit">Actualizar</button>
            </div>
        </form>
    </div>
   </div>
   </div> 
     </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            expedienteId: null,
            oficinas: [],
            entidades: [],
            archivos: [],
            form: {
                oficina_id: '',
                entidad_id: '',
                periodo: '',
                anio_elaboracion: '',
                seccion: '',
                fechas_extremos: '',
                nro_archivo: '',
                unidad_conservacion: '',
                serie_documental: '',
                nro_comprobantes: '',
                ubicacion_estante: '',
                valor_serie_documental: 'T',
                folios: '',
                soporte_papel: '',
                es_copia_original: 1,
                anio_extremo_inicio: '',
                anio_extremo_fin: '',
                color: '',
                observaciones: '',
                estado_archivador: 'B',
                ubicacion_actual: 'A.C',
            }
        };
    },
    methods: {
        async cargarExpediente() {
            const res = await axios.get(`/api/expedientes/${this.expedienteId}`);

            console.log(res.data)
            this.form = { ...this.form, ...res.data };
        },
        async cargarOficinas() {
            const res = await axios.get('/api/oficinaexcel');
            this.oficinas = res.data;
        },
        async cargarEntidades() {
            const res = await axios.get('/api/entidades');
            this.entidades = res.data;
        },
        handleArchivos(e) {
            this.archivos = Array.from(e.target.files);
        },
        async actualizar() {
            const formData = new FormData();
            for (const key in this.form) {
                formData.append(key, this.form[key]);
            }
            for (const file of this.archivos) {
                formData.append('documentos[]', file);
            }

            await axios.post(`/api/expedientes/${this.expedienteId}?_method=PUT`, formData);


            this.$inertia.visit('/listobras', { method: 'get' }); // Esto redirigirá a la ruta principal
            alert("Expediente actualizado correctamente");
        }
    },
    mounted() {
        const urlParams = window.location.pathname.split('/');
        this.expedienteId = urlParams[urlParams.length - 1];

        this.cargarOficinas();
        this.cargarEntidades();
        this.cargarExpediente();
    }
};
</script>
