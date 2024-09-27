<template>
    <FormField
        :name="name"
        :label="label"
        :help="help"
        helpPosition="top"
        :required="required"
        :disabled="disabled"
        :errors="errors"
    >
        <template #default>
            <div class="grid w-full gap-4 mt-4">
                <ul class="flex flex-wrap gap-4">
                    <li
                        v-for="{ uuid, url } in modelValue"
                        :key="uuid"
                        class="relative w-24 h-24 overflow-hidden rounded-lg bg-gray-50"
                    >
                        <img role="presentation" alt="" :src="url" class="object-cover w-24 h-24" />

                        <button
                            type="button"
                            @click="deleteImage(uuid)"
                            class="bg-white rounded-full p-1 absolute top-1 right-1.5 hover:bg-red-50 hover:text-red-600 focus:bg-red-50 focus:text-red-600 outline-red-600"
                        >
                            <XMarkIcon class="w-5 h-5" />
                        </button>
                    </li>

                    <template v-if="loading && files">
                        <li
                            v-for="(file, index) in files"
                            :key="index"
                            class="relative flex items-center justify-center w-24 h-24 overflow-hidden rounded-lg bg-gray-50"
                        >
                            <Icon icon="spinner" class="w-8 h-8 text-gray-200 animate-spin fill-primary-600" />
                        </li>
                    </template>
                </ul>

                <div>
                    <button type="button" @click="open" class="flex gap-2 text-sm font-semibold text-primary-900">
                        <PlusIcon class="w-5 h-5" />
                        <span v-text="chooseLabel" />
                    </button>
                </div>
            </div>
        </template>
    </FormField>
</template>

<script setup>
    import axios from 'axios';
    import { computed, ref, watch } from 'vue';
    import { PlusIcon, XMarkIcon } from '@heroicons/vue/20/solid';
    import { useFileDialog } from '@vueuse/core';
    import FormField from '@/Components/Form/Field.vue';
    import Icon from '@/Components/Icon.vue';
    import route from '@/Helpers/useRoute.js';

    const props = defineProps({
        name: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'text',
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
        placeholder: {
            type: String,
            default: null,
        },
        help: {
            type: String,
            default: null,
        },
        modelValue: {
            type: [Array],
            default: () => [],
        },
        errors: {
            type: Array,
            default: () => [],
        },
        chooseLabel: {
            type: String,
            default: 'Choose',
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const { files, open, reset, onChange } = useFileDialog({
        accept: 'image/*',
        multiple: true,
    });

    const loading = ref(false);

    onChange((files) => {
        if (!files) {
            return;
        }

        loading.value = true;

        const payload = new FormData();

        for (const file of files) {
            payload.append('images[]', file);
        }

        axios
            .post(route('front.media.upload'), payload, {
                onUploadProgress: (progress) => {},
            })
            .then((response) => {
                [...response.data].forEach((image) => {
                    modelValue.value.push(image);
                });
            })
            .finally(() => {
                loading.value = false;

                reset();
            });
    });

    const deleteImage = (uuid) => {
        axios.delete(route('front.media.delete', { media: uuid })).then(() => {
            modelValue.value = modelValue.value.filter((image) => image.uuid !== uuid);
        });
    };
</script>
