import { Modal } from 'bootstrap'

class EventRegistrationBikeSelector {
  constructor() {
    this.bikeInput = document.querySelector('#choose_people_form_neededBike');

    this.companionInputs = document.querySelectorAll('input[name="choose_people_form[companions][]"]');

    this.companionInputs.forEach((element) => {
      element.addEventListener('change', (event) => {
        this.updateBikeInputMaxAttribute();
      });
    });

    this.updateBikeInputMaxAttribute();
  }

  updateBikeInputMaxAttribute() {
    this.bikeInput.max = Array.from(this.companionInputs).filter((input) => input.checked).length + 1;
    if (this.bikeInput.value > this.bikeInput.max) {
      this.bikeInput.value = this.bikeInput.max;
    }
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('#choose_people_form_neededBike')) {
    (() => new EventRegistrationBikeSelector())()
  }
})
