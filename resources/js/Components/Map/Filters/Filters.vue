<template>
    <div class="flex items-center justify-start gap-2 px-4 pt-4">
        <FunnelIcon class="w-6 h-6 text-gray-900" />

        <div class="font-semibold text-gray-900">Filtre</div>
    </div>

    <div class="flex flex-col items-stretch flex-1 px-4 overflow-y-scroll divide-y divide-gray-300">
        <CheckboxList
            name="service_types"
            label="Tip serviciu"
            v-model="filter.st"
            :options="serviceTypes"
            option-value-key="id"
            option-label-key="name"
            class="py-6"
        />

        <template v-for="type in serviceTypes" :key="type.slug">
            <Component
                v-if="filter.st.includes(type.id)"
                :is="getFilterComponent(type.slug)"
                :service-type="type"
                v-model="filter"
            />
        </template>

        <CheckboxList name="status" label="Caracteristici" v-model="filter.ceva" :options="statuses" class="py-6" />
        <CheckboxList name="status" label="Status punct" v-model="filter.status" :options="statuses" class="py-6" />
    </div>

    <button
        type="button"
        class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-red-700 border-t hover:bg-red-700 hover:text-white"
        @click="clearFilters"
    >
        <XMarkIcon class="w-5 h-5" />

        <span v-text="$t('sidebar.clear_filters_label')" />
    </button>
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { usePage } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import useFilters from '@/Helpers/useFilters';

    import Reduce from '@/Components/Map/Filters/Reduce.vue';
    import Reuse from '@/Components/Map/Filters/Reuse.vue';
    import Repairs from '@/Components/Map/Filters/Repairs.vue';
    import WasteCollection from '@/Components/Map/Filters/WasteCollection.vue';
    import route from '@/Helpers/useRoute';
    import pickBy from '@/Helpers/pickBy';

    const page = usePage();

    const serviceTypes = computed(() => page.props.service_types || []);

    const serviceTypesWithMultiplePointTypes = computed(() =>
        serviceTypes.value.filter((filter) => filter.point_types.length > 1)
    );

    const statuses = computed(() => page.props.statuses || []);

    const getFilterComponent = (name) =>
        ({
            reduce: Reduce,
            reuse: Reuse,
            repairs: Repairs,
            waste_collection: WasteCollection,
        })[name] || null;

    const filter = ref({
        st: page.props.filter?.service_types || [],
        pt: serviceTypesWithMultiplePointTypes.value.reduce((acc, { slug }) => {
            acc[slug] = page.props.filter?.point_types?.[slug] || {};

            return acc;
        }, {}),
    });

    const hasFilters = computed(() =>
        Object.entries(filter.value)
            .map(([key, value]) => value)
            .flat()
            .some(Boolean)
    );

    const url = route(route().current(), route().params);

    const { applyFilters, clearFilters } = useFilters(filter, url);

    // watch(filter, applyFilters, { deep: true });
</script>
