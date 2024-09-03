<template>
    <div class="empty:hidden">
        <button
            v-if="isSupported"
            type="button"
            class="text-sm font-medium text-primary-800 hover:underline"
            v-text="$t('add_point.type.use_my_current_location')"
            @click="resume"
        />
    </div>
</template>

<script setup>
    import { watch } from 'vue';
    import { useGeolocation } from '@vueuse/core';

    const emit = defineEmits(['located']);

    const { coords, locatedAt, error, isSupported, resume, pause } = useGeolocation({
        immediate: false,
    });

    watch(locatedAt, () => {
        emit('located', {
            lat: coords.value.latitude,
            lng: coords.value.longitude,
        });

        pause();
    });
</script>
