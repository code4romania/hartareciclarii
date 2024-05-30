<template>
    <div>
        <PillButton color="white" :label="$t('top_menu.add_point')" :icon="MapPinIcon" @click="open"/>
        <div>
            <TransitionRoot appear :show="addMapPoint" as="template">
                <Dialog as="div" @close="close" class="relative z-10">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                    >
                        <div class="fixed inset-0 bg-black/25"/>
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center"
                        >
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                                >
                                    <DialogTitle
                                        as="div"
                                        class="text-lg font-medium leading-6 text-gray-900 flex items-center justify-between"
                                    >
                                        <h3 v-text="$t('add_point.title')"/>
                                        <button
                                            type="button"
                                            class="text-gray-400 hover:text-gray-500"
                                            @click="close">
                                            <XMarkIcon class="w-6 h-6" aria-hidden="true"/>
                                        </button>

                                    </DialogTitle>
                                    <div class="mt-2">
                                        <form @submit="submitForm">
                                            <div v-show="currentStep===1">
                                                <div class="mt-6">
                                                    <p v-text="$t('add_point.first_step.subtitle')"/>
                                                </div>
                                                <div class="mt-8 grid gap-4 ">
                                                    <SearchList
                                                        :label="$t('add_point.first_step.service_type_label')"
                                                        id="service_type"
                                                    />
                                                    <SearchWithMap/>

                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="mt-4 flex  place-content-end  space-x-4 ">
                                        <button
                                            type="reset"
                                            class="inline-flex justify-center rounded-md border border-black bg-white px-3 py-2 text-sm text-primary-500 shadow-sm sm:col-end-2"
                                            @click="close"
                                            v-text="$t('add_point.cancel')"/>
                                        <button
                                            type="submit"
                                            v-if="currentStep!==3"
                                            class="inline-flex justify-center rounded-md border border-black bg-primary-500 px-3 py-2 text-sm text-white shadow-sm sm:col-end-2"
                                            @click="submitLogin"
                                            v-text="$t('add_point.next_step')"/>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </div>
    </div>
</template>

<script setup>
import {
    Combobox, ComboboxInput, ComboboxOption, ComboboxOptions,
    Dialog,
    DialogPanel,
    DialogTitle,
    Listbox,
    ListboxButton, ListboxOption, ListboxOptions,
    TransitionChild,
    TransitionRoot
} from '@headlessui/vue';
import {UserIcon, XMarkIcon} from '@heroicons/vue/20/solid';
import PillButton from '@/Components/Buttons/PillButton.vue';
import route from '@/Helpers/useRoute.js';

import {computed, ref} from 'vue';
import {useForm, usePage} from "@inertiajs/vue3";
import InputComponent from "@/Components/InputComponent.vue";
import CheckboxComponent from "@/Components/CheckboxComponent.vue";
import {MapPinIcon} from "@heroicons/vue/20/solid/index.js";
import SearchList from "@/Components/AddPoint/SearchList.vue";
import SearchWithMap from "@/Components/AddPoint/SearchWithMap.vue";

const addMapPoint = ref(false);

const open = () => {
    addMapPoint.value = true;
};

const close = () => {
    addMapPoint.value = false;
};

const form = useForm({
    'email': '',
    'password': '',
})

const submitLogin = () => {
    form.post(route('login'), {
        preserveScroll: true,
        onSuccess: () => {
            close();
        }

    });
}
let currentStep = ref(1);
const people = [
    'Durward Reynolds',
    'Kenton Towne',
    'Therese Wunsch',
    'Benedict Kessler',
    'Katelyn Rohan',
]
const selectedPerson = ref(people[0])
const query = ref('')

const filteredPeople = computed(() =>
    query.value === ''
        ? people
        : people.filter((person) => {
            return person.toLowerCase().includes(query.value.toLowerCase())
        })
)

const errors = computed(() => usePage().props.errors)
</script>
