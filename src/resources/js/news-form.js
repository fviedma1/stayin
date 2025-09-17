document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#images');
    const thumbnailContainer = document.querySelector('.thumbnail-container');
    const mainPreview = document.querySelector('.main-preview-image');
    const placeholder = document.querySelector('.preview-placeholder');

    if (!input || !thumbnailContainer || !mainPreview || !placeholder) return;

    input.addEventListener('change', (e) => {
        thumbnailContainer.innerHTML = '';
        placeholder.style.display = 'block';
        mainPreview.style.display = 'none';

        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (event) => {
                const thumbnailItem = document.createElement('div');
                thumbnailItem.classList.add('thumbnail-item');

                const thumbnailImage = document.createElement('img');
                thumbnailImage.src = event.target.result;
                thumbnailImage.classList.add('thumbnail-image');

                const fileName = document.createElement('div');
                fileName.textContent = file.name;
                fileName.classList.add('file-name');

                thumbnailItem.appendChild(thumbnailImage);
                thumbnailItem.appendChild(fileName);
                thumbnailContainer.appendChild(thumbnailItem);

                if (index === 0) thumbnailItem.click();
            };
            reader.readAsDataURL(file);
        });
    });

    thumbnailContainer.addEventListener('click', (event) => {
        const clickedThumbnail = event.target.closest('.thumbnail-item');
        if (!clickedThumbnail) return;

        document.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('selected'));
        clickedThumbnail.classList.add('selected');

        mainPreview.src = clickedThumbnail.querySelector('img').src;
        mainPreview.style.display = 'block';
        placeholder.style.display = 'none';
    });
});