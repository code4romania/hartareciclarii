<template>
    <DashboardLayout>
        <div class="container mx-auto mt-10 ">
            <ProfileHeader :contributions_count="contributions_count"/>
            <h3 class="text-2xl font-bold text-gray-900 mt-10">Contributions</h3>
            <div class="mt-8 mx-auto ">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 bg-white">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-300 ">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.point_id')"/>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.contribution_type')"/>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.item_type')"/>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.location')"/>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    v-text="$t('profile.table_heading.date_hour')"/>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <tr v-for="item in contributions" :key="contributions.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    {{ item.id }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.type }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.point_type }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.location }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.date }}</td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900"
                                    >{{ $t('profile.table_content.view_on_map') }}</a
                                    >
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>


<script setup>

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import ProfileHeader from "@/Components/ProfileHeader.vue";

const user = usePage().props.user;
const name = user.firstname + ' ' + user.lastname;
const props = defineProps({
    contributions: {
        type: Array,
        required: true
    }
});
const contributions_count = computed(() => {
    return props.contributions.length;
});


</script>
