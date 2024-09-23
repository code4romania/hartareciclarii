<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="problemType.name" />

        <Input
            type="text"
            name="address"
            :label="$t('report.address.exact_address')"
            v-model="form.address"
            :errors="[form.errors.address]"
            readonly
            disabled
        />

        <MapPreview :form="form" editable>
            <template #bottomleft>
                <Button
                    size="sm"
                    :label="$t('add_point.type.change_pin_location')"
                    @click="$emit('changePinLocation')"
                />
            </template>
        </MapPreview>
    </fieldset>
</template>

<script setup>
    import Button from '@/Components/Button.vue';
    import Input from '@/Components/Form/Input.vue';
    import MapPreview from '@/Components/Form/MapPreview.vue';

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

    const emit = defineEmits(['changePinLocation']);
</script>
