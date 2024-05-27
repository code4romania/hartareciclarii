<template>
    <div>
        <label class="block text-sm font-semibold leading-6 text-gray-900 mb-2" v-text="label" :for="id"/>
        <Combobox @update:modelValue="onSelect">
            <div class="relative">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400"
                                     aria-hidden="true"/>
                <ComboboxInput
                    class="h-12 w-full border-1 rounded-2xl bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                    :placeholder="$t('add_point.first_step.service_type_placeholder')"
                    :id="id"
                    @change="query = $event.target.value" @blur="query = ''"/>
            </div>

            <ComboboxOptions v-if="filteredPeople.length > 0" static
                             class="max-h-72 scroll-py-2 overflow-y-auto py-2 text-sm text-gray-800">
                <ComboboxOption v-for="person in filteredPeople" :key="person.id" :value="person" as="template"
                                v-slot="{ active }">
                    <li :class="['cursor-default select-none px-4 py-2', active && 'bg-indigo-600 text-white']">
                        {{ person.name }}
                    </li>
                </ComboboxOption>
            </ComboboxOptions>

            <p v-if="query !== '' && filteredPeople.length === 0" class="p-4 text-sm text-gray-500">No people found.</p>
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
})
const people = [
    {id: 1, name: 'Leslie Alexander', url: '#'},
    // More people...
]
const query = ref('')
const filteredPeople = computed(() =>
    query.value === ''
        ? []
        : people.filter((person) => {
            return person.name.toLowerCase().includes(query.value.toLowerCase())
        })
)

function onSelect(person) {
    window.location = person.url
}
</script>
