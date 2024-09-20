<template>
    <slot name="trigger" :open="open" :isOpen="isOpen" />

    <TransitionRoot as="template" :show="isOpen">
        <Dialog class="relative z-50" @close="overlayDismissable && close()">
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 transition-opacity bg-black/30" />
            </TransitionChild>

            <div class="fixed inset-0">
                <div class="flex items-start justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <DialogPanel
                            class="w-full h-screen sm:py-4 md:py-8 sm:max-w-lg"
                            :as="form ? 'form' : 'div'"
                            @submit.prevent="submit"
                        >
                            <div
                                class="relative flex flex-col w-full max-h-full overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl"
                            >
                                <DialogTitle
                                    v-if="$slots.title"
                                    as="h3"
                                    class="sticky top-0 px-4 py-5 text-base font-semibold text-gray-900 sm:px-6 sm:text-lg"
                                    :class="{
                                        '!pr-10': dismissable,
                                    }"
                                >
                                    <slot name="title" />
                                </DialogTitle>

                                <button
                                    v-if="dismissable"
                                    type="button"
                                    class="absolute z-10 text-gray-400 rounded-md top-4 right-4 hover:text-gray-500 focus:outline-none focus:text-gray-900"
                                    @click="close"
                                >
                                    <span class="sr-only">Close</span>
                                    <XMarkIcon class="w-6 h-6" aria-hidden="true" />
                                </button>

                                <div
                                    class="relative flex-1 px-4 py-px overflow-y-auto sm:px-6"
                                    :class="{
                                        'pb-5': !$slots.footer,
                                        'pb-1': $slots.footer,
                                    }"
                                >
                                    <slot :close="close" />
                                </div>

                                <div
                                    v-if="$slots.footer"
                                    class="relative flex flex-col-reverse justify-end gap-4 px-4 py-5 sm:flex-row sm:px-6 shrink-0"
                                >
                                    <slot name="footer" :open="open" :close="close" />
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { XMarkIcon } from '@heroicons/vue/24/solid';

    const emit = defineEmits(['submit', 'open', 'close']);

    const props = defineProps({
        dismissable: {
            type: Boolean,
            default: true,
        },
        overlayDismissable: {
            type: Boolean,
            default: true,
        },
        form: {
            type: Boolean,
            default: false,
        },
        open: {
            type: Boolean,
            default: false,
        },
    });

    const isOpen = computed(() => props.open);

    const open = () => {
        emit('open');
    };

    const close = () => {
        emit('close');
    };

    const submit = (event) => {
        if (props.form) {
            emit('submit', event);
        }
    };
</script>
