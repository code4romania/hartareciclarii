<template>
    <AuthLayout :title="$t('auth.register.title')">
        <template #backdrop>
            <h1>Devino membru Harta Reciclării</h1>

            <ul>
                <li>Adaugă noi puncte pe hartă</li>
                <li>Raporteazaă probleme cu punctele existente</li>
                <li>Contribuie la efortul de a menține informațiile actualizate</li>
                <li>Urmărește statusul raportărilor tale</li>
                <li>
                    Alătură-te unei comunități de oameni care vor să trăiască într-un mediu mai verde și mai sustenabil
                </li>
            </ul>
        </template>

        <form class="grid gap-6" @submit.prevent="register">
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

            <Input
                name="phone"
                type="tel"
                :label="$t('auth.register.phone')"
                v-model="form.phone"
                :errors="[form.errors.phone]"
            />

            <Input
                name="email"
                type="email"
                :label="$t('auth.register.email')"
                v-model="form.email"
                :errors="[form.errors.email]"
                required
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

            <Button type="submit" class="w-full mt-2" :disabled="form.processing" primary>
                {{ $t('auth.register.submit') }}
            </Button>

            <p class="text-sm text-center">
                {{ $t('auth.register.already_have_account') }}

                <Link :href="route('auth.login')" class="font-medium text-primary-800 hover:underline">
                    {{ $t('auth.login') }}
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
    import Input from '@/Components/Form/Input.vue';

    const form = useForm({
        first_name: null,
        last_name: null,
        email: null,
        phone: null,
        password: null,
        password_confirmation: null,
    });

    const register = () => {
        form.post(route('auth.register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
            replace: true,
        });
    };
</script>
