<template>
    <div class="relative z-10" v-click-away="close">
        <form class="relative z-20" @submit.prevent="search">
            <MagnifyingGlassIcon
                class="absolute inset-y-2.5 w-6 h-6 pointer-events-none left-4 shrink-0"
                :class="{
                    'text-primary-600': query,
                    'text-gray-400': !query,
                }"
            />

            <input
                type="search"
                name="search"
                ref="input"
                class="block w-full rounded-full border-0 pl-12 pr-4 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-1 focus:ring-inset focus:ring-gray-300 sm:text-sm sm:leading-6 shadow"
                placeholder="Caută o adresă, un punct, sau un material"
                autocomplete="off"
                v-model="query"
                @keydown.esc.stop="clear"
                @focus="open"
            />
        </form>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition ease-in duration-50"
            leave-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="searching"
                class="absolute z-10 w-full pt-10 -mt-10 overflow-hidden text-sm bg-white border border-gray-300 shadow-lg rounded-2xl"
            >
                <ol v-if="hasResults" class="py-2">
                    <li v-for="(result, index) in results" :key="index">
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

                            <span class="flex-1 truncate" v-text="result.name" />
                        </Link>

                        <button
                            v-else
                            type="button"
                            class="flex w-full gap-2 px-4 py-2 text-sm text-left hover:bg-gray-100"
                            @click="fitBounds(result)"
                        >
                            <MapPinIcon class="w-5 h-5 fill-gray-400" />

                            <span class="flex-1 truncate" v-text="result.name" />
                        </button>
                    </li>
                </ol>

                <div v-else-if="loading" class="px-4 py-10 mx-auto text-center text-gray-400 max-w-52">loading</div>

                <div
                    v-else
                    class="px-4 py-10 mx-auto text-center text-gray-400 max-w-52"
                    v-text="$t('app.search.empty')"
                />
            </div>
        </transition>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import route from '@/Helpers/useRoute.js';
    import { MagnifyingGlassIcon, MapPinIcon } from '@heroicons/vue/16/solid';
    import { computed, ref, watch } from 'vue';
    import { router, usePage, Link } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';
    import Icon from '@/Components/Icon.vue';

    const page = usePage();

    const input = ref(null);
    const query = ref(null);
    const loading = ref(false);
    const searching = ref(false);
    const highlightedItem = ref(null);

    const results = ref([]);

    const emit = defineEmits(['fitBounds']);

    const hasResults = computed(() => {
        if (!results.value.length) {
            return false;
        }

        return true;
    });

    const search = useDebounceFn(() => {
        if (query.value === null || query.value.length < 3) {
            close();
            return;
        }

        highlightedItem.value = null;
        loading.value = true;
        searching.value = true;

        axios
            .get(
                route('suggest', {
                    center: new URLSearchParams(window.location.search).get('center'),
                    query: query.value,
                })
            )
            .then((response) => {
                results.value = response.data;
            })
            .catch((error) => {
                console.error(error);
            })
            .finally(() => {
                loading.value = false;
            });
    }, 250);

    watch(query, search);

    const open = () => {
        searching.value = true;
        // query.value = '';
        // input.value.focus();
    };

    const clear = () => {
        close();
        input.value.blur();
        query.value = null;
        results.value = [];
    };

    const close = () => {
        loading.value = false;
        searching.value = false;
    };

    const fitBounds = (result) => {
        emit('fitBounds', result.bounds);
    };
</script>
