<template>
    <AuthLayout :title="$t('auth.login')">
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
                :label="$t('field.email')"
                v-model="form.email"
                :errors="[form.errors.email]"
                required
            />

            <Input
                name="password"
                type="password"
                :label="$t('field.password')"
                v-model="form.password"
                :errors="[form.errors.password]"
                required
            />

            <div class="flex items-center justify-between">
                <Checkbox :label="$t('field.remember')" v-model="form.remember" />

                <Link
                    v-if="canResetPassword"
                    :href="route('auth.password.request')"
                    class="text-sm underline rounded-md text-primary-500 hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    {{ $t('auth.recover') }}
                </Link>
            </div>

            <Button type="submit" class="w-full mt-2" :disabled="form.processing" primary>
                {{ $t('auth.login') }}
            </Button>

            <p class="text-sm text-center">
                {{ $t('auth.register.no_account') }}

                <Link :href="route('auth.register')" class="font-medium text-primary-800 hover:underline">
                    {{ $t('auth.register.register') }}
                </Link>
            </p>
        </form>
    </AuthLayout>
</template>

<script setup>
    import AuthLayout from '@/Layouts/AuthLayout.vue';
    import { useForm, Link } from '@inertiajs/vue3';
    import route from '@/Helpers/useRoute';
    import Button from '@/Components/Button.vue';
    import Checkbox from '@/Components/Form/Checkbox.vue';
    import Input from '@/Components/Form/Input.vue';

    defineProps({
        canResetPassword: {
            type: Boolean,
            default: false,
        },
    });

    const form = useForm({
        email: null,
        password: null,
        remember: false,
    });

    const submit = () => {
        form.post(route('auth.login'), {
            onFinish: () => form.reset('password'),
            replace: true,
        });
    };
</script>
