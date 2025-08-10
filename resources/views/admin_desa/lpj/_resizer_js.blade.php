<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('photos');
    const previewContainer = document.getElementById('image-preview-container');
    const imageInfo = document.getElementById('image-info');

    fileInput.addEventListener('change', function (event) {
        const files = event.target.files;
        if (!files.length) {
            return;
        }

        previewContainer.innerHTML = ''; // Kosongkan preview
        imageInfo.textContent = 'Mengoptimalkan gambar...';

        const resizedFiles = [];
        let processedCount = 0;

        for (const file of files) {
            resizeImage(file, 1024, 1024, function (resizedBlob) {
                const resizedFile = new File([resizedBlob], file.name, {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });
                resizedFiles.push(resizedFile);
                processedCount++;

                // Tampilkan preview untuk gambar ini
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.style.maxWidth = '100px';
                    imgElement.style.maxHeight = '100px';
                    imgElement.style.margin = '5px';
                    imgElement.style.borderRadius = '5px';
                    previewContainer.appendChild(imgElement);
                }
                reader.readAsDataURL(resizedBlob);

                // Jika semua file sudah diproses, update file input
                if (processedCount === files.length) {
                    const dataTransfer = new DataTransfer();
                    resizedFiles.forEach(f => dataTransfer.items.add(f));
                    fileInput.files = dataTransfer.files;
                    imageInfo.textContent = `${files.length} gambar berhasil dioptimalkan.`;
                }
            });
        }
    });

    function resizeImage(file, maxWidth, maxHeight, callback) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function () {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                ctx.canvas.toBlob((blob) => {
                    callback(blob);
                }, 'image/jpeg', 0.8); // Kualitas 80%
            }
        }
    }
});
</script>