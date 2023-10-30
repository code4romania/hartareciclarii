<template>
    <TransitionRoot :show="isOpen" as="template">
        <Dialog as="div" class="relative z-10" @close="closeModal();">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                             leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
            </TransitionChild>

            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div
                        class="relative transform flex flex-col rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:w-full sm:max-w-lg sm:p-6 h-[80vh] overflow-auto">
                        <first-step
                            v-if="activeStep === 'first'"
                            :nomenclatures="nomenclatures"
                            @close="closeModal();"
                            @stepFinished="stepFinished($event)"
                        ></first-step>
                        <second-step
                            v-if="activeStep === 'second'"
                            :nomenclatures="nomenclatures"
                            :previous-step-body="requestBody"
                            @close="closeModal();"
                            @stepFinished="stepFinished($event)"
                        ></second-step>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue'
import {CheckIcon} from '@heroicons/vue/24/outline'
import {CONSTANTS} from "@/constants";
import DesktopFilterCloseIcon from "../../svg-icons/desktopFilterCloseIcon.vue";
import firstStep from "./firstStep.vue";
import secondStep from "./secondStep.vue";
import axios from "axios";
import _ from "lodash";

export default {
    components: {
        firstStep,
        secondStep,
        DesktopFilterCloseIcon,
        Dialog,
        DialogPanel,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        CheckIcon
    },
    props: {
        isOpen: {
            type: Boolean,
            required: true,
            default: false
        },
        isAuthenticated: {
            type: Boolean,
            required: true,
            default: false
        },
        userInfo: {
            type: Object,
            required: false
        }
    },
    computed: {
        CONSTANTS() {
            return CONSTANTS;
        }
    },
    data() {
        return {
            activeStep: 'first',
            //activeStep: 'second',
            nomenclatures: {},
            requestBody: {},
        };
    },
    mounted() {
        this.getNomenclatureValues();
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        getNomenclatureValues() {
            axios
                .get(
                    CONSTANTS.API_DOMAIN + CONSTANTS.ROUTES.STATIC.NOMENCLATURES.GET,
                )
                .then((response) => {
                    this.nomenclatures = _.get(response, 'data', {});
                })
                .catch((err) => {});
        },
        stepFinished(stepData) {
            this.activeStep = stepData.nextStep
            this.requestBody = {...this.requestBody, ...stepData.body}
        }
    }
};
</script>
