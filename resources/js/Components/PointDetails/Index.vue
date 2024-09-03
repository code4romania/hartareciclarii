<template>
    <article
        class="relative max-h-full overflow-x-hidden overflow-y-auto text-sm bg-white border border-gray-300 shadow pointer-events-auto rounded-2xl"
    >
        <Heading :service="point.service" :name="point.name" @close="closePanel" />

        <Subheading :subheading="point.subheading" :status="point.status" />

        <Actions :point="point" />

        <Address :address="point.address" />

        <Materials v-if="point.materials" :materials="point.materials" />

        <Schedule v-if="point.schedule" :schedule="point.schedule" class="px-6 py-4" />

        <Observations :observations="point.observations" :info="point.info" class="px-6 py-4" />

        <div class="py-2">
            <Website v-if="point.website" :website="point.website" class="px-6 py-2" />

            <Email v-if="point.email" :email="point.email" class="px-6 py-2" />

            <Phone v-if="point.phone" :phone="point.phone" class="px-6 py-2" />
        </div>

        <Footer />
    </article>
</template>

<script setup>
    import Actions from '@/Components/PointDetails/Actions.vue';
    import Address from '@/Components/PointDetails/Address.vue';
    import Email from '@/Components/PointDetails/Email.vue';
    import Footer from '@/Components/PointDetails/Footer.vue';
    import Heading from '@/Components/PointDetails/Heading.vue';
    import Materials from '@/Components/PointDetails/Materials.vue';
    import Observations from '@/Components/PointDetails/Observations.vue';
    import Phone from '@/Components/PointDetails/Phone.vue';
    import Schedule from '@/Components/PointDetails/Schedule.vue';
    import Subheading from '@/Components/PointDetails/Subheading.vue';
    import Website from '@/Components/PointDetails/Website.vue';

    import { computed } from 'vue';
    import { closePanel } from '@/Helpers/useMap.js';

    const props = defineProps({
        point: {
            type: Object,
        },
    });
</script>

