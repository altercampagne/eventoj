import { Modal } from 'bootstrap'

class EventRegistrationChoosePrice {
  constructor() {
    this.btnMinus = document.querySelector('button#btn-minus');
    this.btnPlus = document.querySelector('button#btn-plus');
    this.priceInput = document.querySelector('#choose_price_form_price');

    this.btnMinus.addEventListener('click', (event) => {
      this.priceInput.value = parseInt(this.priceInput.value) - 1;
    });
    this.btnPlus.addEventListener('click', (event) => {
      this.priceInput.value = parseInt(this.priceInput.value) + 1;
    });
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('#choose_price_form_price')) {
    (() => new EventRegistrationChoosePrice())()
  }
})
