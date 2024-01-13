class EventRegistrationPriceCalculator {
  constructor() {
    this.selectStart = document.querySelector('#event_registration_form_stageStart');
    this.selectEnd = document.querySelector('#event_registration_form_stageEnd');

    this.availableOptions = [...this.selectStart.options].map(o => o.value);

    this.daysOfPresenceInput = document.querySelector('#event-registration-days-of-presence');
    this.pricePerDayInput = document.querySelector('#event_registration_form_pricePerDay');
    this.finalPrice = document.querySelector('#event-registration-final-price');

    this.updateFinalPrice = this.updateFinalPrice.bind(this)

    this.selectStart.addEventListener('change', this.updateFinalPrice);
    this.selectEnd.addEventListener('change', this.updateFinalPrice);
    this.pricePerDayInput.addEventListener('change', this.updateFinalPrice);

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
