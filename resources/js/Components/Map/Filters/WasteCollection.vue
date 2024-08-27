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

    <!-- <CheckboxList
        :name="`${serviceType.slug}.offers`"
        label="Caracteristici reciclator"
        v-model="filter.pt[serviceType.slug].offers"
        :options="serviceType.point_types"
    /> -->
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';

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
