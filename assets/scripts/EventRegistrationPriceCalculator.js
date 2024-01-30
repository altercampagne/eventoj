import { Modal } from 'bootstrap'

class EventRegistrationPriceCalculator {
  constructor() {
    this.selectStart = document.querySelector('#event_registration_form_stageStart');
    this.selectEnd = document.querySelector('#event_registration_form_stageEnd');

    this.availableOptions = [...this.selectStart.options].map(o => o.value);

    this.daysOfPresenceInput = document.querySelector('#event-registration-days-of-presence');
    this.pricePerDayInput = document.querySelector('#event_registration_form_pricePerDay');
    this.finalPrice = document.querySelector('#event-registration-final-price');

    this.updateFinalPrice = this.updateFinalPrice.bind(this)
    this.updateSelectEnd = this.updateSelectEnd.bind(this)

    this.selectStart.addEventListener('change', this.updateSelectEnd);
    this.selectEnd.addEventListener('change', this.updateFinalPrice);
    this.pricePerDayInput.addEventListener('change', this.updateFinalPrice);

    document.querySelectorAll('div#stageStartModal button[data-stage]').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();

        Modal.getInstance(document.querySelector('div#stageStartModal')).hide();
        this.selectStart.value = event.target.dataset.stage;
        this.updateSelectEnd();
      });
    });

    document.querySelectorAll('div#stageEndModal button[data-stage]').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();

        Modal.getInstance(document.querySelector('div#stageEndModal')).hide();
        this.selectEnd.value = event.target.dataset.stage;
        this.updateFinalPrice();
      });
    });

    this.updateSelectEnd();
  }

  updateSelectEnd() {
    const startIndex = this.availableOptions.indexOf(this.selectStart.value);

    // Disable days which cannot be booked (cannot leave BEFORE arriving)
    Array.prototype.forEach.call(this.selectEnd.options, (option) => {
      option.disabled = option.index <= startIndex;

      let button = document.querySelector('div#stageEndModal button[data-stage="'+option.value+'"]');
      let listItem = button.closest('div.list-group-item');
      let details = listItem.querySelector('div.stageAvailability');

      button.disabled = option.disabled;
      listItem.disabled = option.disabled;

      if (option.disabled) {
        listItem.classList.add('list-group-item-secondary');
        listItem.classList.remove('list-group-item-action');
        details.classList.add('d-none');
      } else {
        listItem.classList.remove('list-group-item-secondary');
        listItem.classList.add('list-group-item-action');
        details.classList.remove('d-none');
      }
    })

    // Update end date if needed
    if(this.availableOptions.indexOf(this.selectEnd.value) <= startIndex) {
      this.selectEnd.value = this.availableOptions[startIndex + 4];
    }
    this.updateFinalPrice();
  }

  updateFinalPrice() {
    const numberOfDays = this.availableOptions.indexOf(this.selectEnd.value) - this.availableOptions.indexOf(this.selectStart.value);

    this.daysOfPresenceInput.value = numberOfDays;

    this.finalPrice.textContent = numberOfDays*this.pricePerDayInput.value + 10;
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('#event_registration_form_stageStart')) {
    (() => new EventRegistrationPriceCalculator())()
  }
})
