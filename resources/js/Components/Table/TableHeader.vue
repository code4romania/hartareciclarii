<template>
    <thead class="bg-gray-50">
        <tr>
            <th
                v-for="({ key, label }, index) in columns"
                :key="`column-${key}`"
                class="py-3.5 px-3 text-left text-sm font-semibold text-gray-900"
                :class="{
                    'pl-4': index === 0,
                    'pr-3 sm:pr-6': index !== columns.length - 1,
                }"
                v-text="label"
            />
        </tr>
    </thead>
</template>

<script setup>
    const props = defineProps({
        columns: {
            type: Array,
            required: true,
        },
    });
</script>
