import 'leaflet'

class Legend extends L.Control {
  onAdd(map) {
    const div = L.DomUtil.create('div', 'info legend');
    const grades = [0, 1, 5, 10, 20, 50];

    // loop through our density intervals and generate a label with a colored square for each interval
    for (var i = 0; i < grades.length; i++) {
        div.innerHTML +=
            '<i style="background:' + this.getColor(grades[i]) + '"></i> ' +
            grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
    }

    return div;
  }

  getColor(count) {
    return count >= 50 ? '#800026' :
          count >= 20 ? '#BD0026' :
          count >= 10 ? '#E31A1C' :
          count >= 5 ? '#FC4E2A' :
          count >= 1 ? '#FEB24C' :
          '#FFEDA0';
  }
}

L.control.legend = (options) => {
    return new Legend(options);
}
