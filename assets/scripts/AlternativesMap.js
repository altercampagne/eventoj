import Map from './Map.js'
import './AlternativesMap/ControlDepartmentDetail.js'
import './AlternativesMap/ControlLegend.js'

class AlternativesMap extends Map {
  constructor(container) {
    super(container, { zoomLevel: 6 });

    const markers = new L.FeatureGroup();
    container.querySelectorAll('ul > li').forEach((element) => {
      const marker = L.marker([element.dataset.latitude, element.dataset.longitude], { icon: this.customIcon })
        .bindPopup(element.innerHTML)
      ;

      markers.addLayer(marker);

      for (event in element.dataset.events.split(',')) {
      }
    });

    this.map.on('zoomend', () => {
      if (this.map.getZoom() < 9) {
        this.map.removeLayer(markers);
      } else {
        this.map.addLayer(markers);
      }
    });
    const legend = L.control.legend({position: 'bottomright'}).addTo(this.map);

    const alternativeCountByDepartments = JSON.parse(container.dataset.alternativeCountByDepartments);

    const controlDepartmentDetail = L.control.departmentDetail().addTo(this.map);

    fetch('https://raw.githubusercontent.com/gregoiredavid/france-geojson/master/departements-version-simplifiee.geojson')
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        let geojson;
        geojson = L.geoJson(data, {
          style: (feature) => {
            let count = alternativeCountByDepartments[parseInt(feature.properties.code)];

            return {
              fillColor: legend.getColor(count),
              weight: 2,
              opacity: 1,
              color: 'white',
              dashArray: '3',
              fillOpacity: 0.7
            };
          },
          onEachFeature: (feature, layer) => {
            layer.on({
              mouseover: (e) => {
                var layer = e.target;

                layer.setStyle({
                  weight: 5,
                  color: '#666',
                  dashArray: '',
                  fillOpacity: 0.7
                });

                layer.bringToFront();
                controlDepartmentDetail.update(feature.properties.nom, feature.properties.code, alternativeCountByDepartments[parseInt(feature.properties.code)]);
              },
              mouseout: (e) => {
                geojson.resetStyle(e.target);
              },
              click: (e) => {
                this.map.fitBounds(e.target.getBounds());
              }
            });
          },
        }).addTo(this.map);
      })
    ;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (null !== document.querySelector('#alternatives-map')) {
    (() => new AlternativesMap(document.querySelector('#alternatives-map')))();
  };
});
