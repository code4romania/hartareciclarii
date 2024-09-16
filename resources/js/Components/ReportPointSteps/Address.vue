<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="problemType.name" />

        <p class="text-sm" v-text="$t('report.address.wrong_help')" />

        <AddressLookup
            name="address"
            :label="$t('report.address.exact_address')"
            :errors="[form.errors.address]"
            v-model="form.address"
            @update:point="updatePoint"
            @update:city="updateCity"
            required
        />

        <UseGeolocation class="flex justify-end" @located="located" />

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
    import AddressLookup from '@/Components/Form/AddressLookup.vue';
    import UseGeolocation from '@/Components/UseGeolocation.vue';
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
</script>
