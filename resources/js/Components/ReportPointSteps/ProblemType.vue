<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="$t('report.type')" />

        <RadioGroup
            name="problem_type"
            :options="$page.props.problem_types"
            v-model="form.type_id"
            option-label-key="name"
            option-value-key="id"
            required
        />
    </fieldset>
</template>

<script setup>
    import RadioGroup from '@/Components/Form/RadioGroup.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
    });
</script>
