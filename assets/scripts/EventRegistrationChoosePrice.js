import { Modal } from 'bootstrap'

class EventRegistrationChoosePrice {
  constructor() {
    this.priceInput = document.querySelector('#choose_price_form_price');

    document.querySelector('button#btn-minus').addEventListener('click', (event) => {
      this.priceInput.value = parseInt(this.priceInput.value) - 1;
    });
    document.querySelector('button#btn-plus').addEventListener('click', (event) => {
      this.priceInput.value = parseInt(this.priceInput.value) + 1;
    });

    // Update price when changing to minimum / breakEvent / support prices
    document.querySelectorAll('button.btn-change-price').forEach((element) => {
      element.addEventListener('click', (event) => {
        this.priceInput.value = parseInt(event.target.dataset.price);
      });
    });
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('#choose_price_form_price')) {
    (() => new EventRegistrationChoosePrice())()
  }
})
