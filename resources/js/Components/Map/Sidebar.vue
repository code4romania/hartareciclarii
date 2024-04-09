<template>
    <div class="flex flex-col px-6 pb-4 overflow-y-auto bg-white grow gap-y-5">
        <div class="flex items-center h-16 shrink-0">
            <img
                class="w-auto h-8"
                src="https://tailwindui.com/img/logos/mark.svg?color=blue&shade=600"
                alt="Your Company"
            />
        </div>
        <nav class="flex flex-col flex-1">
            <ul role="list" class="flex flex-col flex-1 gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <li v-for="item in navigation" :key="item.name">
                            <a
                                :href="item.href"
                                :class="[
                                    item.current
                                        ? 'bg-gray-50 text-blue-600'
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50',
                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                        item.current ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-600',
                                        'h-6 w-6 shrink-0',
                                    ]"
                                    aria-hidden="true"
                                />
                                {{ item.name }}
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="text-xs font-semibold leading-6 text-gray-400">Your teams</div>
                    <ul role="list" class="mt-2 -mx-2 space-y-1">
                        <li v-for="team in teams" :key="team.name">
                            <a
                                :href="team.href"
                                :class="[
                                    team.current
                                        ? 'bg-gray-50 text-blue-600'
                                        : 'text-gray-700 hover:text-blue-600 hover:bg-gray-50',
                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                ]"
                            >
                                <span
                                    :class="[
                                        team.current
                                            ? 'text-blue-600 border-blue-600'
                                            : 'text-gray-400 border-gray-200 group-hover:border-blue-600 group-hover:text-blue-600',
                                        'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-[0.625rem] font-medium bg-white',
                                    ]"
                                    >{{ team.initial }}</span
                                >
                                <span class="truncate">{{ team.name }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
    defineProps({
        navigation: Array,
        teams: Array,
    });
</script>
