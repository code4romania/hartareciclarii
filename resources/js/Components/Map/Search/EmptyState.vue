<template>
    <div class="flex flex-col items-center gap-10 p-6 text-center border-t border-gray-300">
        <Icon icon="search-empty" class="w-32 h-32" />

        <p v-text="$t('sidebar.no_results_found', { query: $page.props.query })" />

        <Button :label="$t('sidebar.see_all_points')" @click="closePanel" />
    </div>
</template>

<script setup>
    import Icon from '@/Components/Icon.vue';
    import Button from '@/Components/Button.vue';
    import { closePanel } from '@/Helpers/useMap.js';
</script>
