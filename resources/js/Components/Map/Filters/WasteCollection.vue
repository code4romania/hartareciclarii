<template>
    <CheckboxList
        :name="`${serviceType.slug}.type`"
        label="Tip punct colectare"
        v-model="filter.pt[serviceType.slug]"
        :options="serviceType.point_types"
        option-value-key="id"
        option-label-key="name"
        class="py-6"
    />

    <MaterialsChecklist
        name="materials"
        v-model="filter.pt.materials"
        :label="$t('sidebar.materials')"
        class="py-6"
        searchable
        clearable
    />
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import MaterialsChecklist from '@/Components/Form/MaterialsChecklist.vue';

    const props = defineProps({
        modelValue: Object,
        serviceType: Object,
    });

    const emit = defineEmits(['update:modelValue']);

    const filter = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });
</script>
