import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useFuse } from '@vueuse/integrations/useFuse';

const page = usePage();

export const query = ref(null);

export const groupMaterialsByCategory = (
    materials = page.props.materials.items,
    categories = page.props.materials.categories
) => {
    return categories
        .map((category) => ({
            ...category,
            materials: materials.filter((material) => {
                let item = material.hasOwnProperty('item') ? material.item : material;

                return item.categories.map((category) => category.id).includes(category.id);
            }),
        }))
        .filter(({ materials }) => materials.length);
};

export default function (materials) {
    const query = ref(null);

    if (typeof materials === 'undefined' || materials === null || !materials.length) {
        materials = page.props.materials.items;
    }

    const { results } = useFuse(query, materials, {
        fuseOptions: {
            keys: ['name', 'categories.name'],
            shouldSort: false,
            includeScore: false,
            ignoreLocation: true,
            findAllMatches: true,
            threshold: 0.3,
        },
        matchAllWhenSearchEmpty: true,
    });

    const groupedResults = computed(() => groupMaterialsByCategory(results.value));

    return {
        query,
        results: groupedResults,
    };
}
