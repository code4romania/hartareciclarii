<template>
    <button
        ref="result"
        type="button"
        class="flex flex-col gap-3 px-4 py-4 text-left"
        :class="{
            'bg-gray-100': isSelected,
            'hover:bg-gray-100 focus:bg-gray-100': !isSelected,
        }"
    >
        <div class="flex items-center gap-2">
            <Icon :icon="`services/${point.service}`" class="w-8 h-8 shrink-0" />

            <span
                class="flex-1 text-lg font-bold leading-snug break-words text-neutral-900 whitespace-break-spaces"
                v-text="point.name"
            />
        </div>

        <p class="text-sm" v-text="point.subheading" />

        <div class="flex items-start gap-2">
            <MapPinIcon class="w-5 h-5 text-gray-400 shrink-0" />

            <address class="flex-1 text-sm not-italic" v-text="point.address" />
        </div>

        <!-- <div class="flex flex-col items-start self-stretch justify-start gap-2 h-14">
            <div class="inline-flex items-start justify-start gap-1">
                <div class="pl-1 pr-2 py-0.5 bg-gray-50 rounded-full justify-start items-center gap-1 flex">
                    <div class="flex items-center justify-center w-5 h-5 px-1 py-1">
                        <div class="w-3 h-3.5 relative flex-col justify-start items-start flex">
                            <div class="relative w-5 h-5">
                                <div class="w-3 h-3.5 left-[3.86px] top-[3.35px] absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-900 text-xs font-normal font-['Inter']">Hârtie și carton</div>
                </div>
                <div class="pl-1 pr-2 py-0.5 bg-gray-50 rounded-full justify-start items-center gap-1 flex">
                    <div class="flex items-center justify-center w-5 h-5 px-1 py-1">
                        <div class="w-3 h-3.5 relative flex-col justify-start items-start flex">
                            <div class="relative w-5 h-5">
                                <div class="w-3 h-3.5 left-[3.86px] top-[3.35px] absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-900 text-xs font-normal font-['Inter']">Plastic și PET</div>
                </div>
            </div>
            <div class="inline-flex items-start justify-start gap-1">
                <div class="pl-1 pr-2 py-0.5 bg-gray-50 rounded-full justify-start items-center gap-1 flex">
                    <div class="flex items-center justify-center w-5 h-5 px-1 py-1">
                        <div class="w-3 h-3.5 relative flex-col justify-start items-start flex">
                            <div class="relative w-5 h-5">
                                <div class="w-3 h-3.5 left-[3.86px] top-[3.35px] absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-gray-900 text-xs font-normal font-['Inter']">DEEE (Electronice &...</div>
                </div>
                <div class="self-stretch px-2 py-0.5 bg-gray-50 rounded-full justify-start items-center gap-1 flex">
                    <div class="text-gray-900 text-xs font-normal font-['Inter']">+3</div>
                </div>
            </div>
        </div> -->
    </button>
</template>

<script setup>
    import { ref, computed, watch } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { MapPinIcon } from '@heroicons/vue/24/solid';
    import Icon from '@/Components/Icon.vue';

    const page = usePage();

    const props = defineProps({
        point: Object,
    });

    const result = ref(null);

    const isSelected = computed(() => page.props.point?.id === props.point.id);

    watch(isSelected, (value) => {
        if (value) {
            result.value.scrollIntoView();
        }
    });
</script>
