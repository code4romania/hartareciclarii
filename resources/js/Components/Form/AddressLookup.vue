<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <template #default="{ invalid }">
            <AutoComplete
                v-model="modelValue"
                class="w-full"
                :placeholder="$t('add_point.type.exact_address_placeholder')"
                :suggestions="suggestions"
                @complete="complete"
                :invalid="invalid"
                optionLabel="name"
                fluid
            >
                <template #option="slotProps">
                    <div class="flex w-full gap-2 text-sm text-left">
                        <MapPinIcon class="w-5 h-5 fill-gray-400" />

                        <span class="flex-1 truncate" v-text="slotProps.option.name" />
                    </div>
                </template>
            </AutoComplete>
        </template>
    </FormField>
</template>

<script setup>
    import axios from 'axios';
    import { computed, ref } from 'vue';
    import { MapPinIcon } from '@heroicons/vue/16/solid';
    import { useDebounceFn } from '@vueuse/core';
    import route from '@/Helpers/useRoute.js';

    import AutoComplete from 'primevue/autocomplete';
    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: null,
        },
        help: {
            type: String,
            default: null,
        },
        options: {
            type: Array,
            default: () => [],
        },
        optionValueKey: {
            type: String,
            default: 'value',
        },
        optionLabelKey: {
            type: String,
            default: 'label',
        },
        modelValue: {
            type: String,
            default: null,
        },
        complete: {
            type: Function,
            default: () => {},
        },
        errors: {
            type: Array,
            default: () => [],
        },
    });

    const emit = defineEmits(['update:modelValue', 'update:city', 'update:point']);

    const modelValue = computed({
        get: () => props.modelValue,
        set: (address) => {
            console.log(address);
            if (typeof address === 'string') {
                return emit('update:modelValue', address);
            }

            emit('update:modelValue', address.name);
            emit('update:city', { city: address.city, county: address.county });
            emit('update:point', { lat: address.center[0], lng: address.center[1] });
        },
    });

    const suggestions = ref([]);

    const complete = useDebounceFn(() => {
        axios
            .get(
                route('front.map.suggest', {
                    query: modelValue.value,
                    type: 'location',
                })
            )
            .then((response) => {
                if (Array.isArray(response.data)) {
                    suggestions.value = response.data;
                }
            });
    }, 500);
</script>
