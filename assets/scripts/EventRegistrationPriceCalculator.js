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

    const modal = document.querySelector('div#stageEndModal');

    // All stages situated after a "complete" stage must be disabled because
    // it's not possible to register for a period which includes days which are
    // already full.
    let disableNextStages = false;
    // Disable days which cannot be booked (cannot leave BEFORE arriving)
    Array.prototype.forEach.call(this.selectEnd.options, (option) => {
      option.disabled = option.index <= startIndex;

      let mustBeDisabled = option.disabled || disableNextStages;

      let listItem = modal.querySelector('div.list-group-item[data-stage="'+option.value+'"]');
      // Update list item only if the stage is not full
      if (listItem.dataset.full == 1) {
        disableNextStages = true;
      } else {
        let button = listItem.querySelector('button[data-stage="'+option.value+'"]');
        let contentForAvailableStageOnly = listItem.querySelectorAll('.content-for-available-stage-only');
        let contentForUnavailableStageOnly = listItem.querySelectorAll('.content-for-unavailable-stage-only');

        button.disabled = mustBeDisabled;
        listItem.disabled = mustBeDisabled;

        if (mustBeDisabled) {
          listItem.classList.add('list-group-item-secondary');
          button.classList.add('d-none');
          contentForAvailableStageOnly.forEach((e) => e.classList.add('d-none'));
          contentForUnavailableStageOnly.forEach((e) => e.classList.remove('d-none'));
        } else {
          listItem.classList.remove('list-group-item-secondary');
          button.classList.remove('d-none');
          contentForAvailableStageOnly.forEach((e) => e.classList.remove('d-none'));
          contentForUnavailableStageOnly.forEach((e) => e.classList.add('d-none'));
        }
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
