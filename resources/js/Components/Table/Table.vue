<template>
    <div>
        <div
            v-if="$slots.title || $slots.description || $slots.actions"
            class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:gap-16"
        >
            <div v-if="$slots.title || $slots.description" class="flex-1">
                <h1 v-if="$slots.title" class="text-base font-bold leading-6 text-gray-900 sm:text-lg">
                    <slot name="title" />
                </h1>
                <p v-if="$slots.description" class="mt-2 text-sm text-gray-700 sm:text-base">
                    <slot name="description" />
                </p>
            </div>
            <div v-if="$slots.actions" class="flex gap-4 shrink-0">
                <slot name="actions" />
            </div>
        </div>

        <div v-if="collection.data.length > 0" class="flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden drop-shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <TableHeader :columns="collection.columns" />

                            <tbody class="bg-white divide-y divide-gray-200">
                                <TableRow
                                    v-for="(row, index) in collection.data"
                                    :key="index"
                                    :row="row"
                                    :columns="collection.columns"
                                >
                                    <template v-for="(_, name) in $slots" v-slot:[name]="slotData">
                                        <slot :name="name" v-bind="slotData" />
                                    </template>
                                </TableRow>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <Pagination :collection="collection" />
        </div>

        <slot name="empty" v-else />
    </div>
</template>

<script setup>
    import Pagination from '@/Components/Pagination.vue';
    import TableHeader from '@/Components/Table/TableHeader.vue';
    import TableRow from '@/Components/Table/TableRow.vue';

    const props = defineProps({
        collection: {
            type: Object,
            required: true,
        },
    });
</script>
