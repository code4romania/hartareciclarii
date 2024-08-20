<template>
    <div class="absolute z-10 w-full sm:w-80 md:w-96 inset-x-3 lg:left-80 top-14 lg:right-auto bottom-3">
        <article
            class="max-h-full overflow-x-hidden overflow-y-auto text-sm bg-white border border-gray-300 divide-y divide-gray-200 shadow rounded-2xl"
        >
            <header class="sticky top-0 flex items-start gap-4 px-6 py-4 bg-white ring-1 ring-gray-200">
                <div class="w-8 h-8 shrink-0">icon</div>

                <h1
                    class="flex-1 text-2xl font-bold break-words text-neutral-900 whitespace-break-spaces"
                    v-text="point.name"
                />

                <button type="button" class="text-gray-400 hover:text-gray-500 shrink-0" @click="close">
                    <XMarkIcon class="w-6 h-6" aria-hidden="true" />
                </button>
            </header>

            <header class="p-6">
                <div>
                    <p class="text-sm" v-text="point.subheading" />
                </div>

                <div class="mt-4">
                    <p class="text-sm" v-text="point.status" />
                </div>
            </header>

            <div class="px-6 py-4">actions</div>

            <div class="flex items-start gap-2 px-6 py-4">
                <MapPinIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <address class="flex-1 text-sm not-italic">{{ point.address }}</address>

                <button type="button" @click="copy(point.address)">
                    <ClipboardDocumentIcon
                        v-if="!copied || text !== point.address"
                        class="w-5 h-5 text-gray-400 shrink-0"
                    />

                    <ClipboardDocumentCheckIcon v-else class="w-5 h-5 text-green-500 shrink-0" />
                </button>
            </div>

            <div class="px-6 py-4">materials</div>

            <div v-if="point.schedule" class="flex items-start gap-2 px-6 py-4">
                <ClockIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <div class="flex-1">{{ point.schedule }}</div>
            </div>

            <div class="flex items-start gap-2 px-6 py-4">
                <ChatBubbleOvalLeftIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <div class="flex-1">
                    <p>Observații</p>
                    <p>{{ point.observations }}</p>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <span v-for="(value, key) in point.offers" :key="key" class="inline-flex items-center gap-1">
                            <CheckIcon v-if="value" class="w-4 h-4" />
                            <XIcon v-else class="w-4 h-4" />
                            {{ key }}
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="point.website" class="flex items-start gap-2 px-6 py-4">
                <GlobeEuropeAfricaIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <a
                    :href="point.website"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:underline focus:underline"
                >
                    {{ point.website }}
                </a>
            </div>

            <div v-if="point.email" class="flex items-start gap-2 px-6 py-4">
                <EnvelopeIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <a :href="`mailto:${point.email}`" class="hover:underline focus:underline">
                    {{ point.email }}
                </a>
            </div>

            <div v-if="point.phone" class="flex items-start gap-2 px-6 py-4">
                <PhoneIcon class="w-5 h-5 text-gray-400 shrink-0" />

                <a :href="`tel:${point.phone}`" class="hover:underline focus:underline">
                    {{ point.phone }}
                </a>
            </div>

            <!-- {{ point }} -->
        </article>
    </div>
</template>

<script setup>
    import { router } from '@inertiajs/vue3';
    import {
        XMarkIcon,
        MapPinIcon,
        ClipboardDocumentCheckIcon,
        ClockIcon,
        ChatBubbleOvalLeftIcon,
        GlobeEuropeAfricaIcon,
        EnvelopeIcon,
        PhoneIcon,
    } from '@heroicons/vue/24/solid';
    import { ClipboardDocumentIcon } from '@heroicons/vue/24/outline';
    import { CheckIcon, XMarkIcon as XIcon } from '@heroicons/vue/16/solid';
    import { useClipboard } from '@vueuse/core';

    const props = defineProps({
        point: {
            type: Object,
        },
    });

    const { text, copy, copied } = useClipboard({ legacy: true });

    const close = () => {
        router.visit('/', {
            data: {
                bounds: new URLSearchParams(window.location.search).get('bounds'),
                center: new URLSearchParams(window.location.search).get('center'),
            },
        });
    };
</script>

