<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.second_step.subtitle')" />

        <Select
            name="point_type"
            :label="$t('add_point.second_step.point_type')"
            v-model="form.point_type"
            :errors="[form.errors.point_type]"
            :options="pointTypes"
            required
        />
    </fieldset>
</template>

<script setup>
    import Select from '@/Components/Form/Select.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        pointTypes: {
            type: Array,
            required: true,
        },
    });
</script>
