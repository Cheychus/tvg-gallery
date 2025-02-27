import GalleryImage from "./galleryImage.js";

export class Gallery {
    galleryManager;
    mainContainer = document.getElementById('mainContainer');
    galleryContainer;
    folderNr = 1;
    galleryImageMap = new Map();
    selectedImages = new Set();

    /**
     * This class is responsible for managing GalleryImages
     * It will get all images corresponding to the folderNr (tab-1, tab-2) and render them in a gallery-container
     * This class is also responsible for communicating with database (CREATE, READ and DELETE images)
     *
     *  After the gallery has been initialised, the gallery container can easily be removed or appended
     * to the main container without need to reconstruct it
     * @param folderNr
     */
    constructor(folderNr) {
        this.folderNr = folderNr;
        this.createGalleryContainer();
    }

    /**
     * The Gallery Manager is responsible for initialisation
     * After this function is finished, all image data is fetched from the database
     * but some images may still need time to be loaded
     * @param galleryManager - responsible for managing this gallery
     * @returns {Promise<boolean>}
     */
    async init(galleryManager) {
        this.galleryManager = galleryManager;
        try {
            await this.renderGalleryImages(this.folderNr);
        } catch (error) {
            console.error("Error loading Gallery images: ", error.message);
        }
        return true;
    }

    /**
     * Creates a new gallery container, and appends it to the main Container
     */
    createGalleryContainer() {
        const div = document.createElement('div');
        div.id = 'image-gallery-' + this.folderNr;
        div.className = "w-full max-h-full pl-4 pr-2 py-4 flex flex-col gap-2 overflow-auto" +
            " scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar-w-2 scrollbar hover:scrollbar-thumb-slate-700" +
            " min-[480px]:grid min-[480px]:grid-cols-5 min-[480px]:auto-rows-auto";
        this.mainContainer.appendChild(div);
        this.galleryContainer = div;
    }

    /**
     * This will fetch all image data from the database with the corresponding folderNr
     * The data is used to create GalleryImages and add them to the gallery.
     * The gallery Images will create placeholders first but after onload is finished,
     * the thumbnail will be shown in the gallery
     * @param folderNr - used for collecting images from the database
     * @returns {Promise<Map<any, any>>} - GalleryImages are stored in a Map for easy and fast managing
     */
    async renderGalleryImages(folderNr) {
        this.folderNr = folderNr;
        const response = await this.getAllImages(folderNr);
        if (!response.ok) {
            throw new Error(`Failed to fetch Gallery images for ${folderNr}`);
        }
        const data = await response.json();
        if (Array.isArray(data)) {
            for (let i = 0; i < data.length; i++) {
                const image = data[i];
                const galleryImage = new GalleryImage(image);
                this.addImageToGallery(galleryImage);
            }
        } else {
            console.error('Unexpected data format: ', data)
        }
        return this.galleryImageMap;
    }

    /**
     * This will remove the gallery from the main container
     */
    hideGallery() {
        this.galleryContainer.remove();
    }

    /**
     * This will add this gallery to the main container
     */
    showGallery() {
        this.mainContainer.appendChild(this.galleryContainer);
    }

    /**
     * This will fetch all image data from the database
     * @param folderNr - only images from this folder will be fetched
     * @returns {Promise<Response>}
     */
    async getAllImages(folderNr) {
        return await fetch(`/api/images/${folderNr}`, {
            method: 'GET',
        });
    }

    /**
     * This will add an image file to the database
     * @param file - image
     * @returns {Promise<any>}
     */
    async addImage(file) {
        if (!this.isSupportedFileType(file)) {
            alert('Nur JPG, PNG, GIF, AVIF, WEBP sind unterstützt');
            return false;
        }

        const formData = new FormData();
        formData.append('file', file);
        const response = await fetch(`/api/image/${this.folderNr}`, {
            method: 'POST',
            body: formData,
        });
        if (response.ok) {
            return await response.json();
        } else {
            console.error('Error adding image: ', await response.text());
        }
    }

    /**
     * Check if file type is supported otherwise return false
     * @param file - upload file
     * @returns {boolean}
     */
    isSupportedFileType(file) {
        const supportedTypes = ['image/jpeg', 'image/png', 'image/gif',
            'image/webp', 'image/avif', 'image/webp', 'image/svg+xml', 'image/bmp'];
        return supportedTypes.includes(file.type);
    }

    /**
     * This will delete all selected images with one request
     * @returns {Promise<void>}
     */
    async deleteImages(){
        console.log(this.selectedImages);
        const images = this.selectedImages;
        const selectedGalleryImages = [];
        images.forEach((imageId) => {
            const galleryImage = this.galleryImageMap.get(Number(imageId));
            galleryImage.hide(true);
            selectedGalleryImages.push(galleryImage);
        });
        this.galleryManager.setCounter(0);

        const jsonData = JSON.stringify({ imageIds: Array.from(images) });
        fetch(`/api/images`, {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: jsonData,
        })
        .then(res => res.json())
        .then(data => {
            selectedGalleryImages.forEach(image => {
                this.removeImageFromGallery(image);
                this.selectedImages.delete(image.id);
            });
            console.log('Bilder entfernt', data);
        })
        .catch(err => console.log("Error: ", err));
    }


    /**
     * Remove an image container from the gallery
     * @param galleryImage
     */
    removeImageFromGallery(galleryImage) {
        galleryImage.imageContainer.remove();
    }

    /**
     * Use this function to add a GalleryImage to the galleryContainer.
     * @param galleryImage - class object GalleryImage is needed
     * @returns {*}
     */
    addImageToGallery(galleryImage) {
        const imageContainer = galleryImage.imageContainer;
        this.galleryContainer.appendChild(imageContainer);
        this.galleryImageMap.set(galleryImage.id, galleryImage);
        return imageContainer;
    }


}