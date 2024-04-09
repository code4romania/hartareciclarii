<template>
    <div class="w-full h-full">
        <LMap
            ref="map"
            :useGlobalLeaflet="true"
            :min-zoom="6"
            :zoom="8"
            :max-zoom="18"
            :center="[45.9432, 24.9668]"
            @moveend="moveend"
        >
            <LTileLayer
                url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png"
                layer-type="base"
                subdomains="abcd"
                name="OpenStreetMap"
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            />
            <LMarkerClusterGroup>
                <LMarker v-for="point in points" :key="point.id" :lat-lng="point.latlng" />
            </LMarkerClusterGroup>
        </LMap>
    </div>
</template>

<script setup>
    import L from 'leaflet';
    globalThis.L = L;

    import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';
    import { LMarkerClusterGroup } from 'vue-leaflet-markercluster';

    import 'leaflet/dist/leaflet.css';
    import 'vue-leaflet-markercluster/dist/style.css';

    import { ref, computed } from 'vue';
    import { router, usePage } from '@inertiajs/vue3';

    const points = computed(() => usePage().props.points?.data || []);

    const map = ref(null);

    const moveend = (event) => {
        const obj = map.value.leafletObject;

        router.reload({
            data: {
                bounds: obj.getBounds().toBBoxString(),
            },
            only: ['points'],
        });
    };
</script>
