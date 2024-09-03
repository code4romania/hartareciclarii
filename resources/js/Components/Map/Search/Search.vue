<template>
    <form class="relative z-20" @submit.prevent="search" v-click-away="close">
        <div class="relative z-20">
            <MagnifyingGlassIcon
                class="absolute inset-y-2.5 w-6 h-6 pointer-events-none left-4"
                :class="{
                    'text-primary-600': query,
                    'text-gray-400': !query,
                }"
            />

            <input
                type="search"
                name="search"
                ref="input"
                class="block w-full rounded-full border-0 pl-12 pr-4 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-gray-300 sm:text-sm sm:leading-6 shadow [&::-webkit-search-cancel-button]:hidden"
                placeholder="Caută"
                autocomplete="off"
                v-model="query"
                @keydown.esc.stop="clear"
                @focus="open"
            />

            <button
                v-if="queryIsValid"
                type="button"
                class="absolute w-5 h-5 overflow-hidden text-gray-500 rounded-full inset-y-3 right-4"
                @click="closeSearch"
            >
                <XMarkIcon />
            </button>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition ease-in duration-50"
            leave-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="suggesting"
                class="absolute z-10 w-full pt-10 -mt-10 overflow-hidden text-sm bg-white border border-gray-300 shadow-lg rounded-2xl empty:hidden"
            >
                <div v-if="!queryIsValid" class="px-4 py-10 mx-auto text-center text-gray-400 max-w-52">
                    Caută o adresă, un punct, sau un material
                </div>

                <ArrowPathIcon v-else-if="loading" class="w-5 h-5 m-4 mx-auto text-gray-400 animate-spin" />

                <ol v-else class="py-2">
                    <li v-for="(result, index) in suggestions" :key="index">
                        <Link
                            v-if="result.type !== 'location'"
                            :href="result.url"
                            class="flex w-full gap-2 px-4 py-2 text-sm hover:bg-gray-100"
                            @click="clear"
                        >
                            <Icon
                                v-if="result.type === 'point'"
                                :icon="`services/${result.icon}`"
                                class="w-5 h-5 shrink-0"
                            />
                            <img v-else-if="result.type === 'material'" :src="result.icon" class="w-5 h-5 shrink-0" />

                            <span class="font-semibold" v-text="result.name" />

                            <span class="flex-1 text-gray-500 truncate" v-text="result.description" />
                        </Link>

                        <button
                            v-else
                            type="button"
                            class="flex w-full gap-2 px-4 py-2 text-sm text-left hover:bg-gray-100"
                            @click="$emit('locate', result)"
                        >
                            <MapPinIcon class="w-5 h-5 fill-gray-400" />

                            <span class="flex-1 truncate" v-text="result.name" />
                        </button>
                    </li>

                    <li v-if="showFallback">
                        <button type="submit" class="flex w-full gap-2 px-4 py-2 text-sm text-left hover:bg-gray-100">
                            <MagnifyingGlassIcon class="w-5 h-5 fill-gray-400" />
                            <span class="flex-1 truncate" v-text="query" />
                        </button>
                    </li>
                </ol>
            </div>
        </transition>
    </form>
</template>

<script setup>
    import axios from 'axios';
    import route from '@/Helpers/useRoute.js';
    import { MagnifyingGlassIcon, MapPinIcon, ArrowPathIcon, XMarkIcon } from '@heroicons/vue/16/solid';
    import { computed, ref, watch } from 'vue';
    import { router, usePage, Link } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';
    import Icon from '@/Components/Icon.vue';
    import { getCoordinatesParameter } from '@/Helpers/useCoordinates.js';
    import { updateMap, closePanel } from '@/Helpers/useMap.js';

    const emit = defineEmits(['locate']);

    const props = defineProps({
        map: Object,
    });

    const page = usePage();

    const input = ref(null);
    const query = ref(page.props.query || null);
    const loading = ref(false);
    const suggesting = ref(false);
    const showFallback = ref(false);
    const highlightedItem = ref(null);

    const suggestions = ref([]);

    const hasSuggestions = computed(() => {
        if (!suggestions.value.length) {
            return false;
        }

        return true;
    });

    const queryIsValid = computed(() => query.value !== null && query.value.length);

    const search = (event) => {
        updateMap(props.map.leafletObject, 'front.map.search', {
            query: query.value,
        });

        close();
    };

    const suggest = useDebounceFn(() => {
        highlightedItem.value = null;
        showFallback.value = false;

        if (!queryIsValid.value) {
            return;
        }

        suggesting.value = true;
        loading.value = true;

        const center = props.map.leafletObject.getCenter();
        const zoom = props.map.leafletObject.getZoom();
        const bounds = props.map.leafletObject.getBounds();

        axios
            .get(
                route('front.map.suggest', {
                    coordinates: getCoordinatesParameter(center, zoom),
                    query: query.value,
                }),
                {
                    headers: {
                        'Map-Bounds': bounds.toBBoxString(),
                    },
                }
            )
            .then((response) => {
                if (Array.isArray(response.data)) {
                    suggestions.value = response.data;
                }
            })
            .catch((error) => {
                console.error(error);
            })
            .finally(() => {
                loading.value = false;
                showFallback.value = true;
            });
    }, 500);

    watch(query, suggest);

    const open = () => {
        suggesting.value = true;
        // query.value = null;
        input.value.focus();
    };

    const clear = () => {
        query.value = null;
        suggestions.value = [];
        close();
    };

    const close = () => {
        loading.value = false;
        suggesting.value = false;
        input.value.blur();
    };

    const fitBounds = (result) => {
        console.log(result);
        emit('fitBounds', result.bounds);
    };

    const closeSearch = () => {
        clear();
        closePanel();
    };
</script>
