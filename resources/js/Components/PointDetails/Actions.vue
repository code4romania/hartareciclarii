<template>
    <div class="grid items-start justify-between grid-cols-3 gap-10 px-6 py-4 ring-1 ring-gray-200">
        <a
            :href="googleMapsUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="gap-2 text-center group focus:outline-none"
        >
            <div
                class="inline-flex items-center justify-center w-12 h-12 p-3 bg-gray-100 rounded-full text-blue-950 group-hover:bg-blue-800 group-hover:text-white group-focus:bg-blue-800 group-focus:text-white group-focus:ring-2 group-focus:ring-blue-800 ring-offset-2"
            >
                <ArrowTurnUpRightIcon class="w-full h-full" />
            </div>

            <div class="mt-2 text-xs font-medium text-sky-900" v-text="$t('point.action.goto')" />
        </a>

        <Link
            :href="route('front.map.report', { point, coordinates })"
            class="gap-2 text-center group focus:outline-none"
            :only="['report']"
        >
            <div
                class="inline-flex items-center justify-center w-12 h-12 p-3 bg-gray-100 rounded-full text-blue-950 group-hover:bg-blue-800 group-hover:text-white group-focus:bg-blue-800 group-focus:text-white group-focus:ring-2 group-focus:ring-blue-800 ring-offset-2"
            >
                <FlagIcon class="w-full h-full" />
            </div>

            <div class="mt-2 text-xs font-medium text-sky-900" v-text="$t('point.action.report')" />
        </Link>

        <button type="button" class="gap-2 text-center group focus:outline-none" @click="shareOrCopy">
            <div
                class="inline-flex items-center justify-center w-12 h-12 p-3 bg-gray-100 rounded-full text-blue-950 group-hover:bg-blue-800 group-hover:text-white group-focus:bg-blue-800 group-focus:text-white group-focus:ring-2 group-focus:ring-blue-800 ring-offset-2"
            >
                <ShareIcon v-if="!copied || text !== url" class="w-full h-full" />
                <LinkIcon v-else class="w-full h-full" />
            </div>

            <div class="mt-2 text-xs font-medium text-sky-900" v-text="$t('point.action.share')" />
        </button>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { useShare, useClipboard } from '@vueuse/core';
    import { ArrowTurnUpRightIcon, FlagIcon, ShareIcon, LinkIcon } from '@heroicons/vue/16/solid';
    import { Link } from '@inertiajs/vue3';
    import route from '@/Helpers/useRoute.js';

    const props = defineProps({
        point: {
            type: Object,
        },
    });

    const googleMapsUrl = computed(() => `https://www.google.com/maps/place/${props.point.latlng.join(',')}`);

    const { coordinates } = route().params;

    const url = computed(() => route('front.map.point', { point: props.point, coordinates }));

    const { share, isSupported: shareIsSupported } = useShare();
    const { text, copy, copied } = useClipboard({ legacy: true });

    const shareOrCopy = () => {
        if (shareIsSupported.value) {
            share({
                title: props.point.name,
                url: url.value,
            });
        } else {
            copy(url.value);
        }
    };
</script>
