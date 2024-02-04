import { Tooltip } from 'bootstrap'
import '@fortawesome/fontawesome-free'
import '@fortawesome/fontawesome-free/css/all.css'
import './styles/app.scss'

import './scripts/EventRegistrationPriceCalculator.js'

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))
