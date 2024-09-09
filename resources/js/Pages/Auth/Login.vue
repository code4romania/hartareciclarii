<template>
    <AuthLayout :title="$t('auth.login')">
        <form class="grid gap-6" @submit.prevent="login">
            <Input
                name="email"
                type="email"
                :label="$t('auth.email')"
                v-model="form.email"
                :errors="[form.errors.email]"
            />

            <Input
                name="password"
                type="password"
                :label="$t('auth.password')"
                v-model="form.password"
                :errors="[form.errors.password]"
            />

            <div class="flex items-center justify-between">
                <Checkbox :label="$t('auth.remember')" v-model="form.remember" />

                <Link
                    v-if="canResetPassword"
                    :href="route('auth.password.request')"
                    class="text-sm underline rounded-md text-primary-500 hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    {{ $t('auth.recover') }}
                </Link>
            </div>

            <Button type="submit" class="w-full mt-2" primary>
                {{ $t('auth.login') }}
            </Button>
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
        status: {
            type: String,
            default: null,
        },
    });

    const form = useForm({
        email: null,
        password: null,
        remember: false,
    });

    const login = () => {
        form.post(route('auth.login'), {
            onFinish: () => form.reset('password'),
            replace: true,
        });
    };
</script>
