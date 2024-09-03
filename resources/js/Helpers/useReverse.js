import axios from 'axios';
import route from '@/Helpers/useRoute.js';
import { getCoordinatesParameter } from '@/Helpers/useCoordinates';

export const reverse = async ({ lat, lng, zoom = 18 }) => {
    const coordinates = getCoordinatesParameter({ lat, lng }, zoom);

    try {
        const response = await axios.get(route('front.map.reverse', { coordinates }));

        return response.data.name;
    } catch (error) {
        console.log(error);
        return null;
    }
};
