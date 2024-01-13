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

    this.updateSelectEnd();
  }

  updateSelectEnd() {
    const startIndex = this.availableOptions.indexOf(this.selectStart.value);

    // Disable days which cannot be booked in order to have at least 4 days.
    Array.prototype.forEach.call(this.selectEnd.options, (option) => {
      option.disabled = option.index < startIndex + 4;
    })

    // Update end date if needed
    if(this.availableOptions.indexOf(this.selectEnd.value) < startIndex + 4) {
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
