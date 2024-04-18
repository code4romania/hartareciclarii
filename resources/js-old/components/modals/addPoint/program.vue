<template>
    <template v-for="(dayObject, day) of selectedDay">
        <div class="mb-3 flex flex-row mt-3">
            <span style="width: 25%; margin-top: 10px;">
                {{ daysSelectValues[day] }}
            </span>

            <template v-if="dayObject.unknown">
                <input
                    style="width: 64%"
                    :disabled="true"
                    id="administration"
                    :placeholder="CONSTANTS.LABELS.ADD_POINT.SECOND_STEP.CLOSED"
                    class="block w-full rounded-md border border-gray-300 py-1.5 text-neutral-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    name="administration"
                />
            </template>

            <template v-else>
                <Listbox
                    style="width: 30%"
                    as="div"
                    v-model="dayObject.startHour"
                >
                    <div class="relative mt-2">
                        <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <span class="block truncate">{{ dayObject.startHour}}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </span>
                        </ListboxButton>

                        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                <ListboxOption
                                    as="template"
                                    v-for="hour in hoursSelectValues"
                                    :key="hour"
                                    :value="hour"
                                    v-slot="{ active, selected }"
                                >
                                    <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                    <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                        {{ hour }}
                                    </span>

                                        <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                        <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                    </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>

                <span style="margin-top: 10px; margin-left: 5px; margin-right: 5px">
                -
                </span>

                <Listbox
                    style="width: 30%"
                    as="div"
                    v-model="dayObject.endHour"
                >
                    <div class="relative mt-2">
                        <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <span class="block truncate">{{ dayObject.endHour}}</span>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </span>
                        </ListboxButton>

                        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <ListboxOptions class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                <ListboxOption
                                    as="template"
                                    v-for="hour in hoursSelectValues"
                                    :key="hour"
                                    :value="hour"
                                    v-slot="{ active, selected }"
                                >
                                    <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                    <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                        {{ hour }}
                                    </span>

                                        <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                        <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                    </span>
                                    </li>
                                </ListboxOption>
                            </ListboxOptions>
                        </transition>
                    </div>
                </Listbox>
            </template>
            <div class="flex h-6 items-center">
                <input
                    style="margin-top: 27px; margin-left: 15px"
                    @change="programUnknownChanged(day)"
                    id="unknownProgram"
                    aria-describedby="unknownProgram-description"
                    name="unknownProgram"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                />
            </div>
        </div>
    </template>
</template>

<script>
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import {CONSTANTS} from "@/constants";
import {CheckIcon, ChevronUpDownIcon} from '@heroicons/vue/20/solid'
import eventBus from "@/eventBus.js";

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxLabel,
        ListboxOption,
        ListboxOptions,
        CheckIcon,
        ChevronUpDownIcon
    },
    name: "program",
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        },
    },
    data() {
        return {
            selectedDay: {
                monday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                tuesday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                wednesday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                thursday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                friday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                saturday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
                sunday: {
                    startHour: '09:00',
                    endHour: '18:00'
                },
            },
            daysSelectValues: {
                monday: 'Luni',
                tuesday: 'Marti',
                wednesday: 'Miercuri',
                thursday: 'Joi',
                friday: 'Vineri',
                saturday: 'Sambata',
                sunday: 'Duminica',
            },
            hoursSelectValues: ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'],
        };
    },
    mounted() {
       this.$emit('programChanged', this.selectedDay);
    },
    methods: {
        programUnknownChanged(dayKey) {
            for (const key in this.selectedDay) {
                if (key === dayKey) {
                    if (this.selectedDay[key].unknown) {
                        this.selectedDay[key] = {
                            startHour: '09:00',
                            endHour: '18:00'
                        }
                        return;
                    }
                    this.selectedDay[key] = {
                        unknown: true
                    }
                }
            }
        },
    },
    watch: {
        selectedDay: {
            handler: function (newVal) {
                this.$emit('programChanged', this.selectedDay);
            },
            deep: true,
        }
    },
}
</script>
