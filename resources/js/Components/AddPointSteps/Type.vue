<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.type.subtitle')" />

        <Select
            name="service_type"
            :label="$t('add_point.type.service_type')"
            :placeholder="$t('add_point.type.service_type_placeholder')"
            v-model="form.service_type_id"
            :errors="[form.errors.service_type_id]"
            :options="serviceTypes"
            required
        />

        <AddressLookup
            name="address"
            :label="$t('add_point.type.exact_address')"
            :errors="[form.errors.address]"
            v-model="form.address"
            @update:point="updatePoint"
            @update:city="updateCity"
            required
        />

        <UseGeolocation class="flex justify-end" @located="located" />

        <MapPreview :form="form" editable @ready="ready">
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
    import { reverse } from '@/Helpers/useReverse.js';

    import Button from '@/Components/Button.vue';
    import AddressLookup from '@/Components/Form/AddressLookup.vue';
    import MapPreview from '@/Components/Form/MapPreview.vue';
    import Select from '@/Components/Form/Select.vue';
    import UseGeolocation from '@/Components/UseGeolocation.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        serviceTypes: {
            type: Array,
            required: true,
        },
    });

    const emit = defineEmits(['changePinLocation']);

    const located = async ({ lat, lng }) => {
        updatePoint({ lat, lng });

        const response = await reverse({ lat, lng });

        props.form.address = response.name;
        props.form.city = response.city;
        props.form.county = response.county;
    };

    const updateCity = ({ city, county }) => {
        props.form.defaults({ city, county });

        props.form.reset('city', 'county');
    };

    const updatePoint = ({ lat, lng }) => {
        props.form.location.lat = lat;
        props.form.location.lng = lng;
    };

    const ready = (leafletObject) => {
        updatePoint(leafletObject.getCenter());
    };
</script>
