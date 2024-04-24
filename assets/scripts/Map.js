import 'leaflet'
import 'leaflet/dist/leaflet.min.css'

class Map {
  constructor(container) {
    let customIcon = L.icon({
      iconUrl: document.getElementById('leaflet-config').dataset.markerIcon,
      iconSize: [30, 45],
      popupAnchor: [0, -10],
    });

    this.container = container;

    this.map = L.map(this.container).setView([this.container.dataset.latitude, this.container.dataset.longitude], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(this.map);

    L.marker([this.container.dataset.latitude, this.container.dataset.longitude], { icon: customIcon }).addTo(this.map)
      .bindPopup(this.container.dataset.tooltip)
      .openPopup();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('div.map').forEach((element) => {
    (() => new Map(element))();
  });
});
