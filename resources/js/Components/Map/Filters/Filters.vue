<template>
    <div class="flex items-center justify-start gap-2 px-4 pt-4">
        <FunnelIcon class="w-6 h-6 text-gray-900" />

        <div class="font-semibold text-gray-900" v-text="$t('filter.title')" />
    </div>

    <form class="flex flex-col items-stretch flex-1 px-4 overflow-y-scroll divide-y divide-gray-300">
        <CheckboxList
            name="service_types"
            :label="$t('filter.service_type')"
            v-model="form.service"
            :options="serviceTypes"
            option-value-key="id"
            option-label-key="name"
            class="py-6"
        />

        <template v-for="serviceType in serviceTypes" :key="serviceType.slug">
            <template v-if="form.service.includes(serviceType.id)">
                <CheckboxList
                    v-if="serviceType.point_types.length > 1"
                    :name="`${serviceType.slug}.type`"
                    :label="$t(`filter.point_type.${serviceType.slug}`)"
                    v-model="form[serviceType.slug]"
                    :options="serviceType.point_types"
                    option-value-key="id"
                    option-label-key="name"
                    class="py-6"
                />

                <MaterialsChecklist
                    v-if="serviceType.can.collect_materials"
                    name="materials"
                    v-model="form.materials[serviceType.slug]"
                    :label="$t('filter.materials')"
                    class="py-6"
                    searchable
                    clearable
                />

                <CheckboxList
                    v-if="characteristics(serviceType).length"
                    :name="`${serviceType.slug}.type`"
                    :label="$t(`filter.characteristics.${serviceType.slug}`)"
                    v-model="form.can[serviceType.slug]"
                    :options="characteristics(serviceType)"
                    class="py-6"
                />
            </template>
        </template>

        <CheckboxList
            name="status"
            :label="$t('filter.status')"
            v-model="form.status"
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
    import cloneDeep from 'lodash.clonedeep';
    import pickBy from 'lodash.pickby';
    import route from '@/Helpers/useRoute.js';
    import { isArray, isNumber, isString } from '@/Helpers/checkType.js';
    import { getCoordinatesParameter } from '@/Helpers/useCoordinates.js';
    import { headers, onCancelToken } from '@/Helpers/useMap.js';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import MaterialsChecklist from '@/Components/Form/MaterialsChecklist.vue';
    import { trans } from 'laravel-vue-i18n';
    import { isObject } from '@vueuse/core';

    const page = usePage();

    const serviceTypes = computed(() => page.props.service_types || []);

    const serviceTypesWithMultiplePointTypes = computed(() =>
        serviceTypes.value.filter((filter) => filter.point_types.length > 1)
    );

    const statuses = computed(() => page.props.statuses || []);

    const getFilterValue = (key, defaultValue, prefix) => {
        if (!page.props.hasOwnProperty('filter')) {
            return defaultValue;
        }

        let filter =
            isString(prefix) && page.props.filter.hasOwnProperty(prefix)
                ? page.props.filter[prefix]
                : page.props.filter;

        if (!filter.hasOwnProperty(key)) {
            return defaultValue;
        }

        let value = filter[key];

        if (isNumber(value)) {
            return [value];
        }

        if (isString(value)) {
            return value.split(',');
        }

        return value || defaultValue;
    };

    const characteristics = (serviceType) =>
        Object.entries(serviceType.can)
            .filter(([key, value]) => !['have_business_name', 'collect_materials'].includes(key) && value)
            .map(([key, value]) => ({
                value: key,
                label: trans(`filter.characteristics.${key}`),
            }));

    const reduceWithFilterValue = (prefix) => {
        return (acc, { slug }) => ({
            ...acc,
            [slug]: getFilterValue(slug, [], prefix),
        });
    };

    const form = useForm({
        // Service types
        service: getFilterValue('service', []),

        // Point types
        ...serviceTypesWithMultiplePointTypes.value.reduce(reduceWithFilterValue(), {}),

        // Materials
        materials: serviceTypes.value
            .filter((serviceType) => serviceType.can.collect_materials)
            .reduce(reduceWithFilterValue('materials'), {}),

        // Characteristics
        can: serviceTypes.value
            .filter((serviceType) => characteristics(serviceType).length)
            .reduce(reduceWithFilterValue('can'), {}),

        // Status
        status: getFilterValue('status', []),
    });

    const hasFilters = computed(() =>
        Object.entries(form)
            .map(([key, value]) => value)
            .flat()
            .some(Boolean)
    );

    const setFilterValue = (value) => {
        if (isObject(value)) {
            return Object.entries(value).reduce(
                (acc, [key, value]) => ({
                    [key]: setFilterValue(value),
                    ...acc,
                }),
                {}
            );
        }

        if (isString(value)) {
            return value;
        }

        if (isArray(value) && value.length) {
            return [...new Set(value)].join(',');
        }

        return null;
    };

    const applyFilters = (form, leafletObject) => {
        const transform = (data) => {
            data = cloneDeep(data);

            Object.entries(data).forEach(([key, value]) => {
                data[key] = setFilterValue(value);
            });

            data.can = pickBy(data.can);
            data.materials = pickBy(data.materials);

            data = pickBy(data);

            return pickBy(data);
        };

        form.transform(transform).get(
            route('front.map.index', {
                coordinates: getCoordinatesParameter(leafletObject.getCenter(), leafletObject.getZoom()),
            }),
            {
                headers: headers(leafletObject),
                only: ['context', 'points', 'mapOptions', 'filter'],
                onCancelToken,
            }
        );
    };

    const clearFilters = () => {
        let callback = (acc, { slug }) => ({
            ...acc,
            [slug]: [],
        });

        form.defaults({
            // Service types
            service: [],

            // Point types
            ...serviceTypesWithMultiplePointTypes.value.reduce(callback, {}),

            // Materials
            materials: serviceTypes.value
                .filter((serviceType) => serviceType.can.collect_materials)
                .reduce(callback, {}),

            // Characteristics
            can: serviceTypes.value.filter((serviceType) => characteristics(serviceType).length).reduce(callback, {}),

            // Status
            status: [],
        });

        form.reset();
    };

    const map = inject('map');

    const shouldApply = ref(true);

    watch(
        () => page.props?.filter,
        (filter) => {
            if (isArray(filter) && !filter.length) {
                shouldApply.value = false;
                clearFilters();
                nextTick(() => (shouldApply.value = true));
            }
        },
        { deep: true }
    );

    watch(
        () => form.data(),
        () => {
            if (shouldApply.value) {
                applyFilters(form, map.value.leafletObject);
            }
        },
        { deep: true }
    );
</script>
