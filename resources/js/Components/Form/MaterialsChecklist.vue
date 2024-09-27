<template>
    <FormField :name="name" :label="label" :help="help" :required="required" :disabled="disabled" :errors="errors">
        <template v-if="clearable && modelValue?.length" #action>
            <button @click="modelValue = []" type="button" class="text-red-600" v-text="$t('action.clear')" />
        </template>

        <template #default="{ invalid }">
            <InputText
                v-if="searchable"
                type="search"
                v-model="query"
                class="w-full mb-4"
                :placeholder="$t('field.material.search_placeholder')"
                @keydown.stop
                @keydown.enter.prevent
            />

            <ul
                v-if="results.length"
                class="grid w-full"
                :class="{
                    'mt-4': !searchable,
                    'gap-0.5': !simple,
                    'gap-4': simple,
                }"
            >
                <Accordion v-for="category in results" :key="`category-${category.id}`" as="ul" :simple="simple">
                    <template #icon>
                        <img :src="category.icon" alt="" />
                    </template>

                    <template #title>
                        {{ category.name }}
                    </template>

                    <ul :class="{ 'divide-y divide-gray-200': !simple }">
                        <li
                            v-for="({ item }, index) in category.materials"
                            :key="`material-${index}`"
                            :class="{
                                'pl-6': simple,
                                'pl-12': !simple,
                            }"
                        >
                            <label class="relative flex py-1 gap-x-2">
                                <input
                                    :ref="refs.set"
                                    type="checkbox"
                                    class="absolute top-0 left-0 z-10 w-full h-full p-0 m-0 border border-gray-300 rounded outline-none opacity-0 appearance-none cursor-pointer peer disabled:cursor-default"
                                    v-model="modelValue"
                                    :value="item.id"
                                    :name="name"
                                    :disabled="item?.disabled"
                                />

                                <div
                                    class="flex items-center justify-center w-4 h-4 my-0.5 border border-gray-300 bg-white text-white rounded peer-checked:border-transparent peer-disabled:bg-gray-400 peer-disabled:select-none peer-disabled:pointer-events-none peer-disabled:cursor-default peer-focus-visible:z-10 peer-focus-visible:outline-none peer-focus-visible:outline-offset-0 peer-focus-visible:ring-1 peer-focus-visible:ring-offset-2"
                                    :class="{
                                        'peer-checked:ring-red-700 peer-checked:bg-red-700 peer-focus-visible:ring-red-500':
                                            remove,
                                        'peer-checked:ring-primary-700 peer-checked:bg-primary-700 peer-focus-visible:ring-primary-500':
                                            !remove,
                                        'border-red-500': invalid,
                                    }"
                                >
                                    <component :is="!remove ? CheckIcon : XMarkIcon" />
                                </div>

                                <span
                                    class="flex-1 text-sm font-medium text-gray-700"
                                    :class="{ 'line-through': checked(index) && remove }"
                                    v-text="item.name"
                                />

                                <slot name="help" :checked="checked(index)" :disabled="item?.disabled" />
                            </label>
                        </li>
                    </ul>
                </Accordion>
            </ul>

            <p v-else class="flex-1 text-sm font-medium text-gray-700" v-text="$t('field.material.search_empty')" />
        </template>
    </FormField>
</template>

<script setup>
    import { computed, useTemplateRef } from 'vue';
    import { CheckIcon, XMarkIcon } from '@heroicons/vue/16/solid';
    import { useTemplateRefsList } from '@vueuse/core';

    import useMaterials from '@/Helpers/useMaterials.js';
    import Accordion from '@/Components/Accordion.vue';
    import FormField from '@/Components/Form/Field.vue';
    import InputText from 'primevue/inputtext';

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
        searchable: {
            type: Boolean,
            default: false,
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        simple: {
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
            default: null,
        },
        remove: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const modelValue = computed({
        get: () => props.modelValue,
        set: (value) => {
            emit('update:modelValue', value);
        },
    });

    const refs = useTemplateRefsList();

    const checked = (index) => refs.value[index]?.checked || false;

    const { query, results } = useMaterials(props.materials);
</script>
