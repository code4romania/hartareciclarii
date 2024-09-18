<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="problemType.name" />

        <MaterialsChecklist
            name="materials"
            v-model="form.materials_add"
            :label="$t('report.materials.add')"
            :errors="[form.errors.materials]"
            :materials="materials"
            type="add"
            required
        >
            <template #help="{ disabled }">
                <span v-if="disabled" class="text-sm text-gray-500" v-text="$t('report.materials.help_add')" />
            </template>
        </MaterialsChecklist>
    </fieldset>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';
    import MaterialsChecklist from '@/Components/Form/MaterialsChecklist.vue';

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
        problemType: {
            type: Object,
            required: true,
        },
        preselectedMaterials: {
            type: Array,
            required: true,
        },
    });

    const page = usePage();

    const materials = computed(() =>
        (page.props.materials || []).map((category) => {
            category = { ...category };

            category.children = category.children.map((material) => {
                material = { ...material };

                if (props.preselectedMaterials.includes(material.key)) {
                    material.checked = true;
                    material.disabled = true;
                }

                return material;
            });

            return category;
        })
    );
</script>
