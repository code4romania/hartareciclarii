<template>
    <fieldset class="contents">
        <legend class="text-sm font-medium text-gray-900" v-text="$t('add_point.materials.subtitle')" />

        <FormField name="materials" :label="$t('add_point.materials.label')" :errors="[form.errors.materials]" required>
            <template #default="{ invalid }">
                <Tree
                    v-model:selectionKeys="form.materials"
                    :value="materials"
                    selectionMode="checkbox"
                    :invalid="invalid"
                    filter
                    filterMode="lenient"
                    required
                    fluid
                    expandedKeys
                    :pt="{
                        nodeContent: ({ global, context }) => ({
                            class: [
                                ...global.class,
                                {
                                    'bg-gray-50': !context.leaf,
                                    'pl-12': context.leaf,
                                },
                            ],
                        }),
                        nodeChildren: ({ global }) => ({
                            class: [...global.class, 'divide-y divide-gray-200'],
                        }),
                    }"
                >
                    <template #category="slotProps">
                        <div class="flex items-center justify-start w-full gap-2 px-2 py-3 text-left bg-gray-50">
                            <div class="flex items-center justify-center w-8 h-8 shrink-0">
                                <img v-if="slotProps.node.icon" :src="slotProps.node.icon" alt="" />
                            </div>

                            <div class="flex-1 text-sm font-medium text-gray-900">
                                {{ slotProps.node.label }}
                            </div>
                        </div>
                    </template>
                </Tree>
            </template>
        </FormField>
    </fieldset>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import Tree from 'primevue/tree';
    import FormField from '@/Components/Form/Field.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
    });

    const page = usePage();

    const materials = computed(() =>
        (page.props.materials || []).map((category) => ({
            ...category,
            selectable: false,
        }))
    );
</script>
