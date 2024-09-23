<template>
    <tr>
        <td
            v-for="({ key, highlight }, index) in columns"
            :key="`column-${key}`"
            class="px-3 py-4 text-sm whitespace-nowrap"
            :class="{
                'pl-4 ': index === 0,
                'pr-3 sm:pr-6 text-right': index === columns.length - 1,
                'font-medium text-gray-900': highlight,
                'text-gray-500': !highlight,
            }"
        >
            <slot :name="key" :[key]="row[key]" :row="row">
                {{ row[key] }}
            </slot>
        </td>
    </tr>
</template>

<script setup>
    const props = defineProps({
        row: {
            type: Object,
            required: true,
        },
        columns: {
            type: Array,
            required: true,
        },
    });
</script>
