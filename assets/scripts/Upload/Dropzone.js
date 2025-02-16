import Dropzone from 'dropzone';
import S3FileUploader from './S3FileUploader.js';
import 'dropzone/dist/dropzone.css';

// @See https://stackoverflow.com/questions/39017087/how-to-implement-dropzone-js-to-upload-file-into-amazon-s3-server
class CustomDropzone {
  constructor(element) {
    const s3FileUploader = new S3FileUploader(document.querySelector('div.dropzone').dataset.signUrl);

    let dropzone = new Dropzone('div.dropzone', {
      url: '/', // The URL will be changed for each new file being processing
      method: 'put',
      // Hijack the xhr.send since Dropzone always upload file by using formData
      // ref: https://github.com/danialfarid/ng-file-upload/issues/743
      sending (file, xhr) {
        let _send = xhr.send
        xhr.send = () => {
          _send.call(xhr, file)
        }
      },
      acceptedFiles: 'image/*',
      parallelUploads: 3,
      headers: {
        'x-amz-acl': 'public-read',
      },
      dictInvalidFileType: "Ce type de fichier n'est pas valide.",

      accept: (file, done) => {
        s3FileUploader.signUrl(file).then((data) => {
          file.uploadURL = decodeURI(data.presigned_url);
          done();
        });
      },
    });
    dropzone.on('processing', (file) => {
      dropzone.options.url = file.uploadURL;
    })
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('div.dropzone').forEach((element) => {
    (() => new CustomDropzone(element))();
  });
});
