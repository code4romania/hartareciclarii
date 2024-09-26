<template>
    <div class="flex items-center justify-start gap-2 px-4 pt-4">
        <FunnelIcon class="w-6 h-6 text-gray-900" />

        <div class="font-semibold text-gray-900" v-text="$t('filter.title')" />
    </div>

    <form class="flex flex-col items-stretch flex-1 px-4 overflow-y-scroll divide-y divide-gray-300">
        <CheckboxList
            name="service_types"
            :label="$t('filter.service_type')"
            v-model="form.filter.service"
            :options="serviceTypes"
            option-value-key="id"
            option-label-key="name"
            class="py-6"
        />

        <template v-for="type in serviceTypes" :key="type.slug">
            <Component
                v-if="form.filter.service.includes(type.id)"
                :is="getFilterComponent(type.slug)"
                :service-type="type"
                :form="form"
            />
        </template>

        <CheckboxList
            name="status"
            :label="$t('filter.status')"
            v-model="form.filter.status"
            :options="statuses"
            class="py-6"
        />
    </form>

    <button
        v-if="hasFilters"
        type="button"
        class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-red-700 border-t hover:bg-red-700 hover:text-white"
        @click="clearFilters"
    >
        <XMarkIcon class="w-5 h-5" />

        <span v-text="$t('filter.clear')" />
    </button>
</template>

<script setup>
    import { computed, ref, watch, inject, nextTick } from 'vue';
    import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import { usePage, useForm } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import Reduce from '@/Components/Map/Filters/Reduce.vue';
    import Reuse from '@/Components/Map/Filters/Reuse.vue';
    import Repairs from '@/Components/Map/Filters/Repairs.vue';
    import WasteCollection from '@/Components/Map/Filters/WasteCollection.vue';
    import cloneDeep from 'lodash.clonedeep';
    import pickBy from 'lodash.pickby';
    import route from '@/Helpers/useRoute.js';
    import typeOf from '@/Helpers/typeOf.js';
    import { getCoordinatesParameter } from '@/Helpers/useCoordinates.js';
    import { headers, onCancelToken } from '@/Helpers/useMap.js';

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

    const getFilterValue = (key, defaultValue) => {
        if (!page.props.filter.hasOwnProperty(key)) {
            return defaultValue;
        }

        let value = page.props.filter[key];

        if (typeof value === 'string') {
            return value.split(',');
        }

        return value || defaultValue;
    };

    const form = useForm({
        filter: {
            // Service types
            service: getFilterValue('service', []),

            // Point types
            ...serviceTypesWithMultiplePointTypes.value.reduce(
                (acc, { slug }) => ({
                    ...acc,
                    [slug]: getFilterValue(slug, []),
                }),
                {}
            ),

            status: getFilterValue('status', []),
        },
    });

    const hasFilters = computed(() =>
        Object.entries(form.filter)
            .map(([key, value]) => value)
            .flat()
            .some(Boolean)
    );

    const setFilterValue = (value) => {
        if (typeOf(value) === Object) {
            return Object.entries(value).reduce(
                (acc, [key, value]) => ({
                    [key]: setFilterValue(value),
                    ...acc,
                }),
                {}
            );
        }

        if (typeOf(value) === String) {
            return value;
        }

        if (typeOf(value) === Array && value.length) {
            return [...new Set(value)].join(',');
        }

        return null;
    };

    const applyFilters = (form, leafletObject) => {
        const url = route('front.map.index', {
            coordinates: getCoordinatesParameter(leafletObject.getCenter(), leafletObject.getZoom()),
        });

        form.transform((data) => {
            data = cloneDeep(data);

            Object.entries(data.filter).forEach(([key, value]) => {
                data.filter[key] = setFilterValue(value);
            });

            data.filter = pickBy(data.filter);

            // console.log(data.filter.type);

            return pickBy(data);
        }).get(url, {
            headers: headers(leafletObject),
            only: ['points', 'mapOptions', 'filter'],
            onCancelToken,
        });
    };

    const clearFilters = () => {
        form.defaults({
            filter: {
                service: [],
                ...serviceTypesWithMultiplePointTypes.value.reduce(
                    (acc, { slug }) => ({
                        ...acc,
                        [slug]: [],
                    }),
                    {}
                ),
                status: [],
            },
        });

        form.reset();
    };

    const map = inject('map');

    const shouldApply = ref(true);

    watch(
        () => page.props.filter,
        (filter) => {
            console.log(filter);
            if (Array.isArray(filter) && !filter.length) {
                shouldApply.value = false;
                form.reset();

                nextTick(() => (shouldApply.value = true));
            }
        },
        { deep: true }
    );

    watch(
        () => form.filter,
        (data) => {
            if (shouldApply.value) {
                applyFilters(form, map.value.leafletObject);
            }
        },
        { deep: true }
    );
</script>
