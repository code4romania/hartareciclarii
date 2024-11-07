import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import route from '@/Helpers/useRoute.js';
import { getCoordinatesParameter } from '@/Helpers/useCoordinates';
import { isNull, isUndefined } from '@/Helpers/checkType';

const page = usePage();

const cancelTokens = ref([]);

export const onCancelToken = (cancelToken) => cancelTokens.value.push(cancelToken);

export const headers = (leafletObject, options = {}) => {
    const headers = {
        'Map-Bounds': leafletObject.getBounds().toBBoxString(),
    };

    if (options.hasOwnProperty('headers')) {
        Object.assign(headers, options.headers);
        delete options.headers;
    }

    return headers;
};

export const updateMap = (leafletObject, routeName, routeParams = {}, options = {}) => {
    Object.assign(routeParams, {
        coordinates: getCoordinatesParameter(leafletObject.getCenter(), leafletObject.getZoom()),
    });

    router.visit(route(routeName, routeParams), {
        headers: headers(leafletObject, options),
        onCancelToken,
        ...options,
    });
};
export const refreshPoints = (leafletObject) => {
    updateMap(leafletObject, route().current(), route().params, {
        only: ['points', 'filter'],
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

export const closePanel = (leafletObject) => {
    if (['filter', 'search'].includes(page.props.context) && !isNull(page.props.point) && !isUndefined(leafletObject)) {
        return fetchPoint(leafletObject, null);
    }

    const props = page.props.mapOptions;
    const coordinates = getCoordinatesParameter(props.center, props.zoom);

    router.visit(route('front.map.index', { coordinates }), {
        headers: headers(leafletObject),
    });
};

export const cancelMapVisits = () => {
    cancelTokens.value.forEach((cancelToken) => cancelToken.cancel());
    cancelTokens.value = [];
};
