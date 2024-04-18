<template>
    <div class="absolute w-full sm:w-80">
        <form
            class="relative z-10 flex items-center self-stretch flex-1 gap-2 overflow-hidden bg-white border-b border-gray-200 rounded-full shadow shrink-0 px-3.5"
            @submit.prevent="runQuery"
        >
            <label for="search-field" class="sr-only">Search</label>

            <MagnifyingGlassIcon class="w-5 h-full text-gray-400 pointer-events-none shrink-0" aria-hidden="true" />

            <input
                id="search-field"
                class="block flex-1 h-full py-2.5 px-0 text-gray-900 border-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                placeholder="Search..."
                v-model="query"
                type="search"
                name="search"
            />
        </form>

        <ol class="relative w-full pt-10 -mt-10 overflow-hidden bg-white border border-gray-300 shadow rounded-2xl">
            <template v-for="(search, index) in searchResults">
                <li v-for="item in search" :key="item">
                    <Link
                        v-if="index==='points'"
                        :href="`/point/${item.id}`"
                        @click="$emit('goToPoint', item)"
                        class="flex w-full gap-2 px-4 py-2 text-sm hover:bg-gray-100"
                    >
                        <MapPinIcon class="w-5 h-5 fill-gray-400" />
                        <span class="flex-1 truncate" v-text="item.name" />
                    </Link>
                    <Link
                        v-if="index==='materials'"
                        :href="`/material/${item.id}`"
                        @click="$emit('goToPoint', item)"
                        class="flex w-full gap-2 px-4 py-2 text-sm hover:bg-gray-100"
                    >
                        <MapPinIcon class="w-5 h-5 fill-gray-400" />
                        <span class="flex-1 truncate" v-text="item.name" />
                    </Link>
                    <Link
                        v-if="index==='nominatim'"
                        @click="$emit('goToPoint', item)"
                        class="flex w-full gap-2 px-4 py-2 text-sm hover:bg-gray-100"
                    >
                        <MapPinIcon class="w-5 h-5 fill-gray-400" />
                        <span class="flex-1 truncate" v-text="item.name" />
                    </Link>
                </li>
            </template>
        </ol>
    </div>
</template>

<script setup>
    import { MagnifyingGlassIcon, MapPinIcon } from '@heroicons/vue/24/solid';
    import { computed, ref, watch } from 'vue';
    import { router, usePage, Link } from '@inertiajs/vue3';
    import { useDebounceFn } from '@vueuse/core';

    const query = ref(null);

    const runQuery = useDebounceFn(
        () => {
            router.reload({
                data: {
                    search: query.value,
                },
                only: ['points', 'search_results'],
            });
        },
        250,
        { maxWait: 1000 }
    );

    const searchResults = computed(() => usePage().props.search_results || []);

    const emit = defineEmits(['goToPoint']);

    watch(query, runQuery);

    const goToPoint = (item) => {
        console.log(item);
        emit('goToPoint', item);
    };
</script>
