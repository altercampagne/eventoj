import { Tooltip } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import './styles/app.scss'

import './scripts/EventRegistrationBikeSelector.js'
import './scripts/EventRegistrationChooseDates.js'
import './scripts/EventRegistrationChoosePrice.js'

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))
