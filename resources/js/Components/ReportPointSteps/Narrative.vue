<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="legend" />

        <slot name="header" />

        <Textarea
            name="description"
            :help="$t('report.narrative.help')"
            help-position="top"
            v-model="form.description"
            :errors="[form.errors.description]"
            :required="required"
        />

        <slot name="footer" />
    </fieldset>
</template>

<script setup>
    import Textarea from '@/Components/Form/Textarea.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        legend: {
            type: String,
            required: true,
        },
        required: {
            type: Boolean,
            default: false,
        },
    });
</script>
