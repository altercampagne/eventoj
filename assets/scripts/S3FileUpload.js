class S3FileUpload {
  constructor(input) {
    this.input = input;
    this.originalInput = document.querySelector(input.dataset.originalInput);
    this.image = document.querySelector(input.dataset.image);
    this.loader = document.querySelector(input.dataset.loader);

    this.input.addEventListener('change', this.onFileChange.bind(this));

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

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('input[type=file].s3-file-upload').forEach((element) => {
    (() => new S3FileUpload(element))();
  });
});
