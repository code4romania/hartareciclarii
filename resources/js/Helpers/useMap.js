import { router } from '@inertiajs/vue3';
import route from '@/Helpers/useRoute.js';

const mergeObjects = (A, B) => {
    let merged = {};

    Object.keys({ ...A, ...B }).map((key) => {
        merged[key] = B[key] || A[key];
    });

    return merged;
};

export const updateMap = (leafletObject, routeName, routeParams = {}, options = {}) => {
    const center = leafletObject.getCenter();
    const zoom = leafletObject.getZoom();
    const bounds = leafletObject.getBounds();

    const coordinates = `@${center.lat.toFixed(6)},${center.lng.toFixed(6)},${zoom}z`;

    router.get(
        route(routeName, Object.assign(routeParams, { coordinates })),
        {},
        {
            headers: {
                'Map-Bounds': bounds.toBBoxString(),
            },
            ...options,
        }
    );
};

export const refreshPoints = (leafletObject) => {
    updateMap(leafletObject, route().current(), route().params, {
        only: ['points'],
        replace: true,
    });
};

export const openPoint = (leafletObject, point) => {
    updateMap(leafletObject, 'point', { point });
};
