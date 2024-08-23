<template>
    <div class="flex flex-col h-screen overflow-hidden">
        <Header class="shrink-0" />

        <div class="relative flex-1">
            <slot />
        </div>
    </div>
</template>

<script setup>
    import Header from '@/Components/Navigation/Header.vue';
</script>
