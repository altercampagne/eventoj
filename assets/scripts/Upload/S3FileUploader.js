class S3FileUploader {
  constructor(dataSignUrl) {
    this.dataSignUrl = dataSignUrl;
  }

  signUrl(file) {
    const _URL = window.URL || window.webkitURL;
    const objectUrl = _URL.createObjectURL(file);

    const img = document.createElement('img');

    const promise = new Promise((resolve, reject) => {
      console.log('in promise');
      img.onload = () => {
        console.log('img onload');
        resolve(fetch(this.dataSignUrl, {
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
            width: img.naturalWidth,
            height: img.naturalHeight,
          }),
        })
          .then((response) => response.json())
        );
      };
    });

    img.src = objectUrl;

    return promise;
  }

  upload(file) {
    return this.signUrl(file).then((data) => {
      // Send file to AWS
      return fetch(decodeURI(data.presigned_url), {
        method: 'PUT',
        body: file,
        headers: {
          'x-amz-acl': 'public-read',
          'Content-Type': file.type != '' ? file.type : 'application/octet-stream',
        },
      })
        .then(() => {
          return data;
        });
    });
  }
}

export default S3FileUploader;
