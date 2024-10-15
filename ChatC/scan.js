document.addEventListener('paste', function(event) {
  const items = (event.clipboardData || event.originalEvent.clipboardData).items;
  for (let index in items) {
      const item = items[index];
      if (item.kind === 'file') {
          const blob = item.getAsFile();
          const reader = new FileReader();

          reader.onload = function(event) {
              const pastedImage = document.getElementById('pastedImage');
              pastedImage.src = event.target.result;
              pastedImage.style.display = 'block';  // Show the pasted image
          };

          reader.readAsDataURL(blob);
      }
  }
});

function scan() {
  const imageInput = document.getElementById('imageInput');
  const pastedImage = document.getElementById('pastedImage');
  const output = document.getElementById('output');
  
  // If an image is uploaded via the file input
  if (imageInput.files.length > 0) {
      const file = imageInput.files[0];
      const reader = new FileReader();

      reader.onload = function () {
          const image = new Image();
          image.src = reader.result;

          image.onload = function () {
              Tesseract.recognize(
                  image, 
                  'eng',
                  { logger: info => console.log(info) }  // Optional: shows OCR progress in console
              ).then(({ data: { text } }) => {
                  output.textContent = text;
              }).catch(err => {
                  output.textContent = 'Error recognizing text: ' + err;
              });
          };
      };

      reader.readAsDataURL(file);
  }
  // If an image is pasted from the clipboard
  else if (pastedImage.src) {
      const image = new Image();
      image.src = pastedImage.src;

      image.onload = function () {
          Tesseract.recognize(
              image, 
              'eng',
              { logger: info => console.log(info) }  // Optional: shows OCR progress in console
          ).then(({ data: { text } }) => {
              output.textContent = text;
          }).catch(err => {
              output.textContent = 'Error recognizing text: ' + err;
          });
      };
  }
  // If no image is provided
  else {
      output.textContent = 'Please upload or paste an image first!';
  }
}