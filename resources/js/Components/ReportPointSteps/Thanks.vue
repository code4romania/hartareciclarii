<template>
    <div class="flex flex-col gap-8 py-8 text-center">
        <Icon icon="thanks" class="mx-auto mt-8 mb-6 w-28 h-28" />

        <h3 class="text-xl font-medium text-gray-900" v-text="$t('report.thanks.subtitle')" />

        <div class="prose">
            <p v-text="$t('report.thanks.intro')" />

            <p v-if="!$page.props.auth" v-text="$t('report.thanks.become_member')" />

            <p v-else>
                {{ $t('report.thanks.view_contributions') }}
                <Link :href="route('front.account.dashboard')" class="inline-block underline">
                    {{ $t('report.thanks.profile') }}
                </Link>
            </p>

            <div class="flex flex-col p-4 text-sm bg-gray-50 rounded-2xl">
                <p class="m-0" v-text="$t('report.thanks.contact_administrator')" />

                <p v-if="point.subheading" class="font-semibold text-left" v-text="point.subheading" />

                <Website v-if="point.website" :website="point.website" class="mt-2" />

                <Email v-if="point.email" :email="point.email" class="mt-2" />

                <Phone v-if="point.phone" :phone="point.phone" class="mt-2" />
            </div>
        </div>

        <div class="flex flex-col w-full gap-4 mx-auto max-w-60">
            <Button
                v-if="problemType.slug === 'rejected_waste'"
                :label="$t('report.thanks.guide')"
                :href="route('auth.register')"
                class="w-full"
                external
                primary
            />

            <Button
                v-if="!$page.props.auth"
                :label="$t('report.thanks.create_account')"
                :href="route('auth.register')"
                class="w-full"
                primary
            />

            <Button :label="$t('report.thanks.close')" @click="close" class="w-full" />
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    import Icon from '@/Components/Icon.vue';
    import Button from '@/Components/Button.vue';
    import Email from '@/Components/PointDetails/Email.vue';
    import Phone from '@/Components/PointDetails/Phone.vue';
    import Website from '@/Components/PointDetails/Website.vue';

    const props = defineProps({
        point: {
            type: Object,
        },
        problemType: {
            type: Object,
            required: true,
        },
        close: {
            type: Function,
            required: true,
        },
    });
</script>
