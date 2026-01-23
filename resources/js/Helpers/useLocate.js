import { ref } from 'vue';
import L from 'leaflet';
import 'leaflet.locatecontrol/dist/L.Control.Locate.min.css';

// Import the locate control plugin
// The plugin needs to extend L.control.locate, so we import it and ensure it's attached
//TODO check with Andrei
import { locate } from 'leaflet.locatecontrol/dist/L.Control.Locate.esm.js';

// Manually attach to L.control if not already attached (for ES module compatibility)
if (!L.control.locate) {
    L.control.locate = locate;
}

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
