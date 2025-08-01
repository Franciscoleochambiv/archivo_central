<script setup>
import { ref, watch } from "vue";
//import { GoogleMap, Marker } from "vue3-google-map";
import { GoogleMap, Marker, MarkerCluster, Polyline } from "vue3-google-map";
const props = defineProps({
  latitude: Number,
  longitude: Number,
});

const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;

// Definir el centro del mapa basado en las coordenadas
const center = ref({ lat: props.latitude || 0, lng: props.longitude || 0 });

// Actualizar el centro del mapa cuando cambien las props
watch(() => [props.latitude, props.longitude], ([newLat, newLng]) => {
  center.value = { lat: newLat, lng: newLng };
});
</script>

<template>
  <GoogleMap
    :api-key="apiKey"
    style="width: 100%; height: 500px;"
    :center="center"
    :zoom="14"
  >
    <Marker :options="{ position: center }" />
  </GoogleMap>
</template>

<style scoped>
/* Estilo opcional */
</style>
