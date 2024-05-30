<template>
    <div>

        <Combobox
            v-model="selectedOption"
            as="div"
            @update:modelValue="query = ''"
            @change="$emit('update:modelValue', selectedOption )"
        >
            <ComboboxLabel class="block text-sm font-semibold leading-6 text-gray-900">{{ label }}</ComboboxLabel>
            <div class="relative">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400"
                                     aria-hidden="true"/>
                <ComboboxInput
                    class="h-12 w-full border-1 rounded-2xl bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                    :placeholder="$t('add_point.first_step.service_type_placeholder')"
                    :displayValue="(option) => option? option.label : ''"
                    :id="id"
                    @change="query = $event.target.value"
                />
            </div>

            <ComboboxOptions v-if="filterOptions.length > 0" static
                             class="max-h-72 scroll-py-2 overflow-y-auto py-2 text-sm text-gray-800">
                <ComboboxOption v-for="option in filterOptions" :key="option.value" :value="option" as="template"
                                v-slot="{ active }">
                    <li :class="['cursor-default select-none px-4 py-2', active && 'bg-indigo-600 text-white']">
                        {{ option.label }}
                    </li>
                </ComboboxOption>
            </ComboboxOptions>

            <p v-if="query !== '' && filterOptions.length === 0" class="p-4 text-sm text-gray-500"
               v-text="$t('no-results')"/>
        </Combobox>
    </div>

</template>

<script setup>
import {computed, ref} from 'vue'
import {MagnifyingGlassIcon} from '@heroicons/vue/20/solid'
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from '@headlessui/vue'


const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    id: {
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

})
const query = ref('')
const filterOptions = computed(() =>
    query.value === ''
        ? []
        : props.options.filter((option) => {
            return option.label.toLowerCase().includes(query.value.toLowerCase())
        })
)
const selectedOption = ref(null)


</script>
