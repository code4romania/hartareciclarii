<template>
    <aside class="flex-col gap-4 overflow-hidden bg-white grow w-80">
        <Filters v-if="showFilters" />

        <SearchResults v-else @select-point="$emit('selectPoint', $event)" />
    </aside>
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import Filters from '@/Components/Map/Filters/Filters.vue';
    import SearchResults from '@/Components/Map/Search/Results.vue';

    const emit = defineEmits(['selectPoint']);

    const page = usePage();

    const showFilters = computed(() => page.props.context !== 'search');
</script>
