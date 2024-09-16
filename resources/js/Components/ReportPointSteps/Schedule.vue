<template>
    <Narrative :form="form" :legend="problemType.name" required />
</template>

<script setup>
    import Narrative from '@/Components/ReportPointSteps/Narrative.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        problemType: {
            type: Object,
            required: true,
        },
    });
</script>
