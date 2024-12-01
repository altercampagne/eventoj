import Map from './Map.js'

class AlternativeMap extends Map {
  constructor(container) {
    super(container, {
      latitude: container.dataset.latitude,
      longitude: container.dataset.longitude,
    });

    L.marker([container.dataset.latitude, container.dataset.longitude], { icon: this.customIconBlue }).addTo(this.map)
      .bindPopup(container.dataset.tooltip)
      .openPopup();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('div.map').forEach((element) => {
    (() => new AlternativeMap(element))();
  });
});
