<template>
    <div class="absolute z-10 w-full sm:w-80 md:w-96 inset-x-3 lg:left-80 top-14 lg:right-auto bottom-3">
        <article
            class="max-h-full overflow-x-hidden overflow-y-auto text-sm bg-white border border-gray-300 divide-y divide-gray-200 shadow rounded-2xl"
        >
            <Heading :service="point.service" :name="point.name" @close="close" />

            <Subheading :subheading="point.subheading" :status="point.status" />

            <Actions :point="point" />

            <Address :address="point.address" />

            <Materials :materials="point.materials" />

            <Schedule v-if="point.schedule" :schedule="point.schedule" />

            <Observations :point="point" />

            <Website v-if="point.website" :website="point.website" :url-params="urlParams" />

            <Email v-if="point.email" :email="point.email" />

            <Phone v-if="point.phone" :phone="point.phone" />

            <!-- {{ point }} -->
        </article>
    </div>
</template>

<script setup>
    import Actions from '@/Components/PointDetails/Actions.vue';
    import Address from '@/Components/PointDetails/Address.vue';
    import Email from '@/Components/PointDetails/Email.vue';
    import Heading from '@/Components/PointDetails/Heading.vue';
    import Materials from '@/Components/PointDetails/Materials.vue';
    import Observations from '@/Components/PointDetails/Observations.vue';
    import Phone from '@/Components/PointDetails/Phone.vue';
    import Schedule from '@/Components/PointDetails/Schedule.vue';
    import Subheading from '@/Components/PointDetails/Subheading.vue';
    import Website from '@/Components/PointDetails/Website.vue';

    import { computed } from 'vue';
    import { router } from '@inertiajs/vue3';

    const props = defineProps({
        point: {
            type: Object,
        },
    });

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

