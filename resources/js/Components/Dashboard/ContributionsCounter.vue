<template>
    <div class="flex items-center gap-4 p-6 bg-green-50 drop-shadow rounded-xl">
        <Icon icon="pin" class="w-16 h-20" />

        <dl class="text-primary-800">
            <dd class="text-5xl font-bold tracking-tight" v-text="contributions" />
            <dt
                class="text-2xl font-medium lowercase truncate"
                v-text="$tChoice('profile.contributions_count', contributions)"
            />
        </dl>
    </div>
</template>

<script setup>
    import Icon from '@/Components/Icon.vue';

    const props = defineProps({
        contributions: {
            type: Number,
            required: true,
        },
    });
</script>
