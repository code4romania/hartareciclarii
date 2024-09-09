<template>
    <div class="flex flex-col items-center gap-8 py-8 text-center">
        <Icon icon="thanks" class="mt-8 mb-6 w-28 h-28" />

        <h3 class="text-xl font-medium text-gray-900" v-text="$t('add_point.thanks.subtitle')" />

        <div class="prose">
            <p v-text="$t('add_point.thanks.intro')" />

            <p v-if="!$page.props.auth" v-text="$t('add_point.thanks.become_member')" />

            <p v-else>
                {{ $t('add_point.thanks.view_contributions') }}
                <Link href="{{ route('dashboard') }}" class="inline-block underline">
                    {{ $t('add_point.thanks.profile') }}
                </Link>
            </p>
        </div>

        <div class="flex flex-col w-full gap-4 max-w-60">
            <Button
                v-if="!$page.props.auth"
                :label="$t('add_point.thanks.create_account')"
                :href="route('auth.register')"
                class="w-full"
                primary
            />

            <Button :label="$t('add_point.thanks.close')" @click="close" class="w-full" />
        </div>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import { MapPinIcon } from '@heroicons/vue/20/solid';
    import Icon from '@/Components/Icon.vue';
    import Button from '@/Components/Button.vue';

    const props = defineProps({
        close: Function,
    });

    const page = usePage();
</script>
