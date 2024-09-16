<template>
    <Narrative :form="form" :legend="problemType.name">
        <template #footer>
            <FileUpload
                name="images"
                :label="$t('report.other.images')"
                :help="$t('report.other.images_help')"
                v-model="form.images"
                :errors="[form.errors.images]"
                :chooseLabel="$t('report.other.images_add')"
            />
        </template>
    </Narrative>
</template>

<script setup>
    import Narrative from '@/Components/ReportPointSteps/Narrative.vue';
    import FileUpload from '@/Components/Form/FileUpload.vue';

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
