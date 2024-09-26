<template>
    <CheckboxList
        :name="`${serviceType.slug}.type`"
        :label="$t('filter.point_type.waste_collection')"
        v-model="form.filter.waste_collection"
        :options="serviceType.point_types"
        option-value-key="id"
        option-label-key="name"
        class="py-6"
    />

    <MaterialsChecklist
        name="materials"
        v-model="form.filter.materials"
        :label="$t('filter.materials')"
        class="py-6"
        searchable
        clearable
        as-filter
    />
</template>

<script setup>
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import MaterialsChecklist from '@/Components/Form/MaterialsChecklist.vue';

    const props = defineProps({
        serviceType: {
            type: Object,
            required: true,
        },
        form: {
            type: Object,
            required: true,
        },
    });
</script>
