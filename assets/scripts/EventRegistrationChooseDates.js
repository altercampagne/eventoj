import { Modal } from 'bootstrap'

class EventRegistrationChooseDates {
  constructor() {
    this.selectStart = document.querySelector('#choose_dates_form_stageStart');
    this.selectEnd = document.querySelector('#choose_dates_form_stageEnd');

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
        this.updateSelectEnd();
      });
    });

    document.querySelectorAll('div#stageEndModal button[data-stage]').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();

        Modal.getInstance(document.querySelector('div#stageEndModal')).hide();
        this.selectEnd.value = event.target.dataset.stage;
        this.updateNumberOfDays();
      });
    });

    // Togle details links on click
    document.querySelectorAll('.label-details-show').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.target.classList.add('d-none');
        event.target.closest('div.list-group-item[data-stage]').querySelector('.label-details-hide').classList.remove('d-none');
      });
    });
    document.querySelectorAll('.label-details-hide').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.target.classList.add('d-none');
        event.target.closest('div.list-group-item[data-stage]').querySelector('.label-details-show').classList.remove('d-none');
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
    if(this.availableOptions.indexOf(this.selectEnd.value) <= startIndex) {
      let endIndex = startIndex + 4;
      if (endIndex > this.selectEnd.options.length - 1) {
        endIndex = this.selectEnd.options.length - 1;
      }
      this.selectEnd.value = this.availableOptions[endIndex];
    }

    // All stages situated after a "complete" stage must be disabled because
    // it's not possible to register for a period which includes days which are
    // already full.
    let disableNextStages = false;
    // Disable days which cannot be booked (cannot leave BEFORE arriving)
    Array.prototype.forEach.call(this.selectEnd.options, (option) => {
      option.disabled = option.index <= startIndex && !option.selected;

      let mustBeDisabled = option.disabled || disableNextStages;

      let listItem = modal.querySelector('div.list-group-item[data-stage="'+option.value+'"]');
      if (listItem != null) { // Because latest choice have been removed from the list
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
      }
    })

    this.updateNumberOfDays();
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
