<template>
    <fieldset class="contents">
        <legend class="text-base font-medium text-gray-900 sm:text-lg" v-text="problemType.name" />

        <MaterialsChecklist
            name="materials"
            v-model="form.materials_remove"
            :label="$t('report.materials.remove')"
            :errors="[form.errors.materials_remove]"
            :materials="materials"
            remove
            required
        >
            <template #help="{ checked }">
                <span v-if="checked" class="text-sm text-gray-500" v-text="$t('report.materials.help_remove')" />
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
        page.props.materials.items.filter((material) => props.preselectedMaterials.includes(material.id))
    );
</script>
