import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import route from '@/Helpers/useRoute.js';

const page = usePage();

const cancelTokens = ref([]);

export const getCenterCoordinates = ({ lat, lng }) => `${lat.toFixed(6)},${lng.toFixed(6)}`;
export const getCenterCoordinatesWithZoom = (center, zoom) => `@${getCenterCoordinates(center)},${zoom}z`;

export const updateMap = (leafletObject, routeName, routeParams = {}, options = {}) => {
    const center = leafletObject.getCenter();
    const zoom = leafletObject.getZoom();
    const bounds = leafletObject.getBounds();

    Object.assign(routeParams, {
        coordinates: getCenterCoordinatesWithZoom(center, zoom),
    });

    let headers = {
        'Map-Bounds': bounds.toBBoxString(),
    };

    if (options.hasOwnProperty('headers')) {
        headers = { ...headers, ...options.headers };
        delete options.headers;
    }

    router.visit(route(routeName, routeParams), {
        headers,
        onCancelToken: (cancelToken) => cancelTokens.value.push(cancelToken),
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
    updateMap(
        leafletObject,
        'front.map.point',
        { point },
        {
            only: ['context', 'point'],
        }
    );
};

export const fetchPoint = (leafletObject, pointId) => {
    updateMap(leafletObject, route().current(), route().params, {
        only: ['point'],
        replace: true,
        headers: {
            'Map-Point': pointId,
        },
    });
};

export const closePanel = () => {
    const props = page.props.mapOptions;
    const coordinates = getCenterCoordinatesWithZoom(props.center, props.zoom);

    router.visit(route('front.map.index', { coordinates }), {
        headers: {
            'Map-Bounds': props.bounds,
        },
    });
};

export const cancelMapVisits = () => {
    cancelTokens.value.forEach((cancelToken) => cancelToken.cancel());
    cancelTokens.value = [];
};
