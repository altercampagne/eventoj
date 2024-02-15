import { Modal } from 'bootstrap'

class EventRegistrationChooseDates {
  constructor() {
    this.selectStart = document.querySelector('#choose_dates_form_stageStart');
    this.firstMeal = document.querySelector('#choose_dates_form_firstMeal');
    this.selectEnd = document.querySelector('#choose_dates_form_stageEnd');
    this.lastMeal = document.querySelector('#choose_dates_form_lastMeal');

    // To retrieve all available options, we need to concat / unique both
    // stageStart & stageEnd options. This is because we don't have the last
    // day in stageStart (and we don't have the first one in stageEnd)
    this.availableOptions = [...this.selectStart.options].map(o => o.value)
      .concat([...this.selectEnd.options].map(o => o.value))
      .filter((value, index, array) => array.indexOf(value) === index)
    ;

    this.daysOfPresenceElement = document.querySelector('#event-registration-days-of-presence');

    this.updateSelectEnd = this.updateSelectEnd.bind(this)
    this.updateNumberOfDays = this.updateNumberOfDays.bind(this)

    this.selectStart.addEventListener('change', this.updateSelectEnd);

    document.querySelectorAll('div#stageStartModal button[data-stage]').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();

        Modal.getInstance(document.querySelector('div#stageStartModal')).hide();
        this.selectStart.value = event.target.dataset.stage;
        this.firstMeal.value = event.target.dataset.meal;
        document.querySelector('#stageStartLabel').innerHTML = event.target.dataset.stageLabel;
        document.querySelector('#firstMealLabel').innerHTML = event.target.dataset.mealLabel;
        this.updateSelectEnd();
      });
    });

    document.querySelectorAll('div#stageEndModal button[data-stage]').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();

        Modal.getInstance(document.querySelector('div#stageEndModal')).hide();
        this.selectEnd.value = event.target.dataset.stage;
        this.lastMeal.value = event.target.dataset.meal;
        document.querySelector('#stageEndLabel').innerHTML = event.target.dataset.stageLabel;
        document.querySelector('#lastMealLabel').innerHTML = event.target.dataset.mealLabel;
        this.updateNumberOfDays();
      });
    });

    this.updateSelectEnd();
  }

  updateSelectEnd() {
    const startIndex = this.availableOptions.indexOf(this.selectStart.value);

    const modal = document.querySelector('div#stageEndModal');

    // If the selected start date is after the current end date, we put an end
    // date 4 days after the start date (of possible, otherwise we select the
    // latest available date)
    if(this.availableOptions.indexOf(this.selectEnd.value) < startIndex) {
      let endIndex = startIndex + 4;
      if (endIndex > this.selectEnd.options.length - 1) {
        endIndex = this.selectEnd.options.length - 1;
      }
      this.selectEnd.value = this.availableOptions[endIndex];
    }

    this.updateAccordionValue(modal, this.selectEnd);

    // All stages situated after a "complete" stage must be disabled because
    // it's not possible to register for a period which includes days which are
    // already full.
    let disableNextStages = false;
    // Disable days which cannot be booked :
    // - days before arrival
    // - days after a complete stage
    Array.prototype.forEach.call(this.availableOptions, (option) => {
      let item = modal.querySelector('div.accordion-item[data-stage="'+option+'"]');

      let index = this.availableOptions.indexOf(option);
      // The option is BEFORE the stageStart
      if (index < startIndex) {
        this.disableAccordionItem(item);

        return;
      }

      // Same day !
      if (index == startIndex) {
        if (this.firstMeal.value == 'dinner') {
          this.disableAccordionItem(item);

          return;
        }
        item.querySelector('button.choose-meal-button[data-meal="breakfast"]').classList.add('d-none');
        if (this.firstMeal.value == 'lunch') {
          item.querySelector('button.choose-meal-button[data-meal="lunch"]').classList.add('d-none');
        }

        return;
      }

      // If the list item is already full or almost, we don't have anyhting
      // special to do, except saving this info to disable all following
      // options
      if (item.dataset.full == 1) {
        disableNextStages = true;

        return;
      }

      if (disableNextStages) {
        this.disableAccordionItem(item);

        return;
      }

      this.enableAccordionItem(item);
    })

    this.updateNumberOfDays();
  }

  updateAccordionValue(modal, select) {
    modal.querySelectorAll('.accordion-item').forEach((item) => {
      let selected = item.dataset.stage == select.value;
      let button = item.querySelector('button.accordion-button');
      let collapse = item.querySelector('div.accordion-collapse');

      button.attributes.getNamedItem('aria-expanded').nodeValue = selected;
      if (selected) {
        button.classList.remove('collapsed');
        collapse.classList.add('show');
      } else {
        button.classList.add('collapsed');
        collapse.classList.remove('show');
      }
    })
  }

  disableAccordionItem(item) {
      let collapseButton = item.querySelector('button.accordion-button');

      collapseButton.classList.add('bg-secondary-subtle');
      collapseButton.classList.add('p-2');

      item.querySelectorAll('button.choose-meal-button').forEach((button) => {
        button.classList.add('d-none');
      });

      if (!item.dataset.full) {
        item.querySelector('span.badge-not-available').classList.remove('d-none');
      }
  }

  enableAccordionItem(item) {
      let collapseButton = item.querySelector('button.accordion-button');

      collapseButton.classList.remove('bg-secondary-subtle');
      collapseButton.classList.remove('p-2');

      item.querySelectorAll('button.choose-meal-button').forEach((button) => {
        button.classList.remove('d-none');
      });

      item.querySelector('span.badge-not-available').classList.add('d-none');
  }

  updateNumberOfDays() {
    const numberOfDays = this.availableOptions.indexOf(this.selectEnd.value) - this.availableOptions.indexOf(this.selectStart.value);

    this.daysOfPresenceElement.innerText = numberOfDays;
  }
}

window.addEventListener('DOMContentLoaded', () => {
  if (null != document.querySelector('#choose_dates_form_stageStart')) {
    (() => new EventRegistrationChooseDates())()
  }
})
