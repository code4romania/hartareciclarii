<template>
    <CheckboxList
        :name="`${serviceType.slug}.type`"
        :label="$t('filter.point_type.repairs')"
        v-model="form.filter.repairs"
        :options="serviceType.point_types"
        option-value-key="id"
        option-label-key="name"
        class="py-6"
    />
</template>

<script setup>
    import CheckboxList from '@/Components/Form/CheckboxList.vue';

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
