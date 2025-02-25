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
        this.pathOriginal = imageData.pathOriginal;
        this.width = imageData.width;
        this.height = imageData.height;
        this.ratio = imageData.ratio;

        this.createImageContainer();
    }


    /**
     * This will create a placeholder that can be used when a gallery is initialised to give user feedback on loading images
     * @returns {HTMLDivElement} - add this to a gallery container
     */
     static createPlaceholder(){
        const placeholderDiv = document.createElement("div");
        placeholderDiv.className = "w-full aspect-square rounded-md bg-tvg-gray flex justify-center items-center opacity-50";
        placeholderDiv.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`;
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
        image.loading = "eager";
        image.className = "thumbnail w-full aspect-square rounded-md object-cover cursor-pointer";
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
        this.checkBox.className = "hidden absolute top-2 left-2 w-5 h-5 rounded-full bg-gray-300 border-gray-300 cursor-pointer";
        this.imageContainer.appendChild(this.checkBox);


        this.imageContainer.appendChild(this.placeholder);
    }

    /**
     * Add or remove the image container checkbox
     * @param on
     */
    toggleCheckbox(on){
        if(on){
            this.checkBox.classList.remove('hidden');
        }else {
            this.checkBox.classList.add('hidden');
        }
    }

    /**
     * Hide or show the image-container
     * This is useful when deleting images, so that the user gets immediate feedback on deleted files, before the request is done
     * @param on
     */
    hide(on){
        if(on){
            this.imageContainer.classList.add('hidden');
        }else{
            this.imageContainer.classList.remove('hidden');
        }

    }

    /**
     * Just a getter for the image id
     * @returns {number}
     */
    getId(){
        return Number(this.id);
    }


}