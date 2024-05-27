<template>

    <div class="">
        <InputComponent
            :label="$t('add_point.first_step.exact_address_label')"
            id="service_name"
            :placeholder="$t('add_point.first_step.exact_address_placeholder')"
            type="text"
            v-model="query"
            :isRequired="true"
            color="gray-700"
            :error="errors"/>
      <div class="min-h-56  h-full">
          <LMap
              ref="map"
              :useGlobalLeaflet="true"
              :min-zoom="10"
              :zoom="10"
              :max-zoom="18"
              :center="[45.9432, 24.9668]"
              @ready="ready"
              @moveend="moveend"
              :options="{
            zoomControl: false,
        }"

              class="w-full h-full z-0"
          >
              <LControlZoom position="bottomright" />
              <LTileLayer
                  url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                  layer-type="base"
                  subdomains="abcd"
                  name="OpenStreetMap"
                  attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
              />
          </LMap>

      </div>
    </div>
</template>

<script setup>

import InputComponent from "@/Components/InputComponent.vue";
import {ref, watch} from "vue";
import {LControlZoom, LMap, LMarker, LTileLayer} from "@vue-leaflet/vue-leaflet";
import {router} from "@inertiajs/vue3";
import {useGeolocation} from "@vueuse/core";


const query = ref('')
const props = defineProps({
    errors: {
        type: String,
        default: null
    }
})
const moveend = (event) => {
    const bounds = event.target.getBounds();
    const center = event.target.getCenter();

    router.reload({
        data: {
            bounds: bounds.toBBoxString(),
            center: `${center.lat},${center.lng}`,
        },
        only: ['points','search_results'],
    });
};


const ready = (leafletObject) => {
    const { coords, locatedAt, error, resume, pause } = useGeolocation({
        enableHighAccuracy: true,
    })
    watch(locatedAt, () => {
        pause();

        leafletObject.flyTo([coords.value.latitude, coords.value.longitude], 17, {
            animate: false,
        });
    });

};
</script>
