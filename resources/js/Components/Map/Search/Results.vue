<template>
    <div class="flex items-center justify-start gap-2 px-4 pt-4">
        <MagnifyingGlassIcon class="w-6 h-6 text-gray-900" />

        <div class="font-semibold text-gray-900" v-text="$tChoice('sidebar.results', results.length)" />
    </div>

    <div v-if="results.length" class="flex flex-col items-stretch flex-1 overflow-y-scroll divide-y divide-gray-300">
        <Result v-for="point in results" :key="point.id" :point="point" @click="$emit('selectPoint', point.id)" />
    </div>

    <EmptyState v-else />
</template>

<script setup>
    import { computed } from 'vue';
    import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import { usePage } from '@inertiajs/vue3';
    import Result from '@/Components/Map/Search/Result.vue';
    import EmptyState from '@/Components/Map/Search/EmptyState.vue';

    const emit = defineEmits(['selectPoint']);

    const page = usePage();

    const results = computed(() => page.props.points || []);
</script>
