class StageEditForm {
  constructor(form) {
    this.descriptionBlock = form.querySelector('div#stage-description-block');
    this.typeInput = form.querySelector('#stage_form_type');
    this.typeInput.addEventListener('change', () => {
      this.updateDescriptionVisibility();
    });

    this.updateDescriptionVisibility();
  }

  // Toggle description visibility cause it's useless for before / after stages
  updateDescriptionVisibility() {
    if ('classic' == this.typeInput.value) {
      this.descriptionBlock.classList.remove('d-none');
    } else {
      this.descriptionBlock.classList.add('d-none');
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (null !== document.querySelector('form[name="stage_form"]')) {
    (() => new StageEditForm(document.querySelector('form[name="stage_form"]')))();
  };
});
