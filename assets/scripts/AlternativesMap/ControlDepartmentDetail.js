import 'leaflet'

class DepartmentDetail extends L.Control {
  onAdd(map) {
    const div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
    div.innerHTML = '<h4>Alternatives par département</h4>';

    return div;
  }

  update(nom, code, count) {
    this.getContainer().innerHTML = '<h4>Alternatives par département</h4><b>' + nom + ' (' + code + ')</b><br />';
    if (count == 0) {
      this.getContainer().innerHTML += 'Aucune alternative';
    } else if (count == 1) {
      this.getContainer().innerHTML += '1 alternative';
    } else {
      this.getContainer().innerHTML += count + ' alternatives';
    }
  }
}

L.control.departmentDetail = (options) => {
    return new DepartmentDetail(options);
}
