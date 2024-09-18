<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <ul class="grid gap-0.5 w-full">
            <Accordion v-for="(category, index) in materials" :key="`category-${index}`" as="ul">
                <template #icon>
                    <img :src="category.icon" alt="" />
                </template>

                <template #title>
                    {{ category.label }}
                </template>

                <ul class="divide-y divide-gray-200">
                    <li v-for="(material, index) in category.children" :key="`material-${index}`" class="pl-12">
                        <label class="relative flex py-1 gap-x-2">
                            <input
                                type="checkbox"
                                class="absolute top-0 left-0 z-10 w-full h-full p-0 m-0 border border-gray-300 rounded outline-none opacity-0 appearance-none cursor-pointer peer disabled:cursor-default"
                                v-model="modelValue"
                                :value="material.key"
                                :name="name"
                                :disabled="material?.disabled"
                            />

                            <div
                                class="flex items-center justify-center w-4 h-4 my-0.5 border border-gray-300 bg-white text-white rounded peer-checked:border-transparent peer-disabled:bg-gray-400 peer-disabled:select-none peer-disabled:pointer-events-none peer-disabled:cursor-default peer-focus-visible:z-10 peer-focus-visible:outline-none peer-focus-visible:outline-offset-0 peer-focus-visible:ring-1 peer-focus-visible:ring-offset-2 peer-focus-visible:ring-primary-500"
                                :class="{
                                    'peer-checked:ring-red-700 peer-checked:bg-red-700 peer-focus-visible:ring-red-500':
                                        type === 'remove',
                                    'peer-checked:ring-primary-700 peer-checked:bg-primary-700 peer-focus-visible:ring-primary-500':
                                        type === 'add',
                                }"
                            >
                                <component v-if="checked(material)" :is="type === 'add' ? CheckIcon : XMarkIcon" />
                            </div>

                            <span
                                class="flex-1 text-sm font-medium text-gray-700"
                                :class="{ 'line-through': checked(material) && type === 'remove' }"
                                v-text="material.label"
                            />

                            <slot name="help" :checked="checked(material)" :disabled="material?.disabled" />
                        </label>
                    </li>
                </ul>
            </Accordion>
        </ul>
    </FormField>
</template>

<script setup>
    import { computed } from 'vue';
    import { CheckIcon, XMarkIcon } from '@heroicons/vue/16/solid';

    import Accordion from '@/Components/Accordion.vue';
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
            type: Array,
            default: [],
        },
        errors: {
            type: Array,
            default: () => [],
        },
        materials: {
            type: Array,
            default: () => [],
        },
        type: {
            type: String,
            default: 'add',
            validator: (value) => ['add', 'remove'].includes(value),
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const getOption = (option, key) => (option.hasOwnProperty(key) ? option[key] : option);

    const options = computed(() =>
        props.options.map((option) => ({
            value: getOption(option, props.optionValueKey),
            label: getOption(option, props.optionLabelKey),
        }))
    );

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => {
            emit('update:modelValue', value);
        },
    });

    const checked = (material) => material.checked === true || modelValue.value.includes(material.key);
</script>
