<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="problemType.name" />

        <CheckboxList
            name="sub_types"
            :label="$t('report.materials.type')"
            :options="problemType.children"
            v-model="form.sub_types"
            :errors="[form.errors.sub_types]"
            option-value-key="id"
            option-label-key="name"
            required
        />
    </fieldset>
</template>

<script setup>
    import CheckboxList from '@/Components/Form/CheckboxList.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        problemType: {
            type: Object,
            required: true,
        },
    });
</script>
