import './bootstrap.js';
import { Tooltip, Modal } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import 'trix'
import 'trix/dist/trix.min.css'
import './styles/admin/admin.scss'

import './scripts/AddressAutocomplete.js'
import './scripts/S3FileUpload.js'

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

// Makes table links clickable
document.querySelectorAll('tr[data-href]').forEach((element) => {
  element.addEventListener('click', () => {
    window.location = element.dataset.href;
  });
});

// Open Magic search when Ctrl + K is hit
window.addEventListener('keydown', (event) => {
  if (!event.ctrlKey || String.fromCharCode(event.which).toLowerCase() !== 'k') {
    return;
  }

  event.preventDefault();

  new Modal(document.getElementById('magicSearchModal')).show()
});

// Focus on search input when opening magic search modal
document.getElementById('magicSearchModal').addEventListener('shown.bs.modal', () => {
  document.getElementById('magicSearchInput').focus()
})
