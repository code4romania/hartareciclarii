<template>
    <div>Waste Collection</div>

    <CheckboxList
        :name="`${serviceType.slug}.type`"
        label="Tip punct colectare"
        v-model="filter.pt[serviceType.slug]"
        :options="serviceType.point_types"
        option-value-key="id"
        option-label-key="name"
    />

    <div>Materials</div>
    <Tree v-model:selectionKeys="filter.pt.materials" :value="materials" selectionMode="checkbox" />
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import Checkbox from 'primevue/checkbox';
    import Tree from 'primevue/tree';

    const page = usePage();

    const props = defineProps({
        modelValue: Object,
        serviceType: Object,
    });

    const emit = defineEmits(['update:modelValue']);

    const filter = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const materials = computed(() => page.props.materials || []);
</script>
