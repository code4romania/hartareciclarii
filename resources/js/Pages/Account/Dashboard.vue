<template>
    <Head :title="$t('profile.my_profile')" />

    <div class="container mx-auto mt-10 space-y-12">
        <div class="flex flex-col gap-6 lg:flex-row">
            <div
                class="flex flex-col flex-1 gap-5 p-6 overflow-hidden bg-white rounded-lg drop-shadow sm:flex-row sm:flex-wrap sm:items-center sm:justify-between"
            >
                <img class="w-20 h-20 mx-auto rounded-full shrink-0" :src="$page.props.auth.avatar" alt="" />

                <div class="flex-1 space-y-1 overflow-hidden text-center sm:text-left">
                    <h1
                        class="text-xl font-bold text-gray-900 truncate sm:text-2xl"
                        v-text="$page.props.auth.full_name"
                    />

                    <p class="text-sm font-medium text-gray-600">
                        <span class="inline-flex items-center gap-2">
                            <span v-text="$page.props.auth.email" />

                            <template v-if="$page.props.auth.phone">
                                <span class="font-normal opacity-50 select-none" aria-hidden="true">|</span>
                                <span v-text="$page.props.auth.phone" />
                            </template>
                        </span>
                    </p>

                    <p class="text-sm font-medium text-gray-600">
                        <span v-text="$t('profile.register_from')" />
                        <time v-text="$page.props.auth.created_at" />
                    </p>
                </div>

                <div class="w-full shrink-0 md:w-auto">
                    <Link
                        :href="route('front.account.settings')"
                        class="flex items-center justify-center px-3 py-2 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        v-text="$t('profile.settings')"
                    />
                </div>
            </div>

            <ContributionsCounter class="shrink-0" :contributions="contributions.meta.total" />
        </div>

        <Table :collection="contributions">
            <template #title>
                {{ $t('contributions.title') }}
            </template>

            <template #contribution_type="{ contribution_type, row }">
                <div
                    v-if="contribution_type === 'place'"
                    class="font-medium text-gray-900"
                    v-text="$t('contributions.type.place')"
                />

                <div v-if="contribution_type === 'problem'">
                    <div class="font-medium text-gray-900" v-text="$t('contributions.type.problem')" />
                    <div v-text="row.problem_type" />
                </div>
            </template>

            <template #actions="{ row }">
                <Link v-if="row?.url" :href="row.url" class="text-sm font-medium text-blue-600 hover:underline">
                    {{ $t('contributions.view_on_map') }}
                </Link>
            </template>

            <template #empty>
                <div class="flex items-start gap-6">
                    <Icon icon="thanks" class="w-28 h-28" />
                    <div class="flex-1 prose prose-lg max-w-none">
                        <p v-for="(line, index) in $t('contributions.empty.lines')" :key="index" v-text="line" />
                    </div>
                </div>
            </template>
        </Table>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';

    import ContributionsCounter from '@/Components/Dashboard/ContributionsCounter.vue';
    import Table from '@/Components/Table/Table.vue';
    import Icon from '@/Components/Icon.vue';
    import Head from '@/Components/Head.vue';

    const props = defineProps({
        contributions: {
            type: Object,
            required: true,
        },
    });
</script>
