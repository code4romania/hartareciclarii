<template>
    <div class="px-6 py-4 ring-1 ring-gray-200">
        <h2 class="font-medium">Materiale colectate</h2>

        <div class="mt-4 grid gap-0.5">
            <Accordion v-for="(category, index) in materials" :key="`category-${index}`">
                <template #icon>
                    <img :src="category.icon" alt="" />
                </template>

                <template #title>
                    {{ category.name }}
                </template>

                <ul class="divide-y divide-gray-200">
                    <li v-for="(material, index) in category.materials" :key="`material-${index}`" class="py-1 pl-12">
                        <Link
                            :href="route('material', material.id)"
                            class="text-blue-500 hover:underline focus:underline"
                        >
                            {{ material.name }}
                        </Link>
                    </li>
                </ul>
            </Accordion>
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    import Accordion from '@/Components/Accordion.vue';

    const props = defineProps({
        materials: {
            type: Array,
            required: true,
        },
    });
</script>


