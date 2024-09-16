<template>
    <AuthLayout :title="$t('auth.reset_password')">
        <template #backdrop>
            <h1>Devino membru Harta Reciclării</h1>

            <ul>
                <li>Adaugă noi puncte pe hartă</li>
                <li>Raportează probleme cu punctele existente</li>
                <li>Contribuie la efortul de a menține informațiile actualizate</li>
                <li>Urmărește statusul raportărilor tale</li>
                <li>
                    Alătură-te unei comunități de oameni care vor să trăiască într-un mediu mai verde și mai sustenabil
                </li>
            </ul>
        </template>

        <form class="grid gap-6" @submit.prevent="submit">
            <Input
                name="email"
                type="email"
                :label="$t('auth.register.email')"
                v-model="form.email"
                :errors="[form.errors.email]"
                readonly
            />

            <Input
                name="password"
                type="password"
                :label="$t('auth.register.password')"
                v-model="form.password"
                :errors="[form.errors.password]"
                required
            />

            <Input
                name="password_confirmation"
                type="password"
                :label="$t('auth.register.password_confirmation')"
                v-model="form.password_confirmation"
                :errors="[form.errors.password_confirmation]"
                required
            />

            <Button type="submit" class="w-full" :disabled="form.processing" primary>
                {{ $t('auth.reset_password') }}
            </Button>

            <p class="text-sm">
                <Link
                    :href="route('auth.login')"
                    class="inline-flex items-center gap-1 font-medium text-primary-800 hover:underline"
                >
                    <ArrowLongLeftIcon class="inline-block w-4 h-4" />
                    <span v-text="$t('auth.login')" />
                </Link>
            </p>
        </form>
    </AuthLayout>
</template>

<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue';
    import { useForm, Link } from '@inertiajs/vue3';
    import { ArrowLongLeftIcon } from '@heroicons/vue/20/solid';
    import route from '@/Helpers/useRoute';
    import Button from '@/Components/Button.vue';
    import Input from '@/Components/Form/Input.vue';

    const props = defineProps({
        token: {
            type: String,
            default: null,
        },
        email: {
            type: String,
            default: null,
        },
    });

    const form = useForm({
        email: props.email,
        token: props.token,
        password: null,
        password_confirmation: null,
    });

    const submit = () => {
        form.post(route('auth.password.store'), {
            onSuccess: () => form.reset(),
        });
    };
</script>
