<template>
    <div class="flex flex-col min-h-screen overflow-hidden md:h-screen">
        <Header class="shrink-0" />

        <div class="relative flex-1">
            <slot />
        </div>
    </div>
</template>

<script setup>
    import Header from '@/Components/Navigation/Header.vue';
</script>
