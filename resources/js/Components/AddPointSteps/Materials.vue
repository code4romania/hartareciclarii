<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.materials.subtitle')" />

        <MaterialsChecklist
            name="materials"
            v-model="form.materials"
            :label="$t('add_point.materials.label')"
            :errors="[form.errors.materials]"
            required
        />
    </fieldset>
</template>

<script setup>
    import MaterialsChecklist from '@/Components/Form/MaterialsChecklist.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
    });
</script>
