<template>
    <div class="absolute z-10 w-full sm:w-80 md:w-96 inset-x-3 lg:left-80 top-14 lg:right-auto bottom-3">
        <article
            class="max-h-full overflow-x-hidden overflow-y-auto text-sm bg-white border border-gray-300 divide-y divide-gray-200 shadow rounded-2xl"
        >
            <header class="sticky top-0 flex items-start gap-4 px-6 py-4 bg-white ring-1 ring-gray-200">
                <Icon :icon="`services/${point.service}`" class="w-8 h-8 shrink-0" />

                <h1
                    class="flex-1 text-2xl font-bold leading-snug break-words text-neutral-900 whitespace-break-spaces"
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
                    <div class="inline-flex items-center gap-1">
                        <Icon
                            :icon="
                                {
                                    'check-circle': CheckCircleIcon,
                                    'question-mark-circle': QuestionMarkCircleIcon,
                                    'exclamation-triangle': ExclamationTriangleIcon,
                                }[point.status.icon]
                            "
                            class="w-4 h-4 shrink-0"
                            :class="{
                                'text-green-500': point.status.color === 'success',
                                'text-amber-500': point.status.color === 'warning',
                                'text-red-500': point.status.color === 'danger',
                            }"
                        />

                        <span
                            class="text-sm font-medium"
                            :class="{
                                'text-green-900': point.status.color === 'success',
                                'text-yellow-900': point.status.color === 'warning',
                                'text-red-900': point.status.color === 'danger',
                            }"
                            v-text="point.status.label"
                        />
                    </div>

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

            <div class="px-6 py-4">
                <h2 class="font-medium">Materiale colectate</h2>

                <div class="mt-4 grid gap-0.5">
                    <Accordion v-for="(category, index) in point.materials" :key="`category-${index}`">
                        <template #icon>
                            <img :src="category.icon" alt="" />
                        </template>

                        <template #title>
                            {{ category.name }}
                        </template>

                        <ul class="divide-y divide-gray-200">
                            <li
                                v-for="(material, index) in category.materials"
                                :key="`material-${index}`"
                                class="py-1 pl-12"
                            >
                                <Link
                                    :href="route('material', material.id)"
                                    :data="urlParams"
                                    class="text-blue-500 hover:underline focus:underline"
                                >
                                    {{ material.name }}
                                </Link>
                            </li>
                        </ul>
                    </Accordion>
                </div>
            </div>

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
    import { computed } from 'vue';
    import { router, Link } from '@inertiajs/vue3';
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
    import {
        CheckIcon,
        XMarkIcon as XIcon,
        ExclamationTriangleIcon,
        QuestionMarkCircleIcon,
        CheckCircleIcon,
    } from '@heroicons/vue/16/solid';
    import { useClipboard } from '@vueuse/core';
    import Accordion from '@/Components/Accordion.vue';
    import Icon from '@/Components/Icon.vue';

    const props = defineProps({
        point: {
            type: Object,
        },
    });

    const { text, copy, copied } = useClipboard({ legacy: true });

    const urlParams = computed(() => {
        let params = new URLSearchParams(window.location.search);

        return {
            bounds: params.get('bounds'),
            center: params.get('center'),
        };
    });

    const close = () => {
        router.visit('/', {
            data: urlParams.value,
        });
    };
</script>

