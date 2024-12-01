import Map from './Map.js'

class AlterpotesMap extends Map {
  constructor(container) {
    super(container, { zoomLevel: 6 });

    const markers = new L.FeatureGroup();
    container.querySelectorAll('ul > li').forEach((element) => {
      const marker = L.marker([element.dataset.latitude, element.dataset.longitude], { icon: element.dataset.itself ? this.customIconPurple : this.customIconBlue })
        .bindPopup(element.innerHTML)
      ;

      markers.addLayer(marker);
    });

    this.map.addLayer(markers);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (null !== document.querySelector('#alterpotes-map')) {
    (() => new AlterpotesMap(document.querySelector('#alterpotes-map')))();
  };
});
