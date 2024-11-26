class S3FileUpload {
  constructor(input) {
    this.input = input;
    this.originalInput = document.querySelector(input.dataset.originalInput);
    this.image = document.querySelector(input.dataset.image);
    this.loader = document.querySelector(input.dataset.loader);

    this.image.addEventListener('load', (event) => {
        this.image.classList.remove('d-none');
        this.loader.classList.add('d-none');
    });
  }

  onFileChange(event) {
    event.preventDefault();

    this.image.classList.add('d-none');
    this.loader.classList.remove('d-none');

    const file = event.target.files[0];

    // First query: retrieve a signed URL from our server
    fetch(this.originalInput.attributes.getNamedItem('data-sign-url').value, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest', // Header needed for symfony to known it's an AJAX request
      },
      body: JSON.stringify({
        filename: file.name,
        last_modified: file.lastModified,
        size: file.size,
        type: file.type,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        // Send file to AWS
        fetch(decodeURI(data.presigned_url), {
          method: 'PUT',
          body: file,
          headers: {
            'x-amz-acl': 'public-read',
            'Content-Type': file.type,
          },
        })
          .then(() => {
            this.originalInput.value = data.uploaded_file_id;
            this.image.src = data.uploaded_file_url;
          });
      });
  }
}

// When a remove button is clicked, its grand-parent (the "li" containing the div container) is deleted.
document.addEventListener('click', () => {
  const target = event.target.closest('.s3-file-upload-container button.delete-button');

  if (target) {
      event.preventDefault();

      event.target.parentNode.parentNode.remove();
  }
});

document.addEventListener('change', (event) => {
  const target = event.target.closest('input[type=file].s3-file-upload');

  if (target) {
    const fileUpload = new S3FileUpload(target);

    fileUpload.onFileChange(event);
  }
});
