<template>
    <nav v-if="hasPages" class="flex items-center justify-between px-4 border-t border-gray-200 sm:px-0">
        <div class="flex flex-1 w-0 -mt-px">
            <Link
                v-if="prevPage"
                :href="prevPage"
                class="inline-flex items-center pt-4 pr-1 text-sm font-medium text-gray-500 border-t-2 border-transparent gap-x-3 hover:text-gray-700 hover:border-gray-300"
            >
                <ArrowLongLeftIcon class="w-5 h-5 text-gray-400" />

                <span v-text="$t('pagination.previous')" />
            </Link>
        </div>

        <div class="hidden md:-mt-px md:flex">
            <template v-for="(page, index) in collection.meta.links">
                <Link
                    v-if="
                        !page.active &&
                        page.url &&
                        ![collection.links.prevPage, collection.links.nextPage].includes(page.url)
                    "
                    :key="`link-${index}`"
                    class="inline-flex items-center px-4 pt-4 text-sm font-medium text-gray-500 border-t-2 border-transparent hover:text-gray-700 hover:border-gray-300"
                    :href="page.url"
                    v-text="page.label"
                />

                <span
                    v-if="!page.active && !page.url"
                    :key="`ellipsis-${index}`"
                    class="inline-flex items-center px-4 pt-4 text-sm font-medium text-gray-500 border-t-2 border-transparent"
                    v-text="page.label"
                />

                <span
                    v-if="page.active"
                    :key="`span-${index}`"
                    aria-current="page"
                    class="inline-flex items-center px-4 pt-4 text-sm font-medium border-t-2 text-primary-600 border-primary-500"
                    v-text="page.label"
                />
            </template>
        </div>

        <div class="flex justify-end flex-1 w-0 -mt-px">
            <Link
                v-if="nextPage"
                :href="nextPage"
                class="inline-flex items-center pt-4 pl-1 text-sm font-medium text-gray-500 border-t-2 border-transparent gap-x-3 hover:text-gray-700 hover:border-gray-300"
            >
                <span v-text="$t('pagination.next')" />

                <ArrowLongRightIcon class="w-5 h-5 text-gray-400" />
            </Link>
        </div>
    </nav>
</template>

<script setup>
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import { ArrowLongLeftIcon, ArrowLongRightIcon } from '@heroicons/vue/20/solid';

    const props = defineProps({
        collection: {
            type: Object,
            required: true,
        },
        maxVisibleButtons: {
            type: Number,
            default: 3,
        },
    });

    const hasPages = computed(() => {
        if (props.collection.meta.total === 0) {
            return false;
        }

        if (props.collection.meta.total <= props.collection.meta.per_page) {
            return false;
        }

        return true;
    });

    const prevPage = computed(() => {
        if (props.collection.links.prev) {
            return props.collection.links.prev;
        }

        return false;
    });

    const nextPage = computed(() => {
        if (props.collection.links.next) {
            return props.collection.links.next;
        }

        return false;
    });

    // export default {
    //     computed: {
    //         pages() {
    //             return this.pagination(this.meta.current_page, this.meta.last_page).map((page) => {
    //                 if (page === '...') {
    //                     return {
    //                         url: null,
    //                         label: page,
    //                         active: false,
    //                     };
    //                 } else {
    //                     return {
    //                         url: this.pageUrl(page),
    //                         label: page,
    //                         active: page === this.meta.current_page,
    //                     };
    //                 }
    //             });
    //         },
    //         prevPage() {
    //             if (this.meta.current_page - 1 >= 1) {
    //                 return this.pageUrl(this.meta.current_page - 1);
    //             }

    //             return false;
    //         },
    //         nextPage() {
    //             if (this.meta.current_page + 1 <= this.meta.last_page) {
    //                 return this.pageUrl(this.meta.current_page + 1);
    //             }

    //             return false;
    //         },
    //     },
    //     methods: {
    //         pageUrl(page) {
    //             let params = new URLSearchParams(location.search);

    //             params.set('page', page);

    //             return this.meta.path + '?' + params.toString();
    //         },
    //         separate(a, b) {
    //             return [
    //                 a,
    //                 ...({
    //                     0: [],
    //                     1: [b],
    //                 }[b - a] || ['...', b]),
    //             ];
    //         },
    //         pagination(currentPage, pageCount) {
    //             return Array(this.maxVisibleButtons * 2 + 1)
    //                 .fill()
    //                 .map((_, index) => currentPage - this.maxVisibleButtons + index)
    //                 .filter((page) => 0 < page && page <= pageCount)
    //                 .flatMap((page, index, { length }) => {
    //                     if (!index) {
    //                         return this.separate(1, page);
    //                     }

    //                     if (index === length - 1) {
    //                         return this.separate(page, pageCount);
    //                     }

    //                     return [page];
    //                 });
    //         },
    //     },
    // };
</script>
