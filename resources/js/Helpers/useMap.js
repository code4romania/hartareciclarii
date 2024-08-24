import { router, usePage } from '@inertiajs/vue3';
import route from '@/Helpers/useRoute.js';

const page = usePage();

export const updateMap = (leafletObject, routeName, routeParams = {}, options = {}) => {
    const center = leafletObject.getCenter();
    const zoom = leafletObject.getZoom();
    const bounds = leafletObject.getBounds();

    const coordinates = `@${center.lat.toFixed(6)},${center.lng.toFixed(6)},${zoom}z`;

    router.visit(route(routeName, Object.assign(routeParams, { coordinates })), {
        headers: {
            'Map-Bounds': bounds.toBBoxString(),
        },
        ...options,
    });
};

export const refreshPoints = (leafletObject) => {
    updateMap(leafletObject, route().current(), route().params, {
        only: ['points', 'mapOptions'],
        replace: true,
    });
};

export const openPoint = (leafletObject, point) => {
    updateMap(leafletObject, 'point', { point });
};

export const closePanel = () => {
    const props = page.props.mapOptions;
    const coordinates = `@${props.center.lat.toFixed(6)},${props.center.lng.toFixed(6)},${props.zoom}z`;

    router.visit(route('home', { coordinates }), {
        headers: {
            'Map-Bounds': props.bounds,
        },
    });
};
