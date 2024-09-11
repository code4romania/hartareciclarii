<template>
    <div class="container mx-auto mt-10">
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

            <ContributionsCounter class="shrink-0" :contributions="contributions_count" />
        </div>

        <h3 class="mt-10 text-2xl font-bold text-gray-900">Contributions</h3>
        <div class="mx-auto mt-8">
            <div class="-mx-4 -my-2 overflow-x-auto bg-white sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.point_id')"
                                />
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.contribution_type')"
                                />
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.item_type')"
                                />
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.location')"
                                />
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.date_hour')"
                                />
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="item in contributions" :key="contributions.id">
                                <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-0">
                                    {{ item.id }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ item.type }}</td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ item.point_type }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ item.location }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ item.date }}</td>
                                <td
                                    class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-0"
                                >
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">{{
                                        $t('profile.table_content.view_on_map')
                                    }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';

    import ContributionsCounter from '@/Components/Dashboard/ContributionsCounter.vue';

    const props = defineProps({
        contributions: {
            type: Array,
            required: true,
        },
        contributions_count: {
            type: Number,
            required: true,
        },
    });
</script>
