import Map from './Map.js'

class AlternativesMap extends Map {
  constructor(container) {
    super(container, { zoomLevel: 6 });

    container.querySelectorAll('ul > li').forEach((element) => {
      L.marker([element.dataset.latitude, element.dataset.longitude], { icon: this.customIcon }).addTo(this.map)
        .bindPopup(element.innerHTML);

      for (event in element.dataset.events.split(',')) {
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (null !== document.querySelector('#alternatives-map')) {
    (() => new AlternativesMap(document.querySelector('#alternatives-map')))();
  };
});
