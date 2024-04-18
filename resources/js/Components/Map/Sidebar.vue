<template>
    <aside class="flex flex-col px-6 pb-4 overflow-y-auto bg-white grow gap-y-5">
        <div class="flex items-center justify-start gap-2 py-4">
            <FunnelIcon class="w-6 h-6 text-gray-900" />

            <div class="font-semibold text-gray-900">Filtre</div>
        </div>

        <ul v-if="selectedServiceType === null">
            <li v-for="(label, type) in serviceTypes" :key="type">
                <button
                    class="flex items-center justify-between w-full text-gray-700"
                    type="button"
                    @click="selectedServiceType = type"
                    v-text="trans(label)"
                />
            </li>
        </ul>

        <section v-else>
            <div class="flex gap-2">
                <button type="button" @click="selectedServiceType = null">&larr;</button>
                <h2>{{ serviceTypes[selectedServiceType] }}</h2>
            </div>

            <CheckboxList v-model="selectedMaterialTypes" :options="collectionPointsTypes" class="flex-col" />
        </section>

        <div class="fixed bottom-0 px-6 py-3 mt-2 bg-white border-t lg:w-96" :class="{ fixed: filtersOpen }">
            <button
                class="flex items-center justify-center w-full text-red-700"
                type="button"
                v-on:click="resetFilters()"
            >
                <desktop-filter-clear-icon></desktop-filter-clear-icon>
                {{ $t('sidebar.clear_filters_label') }}
            </button>
        </div>
    </aside>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { FunnelIcon } from '@heroicons/vue/24/outline';
    import { usePage } from '@inertiajs/vue3';
    import CheckboxList from '@/Components/Form/CheckboxList.vue';
    import {trans} from "laravel-vue-i18n";

    const hasResults = ref(false);
    const collectedFilters = ref({});
    const filtersOpen = ref(true);

    const open = ref(false);
    const filters = ref({});
    const pointFilterLiveSearch = ref('');
    const materialFilterLiveSearch = ref('');
    const selectedMaterialTypes = ref([]);
    const selectedCollectionPointsTypes = ref([]);
    const filtersCount = ref(0);
    const materialTypesFilters = ref([]);
    const searchParamsForFilters = ref({
        search_key: '',
        service_id: null,
    });

    const serviceTypes = computed(() => usePage().props.service_types || []);
    const selectedServiceType = ref(null);

    const collectionPointsTypes = computed(() =>
        Object.entries(usePage().props.point_types[selectedServiceType.value] || []).map(([label, value]) => ({
            value,
            label,
        }))
    );
</script>
