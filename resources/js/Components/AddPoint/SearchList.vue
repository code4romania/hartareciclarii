<template>
    <Combobox v-model="proxySelected" as="div" class="relative">
        <ComboboxLabel class="block text-sm font-semibold leading-6 text-gray-900">{{ label }}</ComboboxLabel>
        <div class="relative">
            <MagnifyingGlassIcon
                class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400"
                aria-hidden="true"
            />

            <ComboboxInput
                class="w-full h-12 pr-4 text-gray-900 bg-transparent border-1 rounded-2xl pl-11 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                :placeholder="$t('add_point.first_step.service_type_placeholder')"
                :displayValue="(option) => option.label"
                @change="query = $event.target.value"
            />

            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                <ChevronUpDownIcon class="w-5 h-5 text-gray-400" aria-hidden="true" />
            </ComboboxButton>
        </div>

        <TransitionRoot
            leave="transition ease-in duration-100"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
            @after-leave="query = ''"
        >
            <ComboboxOptions
                class="absolute z-10 w-full py-2 mt-1 overflow-y-auto text-sm text-gray-800 bg-white rounded-md shadow-lg max-h-72 scroll-py-2 ring-1 ring-black/5"
                static
            >
                <p
                    v-if="query !== '' && !filterOptions.length"
                    class="p-4 text-sm text-gray-500"
                    v-text="$t('no-results')"
                />

                <ComboboxOption
                    v-for="option in filterOptions"
                    :key="option.value"
                    :value="option"
                    as="template"
                    v-slot="{ active }"
                >
                    <li :class="['cursor-default select-none px-4 py-2', active && 'bg-indigo-600 text-white']">
                        {{ option.label }}
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </TransitionRoot>
    </Combobox>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { MagnifyingGlassIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';
    import {
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOptions,
        ComboboxOption,
        TransitionRoot,
    } from '@headlessui/vue';

    const props = defineProps({
        label: {
            type: String,
            required: true,
        },
        options: {
            type: Array,
            required: true,
        },
        modelValue: {
            type: String,
            required: true,
        },
    });

    const query = ref('');

    const filterOptions = computed(() =>
        query.value === ''
            ? props.options
            : props.options.filter((option) => {
                  return option.label.toLowerCase().includes(query.value.toLowerCase());
              })
    );

    const emit = defineEmits(['update:modelValue']);

    const proxySelected = computed({
        get: () => props.options[props.modelValue],
        set: (value) => emit('update:modelValue', value.value),
    });
</script>
