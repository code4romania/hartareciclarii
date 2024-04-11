<template>
    <form
        class="absolute flex items-center self-stretch flex-1 gap-2 overflow-hidden bg-white border-b border-gray-200 rounded-full shadow shrink-0 px-3.5"
        action="#"
        method="GET"
    >
        <label for="search-field" class="sr-only">Search</label>

        <MagnifyingGlassIcon class="w-5 h-full text-gray-400 pointer-events-none" aria-hidden="true" />

        <input
            id="search-field"
            class="block w-full h-full py-2.5 px-0 text-gray-900 border-0 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
            placeholder="Search..."
            v-model="search"
            type="search"
            name="search"
        />
    </form>

</template>

<script setup>
    import { MagnifyingGlassIcon,  } from '@heroicons/vue/24/solid';
    import {computed, ref, watch} from "vue";
    import {router, useForm, usePage} from "@inertiajs/vue3";

    const search = ref('')
    const loading = ref(false)
    const searchResult = computed(() => usePage().props.search_results || []);


    watch(search,(value,oldValue) => {
        loading.value = true

        router.reload({
            data: {
                search: value
            },
            only: ['points','search_results']
        })
    })
</script>
