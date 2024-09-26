<template>
    <CheckboxList
        v-if="serviceType.point_types.length > 1"
        :name="`${serviceType.slug}.type`"
        :label="$t(`filter.point_type.${serviceType.slug}`)"
        v-model="form.filter[serviceType.slug]"
        :options="serviceType.point_types"
        option-value-key="id"
        option-label-key="name"
        class="py-6"
    />

    <MaterialsChecklist
        v-if="serviceType.can.collect_materials"
        name="materials"
        v-model="form.filter.materials"
        :label="$t('filter.materials')"
        class="py-6"
        searchable
        clearable
    />

    <CheckboxList
        v-if="characteristics.length"
        :name="`${serviceType.slug}.type`"
        :label="$t(`filter.point_type.${serviceType.slug}`)"
        v-model="form.filter[serviceType.slug].characteristics"
        :options="chara"
        option-value-key="id"
        option-label-key="name"
        class="py-6"
    />
</template>

<script setup>
    import { computed } from 'vue';
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

    const characteristics = computed(() =>
        Object.entries(serviceType.can)
            .filter(([key, value]) => key !== 'have_business_name' && value)
            .map(([key, value]) => ({
                value: key,
                label: $t(`filter.characteristics.${value}`),
            }))
    );
</script>
