<template>
    <div class="flex flex-col min-h-screen bg-gray-50">
        <Header class="shrink-0" dashboard />

        <div class="relative flex-1">
            <slot />
        </div>
    </div>
</template>

<script setup>
    import Header from '@/Components/Navigation/Header.vue';
</script>
