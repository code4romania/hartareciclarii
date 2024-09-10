<template>
    <Panel as="form" @submit.prevent="submit">
        <div class="grid gap-6">
            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="current_password"
                    type="password"
                    :label="$t('auth.register.current_password')"
                    v-model="form.current_password"
                    :errors="[form.errors.current_password]"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="password"
                    type="password"
                    :label="$t('auth.register.password')"
                    v-model="form.password"
                    :errors="[form.errors.password]"
                    required
                />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <Input
                    name="password_confirmation"
                    type="password"
                    :label="$t('auth.register.password_confirmation')"
                    v-model="form.password_confirmation"
                    :errors="[form.errors.password_confirmation]"
                    required
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
        current_password: null,
        password: null,
        password_confirmation: null,
    });

    const submit = () => {
        form.post(route('front.account.password'), {
            onSuccess: () => form.reset(),
        });
    };
</script>
