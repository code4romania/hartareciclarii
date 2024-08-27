import { router } from '@inertiajs/vue3';
import pickBy from '@/Helpers/pickBy.js';

export default function (props, url) {
    const applyFilters = () => {
        return router.get(
            url,
            pickBy({
                filter: pickBy(props.value),
            }),
            {
                preserveScroll: true,
            }
        );
    };

    const clearFilters = () => {
        router.visit(url);
    };

    return {
        applyFilters,
        clearFilters,
    };
}
