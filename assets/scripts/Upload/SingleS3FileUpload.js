import S3FileUploader from './S3FileUploader.js';

class SingleS3FileUpload {
  constructor(input) {
    this.input = input;
    this.originalInput = document.querySelector(input.dataset.originalInput);
    this.image = document.querySelector(input.dataset.image);
    this.loader = document.querySelector(input.dataset.loader);

    this.image.addEventListener('load', (event) => {
        this.image.classList.remove('d-none');
        this.loader.classList.add('d-none');
    });

    this.s3fileUploader = new S3FileUploader(this.originalInput.attributes.getNamedItem('data-sign-url').value);
  }

  onFileChange(event) {
    event.preventDefault();

    this.image.classList.add('d-none');
    this.loader.classList.remove('d-none');

    const file = event.target.files[0];

    // First query: retrieve a signed URL from our server
    this.s3fileUploader.upload(file).then((data) => {
      this.originalInput.value = data.uploaded_image_id;
      this.image.src = data.uploaded_image_url;
    });
  }
}

// When a remove button is clicked, its grand-parent (the "li" containing the div container) is deleted.
document.addEventListener('click', () => {
  const target = event.target.closest('.s3-image-upload-container button.delete-button');

  if (target) {
      event.preventDefault();

      event.target.parentNode.parentNode.remove();
  }
});

document.addEventListener('change', (event) => {
  const target = event.target.closest('input[type=file].s3-image-upload');

  if (target) {
    const fileUpload = new SingleS3FileUpload(target);

    fileUpload.onFileChange(event);
  }
});
