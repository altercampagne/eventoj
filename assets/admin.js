import './bootstrap.js';
import { Tooltip, Modal } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import 'trix'
import 'trix/dist/trix.min.css'
import ClipboardJS from 'clipboard'
import './styles/admin/admin.scss'

import './scripts/Upload/SingleS3FileUpload.js'

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

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

// Copy button
const clipboard = new ClipboardJS('div[data-clipboard-text]');
clipboard.on('success', function(e) {
  Tooltip.getOrCreateInstance(e.trigger).setContent({
    '.tooltip-inner': 'Copié !'
  });

  e.trigger.addEventListener('hidden.bs.tooltip', () => {
    Tooltip.getOrCreateInstance(e.trigger).setContent({
      '.tooltip-inner': e.trigger.dataset.bsOriginalTitle,
    })
  }, {
    once: !0
  });
});
