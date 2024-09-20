<template>
    <Alert v-if="$page.props.auth.is_unverified" type="warning" :message="$t('auth.verify.unverified_notice')">
        <template #action>
            <button
                type="button"
                @click="submit"
                class="font-semibold text-yellow-700 underline hover:text-yellow-600"
                v-text="$t('auth.verify.resend')"
            />
        </template>
    </Alert>

    <Alert
        v-else-if="$page.props.auth.show_verified_message"
        type="success"
        :message="$t('auth.verify.verified_notice')"
    />
</template>

<script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import route from '@/Helpers/useRoute';
    import Alert from '@/Components/Alert.vue';

    const success = ref(false);
    const message = ref(null);

    const submit = () => {
        axios
            .post(route('auth.verification.send'))
            .then((response) => {
                success.value = true;
                message.value = response.data.message;
            })
            .catch(({ response }) => {
                success.value = false;
                message.value = response.data.message;
            });
    };
</script>
