import 'leaflet'
import 'leaflet/dist/leaflet.min.css'

export default class Map {
  constructor(container, {
    latitude = 46.7111,
    longitude = 1.7191,
    zoomLevel = 13,
  } = {}) {
    this.customIcon = L.icon({
      iconUrl: document.getElementById('leaflet-config').dataset.markerIcon,
      iconSize: [30, 45],
      popupAnchor: [0, -10],
    });

    this.container = container;
    this.map = L.map(this.container).setView([latitude, longitude], zoomLevel);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);
  }
};
