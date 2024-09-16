<template>
    <Narrative :form="form" :legend="problemType.name">
        <template #header>
            <CheckboxList
                name="sub_types"
                :label="$t('report.rejected_waste.sub_types')"
                :options="problemType.children"
                v-model="form.sub_types"
                :errors="[form.errors.sub_types]"
                option-value-key="id"
                option-label-key="name"
            />
        </template>
    </Narrative>
</template>

<script setup>
    import Narrative from '@/Components/ReportPointSteps/Narrative.vue';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';

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
