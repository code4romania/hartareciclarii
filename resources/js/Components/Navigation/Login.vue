<template>
    <div>
        <PillButton color="white" :label="$t('auth.login')" :icon="UserIcon" @click="open" />

        <TransitionRoot
            :show="isOpen"
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
        >
            <Dialog as="div" @close="close">
                <TransitionChild
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/30 z-[9999]" />
                </TransitionChild>

                <TransitionChild
                    enter="duration-300 ease-out"
                    enter-from="opacity-0 scale-95"
                    enter-to="opacity-100 scale-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100 scale-100"
                    leave-to="opacity-0 scale-95"
                >
                    <DialogPanel>
                        <DialogTitle>Deactivate account</DialogTitle>
                        <h2>alexa</h2>
                        <button @click="close">Close</button>
                    </DialogPanel>
                </TransitionChild>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
    import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue';
    import { UserIcon, XMarkIcon } from '@heroicons/vue/20/solid';
    import PillButton from '@/Components/Buttons/PillButton.vue';

    import { ref } from 'vue';

    const isOpen = ref(false);

    const open = () => {
        isOpen.value = true;
    };

    const close = () => {
        isOpen.value = false;
    };
</script>
