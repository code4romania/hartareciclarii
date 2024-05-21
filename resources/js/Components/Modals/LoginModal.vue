<template>
        <!-- Wrap your dialog in a `TransitionRoot` to add transitions. -->
        <TransitionRoot
            :show="show"
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
        >
            <Dialog @close="!show">
                <DialogPanel>
                    <DialogTitle>Deactivate account</DialogTitle>
                    <h2>alexa</h2>
                    <button @click="show = false">Close</button>
                </DialogPanel>
            </Dialog>
        </TransitionRoot>


</template>
<script setup>
import {Dialog, DialogPanel, DialogTitle, TransitionRoot} from "@headlessui/vue";
import {XMarkIcon} from "@heroicons/vue/24/outline/index.js";

const props = defineProps({
    show: Boolean,
});

</script>
