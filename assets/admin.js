import { Tooltip } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import 'trix'
import 'trix/dist/trix.min.css'
import './styles/admin/admin.scss'

import './scripts/S3FileUpload.js'

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

document.querySelectorAll('tr[data-href]').forEach((element) => {
  element.addEventListener('click', () => {
    window.location = element.dataset.href;
  });
});
