export const getCoordinates = ({ lat, lng }) => `${lat.toFixed(6)},${lng.toFixed(6)}`;

export const getCoordinatesParameter = (latLng, zoom) => `@${getCoordinates(latLng)},${zoom}z`;
