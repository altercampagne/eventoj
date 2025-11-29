import './stimulus_bootstrap.js';
import { Tooltip } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import lightGallery from 'lightgallery'
import lgThumbnail from 'lightgallery/plugins/thumbnail'
import lgZoom from 'lightgallery/plugins/zoom'
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-thumbnail.css';
import 'lightgallery/css/lg-zoom.css';
import './styles/app.scss'

import './scripts/AlternativeMap.js'
import './scripts/AlternativesMap.js'
import './scripts/AlterpotesMap.js'
import './scripts/EventRegistrationBikeSelector.js'
import './scripts/EventRegistrationChooseDates.js'
import './scripts/EventRegistrationChoosePrice.js'
import './scripts/Upload/Dropzone.js'

document.querySelectorAll('.lightgallery').forEach((element) => {
  lightGallery(element, {
      selector: '.gallery-item',
      plugins: [lgZoom, lgThumbnail],
      thumbnail: true,
      licenseKey: document.getElementById('lightgallery-license-key').value,
      speed: 500,
  });
});

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))
