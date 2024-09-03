import { ref } from 'vue';
import L from 'leaflet';
import 'leaflet.locatecontrol';
import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';

export default function () {
    const locateControlRef = ref(null);

    const locateControl = (options = {}) => {
        locateControlRef.value = L.control.locate({
            position: 'bottomright',
            showPopup: false,
            locateOptions: {
                enableHighAccuracy: true,
            },
            clickBehavior: {
                inView: 'setView',
                outOfView: 'setView',
                inViewNotFollowing: 'setView',
            },
            ...options,
        });

        return locateControlRef.value;
    };

    const startLocate = () => locateControlRef.value.start();

    return {
        locateControl,
        startLocate,
    };
}
