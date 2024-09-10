<template>
    <Panel as="form" @submit.prevent="submit">
        <div class="grid gap-6">
            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="first_name"
                    type="text"
                    :label="$t('auth.register.first_name')"
                    v-model="form.first_name"
                    :errors="[form.errors.first_name]"
                    required
                />

                <Input
                    name="last_name"
                    type="text"
                    :label="$t('auth.register.last_name')"
                    v-model="form.last_name"
                    :errors="[form.errors.last_name]"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="email"
                    type="email"
                    :label="$t('auth.register.email')"
                    v-model="form.email"
                    :errors="[form.errors.email]"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="phone"
                    type="tel"
                    :label="$t('auth.register.phone')"
                    v-model="form.phone"
                    :errors="[form.errors.phone]"
                />
            </div>
        </div>

        <template #footer>
            <Button type="submit" :label="$t('profile.save')" size="sm" :disabled="form.processing" primary />
        </template>
    </Panel>
</template>

<script setup>
    import { useForm, usePage } from '@inertiajs/vue3';
    import route from '@/Helpers/useRoute';
    import Button from '@/Components/Button.vue';
    import Panel from '@/Components/Panel.vue';
    import Input from '@/Components/Form/Input.vue';

    const page = usePage();

    const form = useForm({
        first_name: page.props.auth.first_name,
        last_name: page.props.auth.last_name,
        email: page.props.auth.email,
        phone: page.props.auth.phone,
    });

    const submit = () => {
        form.post(route('front.account.profile'));
    };
</script>
