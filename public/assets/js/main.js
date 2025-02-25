import {GalleryManager} from "./galleryManager.js";

const galleryManager = new GalleryManager();
await galleryManager.switchToGalleryFolder(1);

document.getElementById('deleteButton').addEventListener('click', async function () {
    await galleryManager.activeGallery.deleteImages();
})

const folderButtons = document.querySelectorAll('.tab')
folderButtons.forEach((button) => {
    button.addEventListener('click', async function () {
        const id = button.dataset.id;
        await galleryManager.switchToGalleryFolder(Number(id));
    })
})

