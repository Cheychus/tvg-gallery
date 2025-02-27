export default class GalleryImage {

    checkBox;
    imageContainer;
    imageElement;

    /**
     * This class represent an image that can be loaded in the gallery object
     * It will contain data about different image paths, id corresponding to the database id and original image data
     * After construction, this.imageContainer can be used in gallery
     * @param imageData - initialised mainly with database data
     */
    constructor(imageData) {
        this.id = imageData.id;
        this.name = imageData.name;
        this.pathThumb = imageData.pathThumb;
        this.pathPreview = imageData.pathPreview;
        this.pathLow = imageData.pathLow;
        this.width = imageData.width;
        this.height = imageData.height;
        this.ratio = imageData.ratio;

        this.createImageContainer();
    }


    /**
     * This will create a placeholder that can be used when a gallery is initialised to give user feedback on loading images
     * @returns {HTMLDivElement} - add this to a gallery container
     */
    static createPlaceholder() {
        const placeholderDiv = document.createElement("div");
        placeholderDiv.className = "w-full aspect-square rounded-md bg-tvg-gray flex justify-center items-center animate-pulse";
        placeholderDiv.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>`;
        return placeholderDiv;
    }

    /**
     * This will create an image-container, that can be used in gallery
     * First it will be filled with a placeholder and after image.onload is fired,
     * placeholder will be replaced with thumbnail picture
     * The thumbnail picture will be loaded with a lazy loading event, controlled by IntersectionObserver
     * The data-src attribut contains a preview image, that can be used for the overlay
     * The container also contains a checkbox, that can be used for selection
     * image.id contains the corresponding database id
     */
    createImageContainer() {
        this.imageContainer = document.createElement("div");
        this.imageContainer.className = "image-container relative w-full aspect-square";

        this.placeholder = GalleryImage.createPlaceholder();
        const image = new Image();
        image.setAttribute("data-src", this.pathPreview);
        image.setAttribute("data-src-low", this.pathLow);
        image.loading = "eager";
        image.className = "bg-tvg-gray/60 thumbnail w-full aspect-square rounded-md object-cover cursor-pointer";
        image.alt = "Bild";
        image.dataset.id = this.id;
        image.addEventListener("load", () => {
            this.placeholder.replaceWith(image);
        })
        this.imageElement = image;

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    image.src = this.pathThumb;
                    image.addEventListener("load", () => {
                        this.placeholder.replaceWith(image);
                    });
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(this.imageContainer);

        // checkbox for selection
        this.checkBox = document.createElement("input");
        this.checkBox.type = "checkbox";
        this.checkBox.className = "hidden absolute top-2 left-2 w-5 h-5 bg-gray-300 border-gray-300";
        this.imageContainer.appendChild(this.checkBox);


        this.imageContainer.appendChild(this.placeholder);
    }


    /**
     * Add or remove the image container checkbox
     * @param on
     */
    toggleCheckbox(on) {
        if (on) {
            this.checkBox.classList.remove('hidden');
        } else {
            this.checkBox.classList.add('hidden');
        }
    }

    /**
     * Hide or show the image-container
     * This is useful when deleting images, so that the user gets immediate feedback on deleted files, before the request is done
     * @param on
     */
    hide(on) {
        if (on) {
            this.imageContainer.classList.add('hidden');
        } else {
            this.imageContainer.classList.remove('hidden');
        }

    }

    /**
     * Just a getter for the image id
     * @returns {number}
     */
    getId() {
        return Number(this.id);
    }


}