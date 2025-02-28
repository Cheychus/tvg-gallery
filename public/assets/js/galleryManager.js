import {Gallery} from "./gallery.js";
import GalleryImage from "./galleryImage.js";

export class GalleryManager {
    counter = document.getElementById("select-count");
    selectAll = document.getElementById("select-all");
    toggleButton = document.getElementById('toggle-select');
    overlay = document.getElementById("imageOverlay");
    overlayImage = document.getElementById("overlayImage");
    leftArrow = document.getElementById("arrow-left");
    rightArrow = document.getElementById("arrow-right");
    activeTab = document.getElementById("tab-1");
    loadingSpinner = document.getElementById("loading-spinner");
    isSelectionMode = false;
    activeGalleryImageMap;
    overlayImageId;
    activeOverlay = false;

    /**
     * This class is for managing multiple gallery's and switching between them
     * The manager will set up Events and Elements such as Upload-Button, tabs and Overlay
     */
    constructor() {
        this.galleries = new Map();
        this.activeGallery = null;
        this.setupTabChangeEvent();
        this.setupUploadButton();
        this.setupSelectAllButton();
        this.setupToggleSelectionEvent();
        this.setupOverlayEvent();
    }

    /**
     * Switch to a different Gallery and render their images
     * @param id - gallery number
     * @returns {Promise<void>}
     */
    async switchToGalleryFolder(id) {
        if (this.activeGallery !== null) {
            this.activeGallery.hideGallery();
        }
        if (!this.galleries.has(id)) {
            const gallery = new Gallery(id);
            await gallery.init(this);
            this.galleries.set(id, gallery);

            console.log('Gallery initialisiert: ', id);
        }
        this.activeGallery = this.galleries.get(id);
        this.activeGallery.showGallery();
        this.activeGalleryImageMap = this.activeGallery.galleryImageMap;

        this.updateCounter();
        this.toggleCheckBoxes();


        this.setupImageContainerEvent();
    }


    /**
     * Setup Toggle Selection event. This will enable selecting images after change event
     */
    setupToggleSelectionEvent() {
        this.toggleButton.addEventListener('change', () => {
            this.isSelectionMode = !this.isSelectionMode;
            if (!this.isSelectionMode) {
                this.deactivateSelectionMode();
            } else {
                this.activateSelectionMode();
            }

            this.toggleCheckBoxes();

            return true;
        });
    }

    /**
     * This will add or remove checkboxes for all images in all galleries
     */
    toggleCheckBoxes() {
        this.galleries.forEach(gallery => {
            gallery.galleryImageMap.forEach(galleryImage => {
                galleryImage.toggleCheckbox(this.isSelectionMode);
            });
        });
    }

    /**
     * Enable or disable all checkboxes in all galleries
     * @param gallery
     * @param on
     */
    checkAllCheckboxes(gallery, on) {
        if (on) {
            gallery.galleryImageMap.forEach(galleryImage => {
                if (!galleryImage.checkBox.checked) {
                    galleryImage.checkBox.checked = true;
                    gallery.selectedImages.add(galleryImage.id);
                }
            })
        } else {
            gallery.selectedImages.clear();
            gallery.galleryImageMap.forEach(galleryImage => {
                galleryImage.checkBox.checked = false;
            });
        }
        this.updateCounter();
    }

    /**
     * Change selection text next to the trash bin, for visual recognition
     * This will also uncheck all checkboxes and reset the counter
     */
    deactivateSelectionMode() {
        this.counter.classList.remove("text-white");
        this.counter.classList.add("text-tvg-blue-800");
        this.selectAll.classList.remove("text-white");
        this.selectAll.classList.remove("hover:text-tvg-blue-100");
        this.selectAll.classList.add("text-tvg-blue-800");
        this.selectAll.classList.remove("cursor-pointer");
        this.selectAll.disabled = true;
        this.galleries.forEach(gallery => {
            this.checkAllCheckboxes(gallery, false);
        })


        this.updateCounter();
    }

    /**
     * Change selection text next to the trash bin, for visual recognition
     */
    activateSelectionMode() {
        this.counter.classList.remove("text-tvg-blue-800");
        this.counter.classList.add("text-white");
        this.selectAll.disabled = false;
        this.selectAll.classList.remove("text-tvg-blue-800");
        this.selectAll.classList.add("text-white");
        this.selectAll.classList.add("cursor-pointer");
        this.selectAll.classList.add("hover:text-tvg-blue-100");
    }


    /**
     * Overlay and arrows will be hidden and opacity reset
     */
    leaveOverlay() {
        this.overlay.classList.add("hidden");
        this.overlayImage.src = "";
        this.overlayImage.classList.remove('opacity-100')
        this.overlayImage.classList.add('opacity-0')
        this.leftArrow.classList.add("hidden");
        this.rightArrow.classList.add("hidden");
        this.activeOverlay = false;
    }

    /**
     * Calculate the previous key in the image Map and load previous image in the overlay
     * @param key - ImageMap key
     * @returns {null}
     */
    previousGalleryImage(key) {
        const keys = Array.from(this.activeGalleryImageMap.keys());
        const index = keys.indexOf(key);
        if (index === -1) return null;
        const previousKey = keys[(index - 1 + keys.length) % keys.length];
        const prevGalleryImage = this.activeGalleryImageMap.get(previousKey);
        this.loadSelectedImageInOverlay(prevGalleryImage.getId());
    }

    /**
     * Calculate the next key in the image Map and load next image in the overlay
     * @param key - ImageMap key
     * @returns {null}
     */
    nextGalleryImage(key) {
        const keys = Array.from(this.activeGalleryImageMap.keys());
        const index = keys.indexOf(key);
        if (index === -1) return null;
        const nextKey = keys[(index + 1) % keys.length];
        const nextGalleryImage = this.activeGalleryImageMap.get(nextKey);
        this.loadSelectedImageInOverlay(nextGalleryImage.getId());
    }


    /**
     * Get the galleryImage Object from the active Gallery and load preview image in overlay
     * @param imageId - corresponding to image map key
     */
    loadSelectedImageInOverlay(imageId) {
        this.overlayImageId = Number(imageId);
        const galleryImage = this.activeGalleryImageMap.get(Number(imageId));
        this.showLoadingSpinner(true);
        this.overlayImage.classList.remove('opacity-100');
        this.overlayImage.classList.add('opacity-0');

        const preloadImg = new Image();
        preloadImg.src = galleryImage.pathPreview;

        if (preloadImg.complete) {
            this.overlayImage.src = preloadImg.src;
            this.showLoadingSpinner(false);
            this.overlayImage.classList.remove('opacity-0');
            this.overlayImage.classList.add('opacity-100');
            this.leftArrow.classList.remove('hidden');
            this.rightArrow.classList.remove('hidden');
        } else {
            // Low Quality image first
            this.overlayImage.src = galleryImage.pathLow;
            this.overlayImage.onload = () => {
                if (this.overlayImageId !== Number(imageId)) {
                    return;
                }
                this.showLoadingSpinner(false);
                this.overlayImage.classList.remove('opacity-0');
                this.overlayImage.classList.add('opacity-100');
                this.leftArrow.classList.remove('hidden');
                this.rightArrow.classList.remove('hidden');
            }

            preloadImg.onload = () => {
                if (this.overlayImageId !== Number(imageId)) {
                    return;
                }
                this.overlayImage.src = preloadImg.src;
                this.leftArrow.classList.remove('hidden');
                this.rightArrow.classList.remove('hidden');
            }
        }


    }


    /**
     * Refresh counter value with current selected images array size
     */
    updateCounter() {
        this.setCounter(this.activeGallery.selectedImages.size);
    }

    /**
     * Set a new Value for selection counter
     * @param count - new value
     */
    setCounter(count) {
        this.counter.textContent = `${count} ausgewählt`;
    }


    /**
     * Enable or disable the overlay loading spinner
     * @param on
     */
    showLoadingSpinner(on) {
        if (!on) {
            this.loadingSpinner.classList.add('hidden');
        } else {
            this.loadingSpinner.classList.remove('hidden');
        }

    }

    /**
     * Set up the overlay Event
     * Click => leave and close overlay
     * Arrows => Click to load next or previous image in the overlay
     * Keys => Left and right Arrow are also for navigation
     * Esc => also leave the overlay
     */
    setupOverlayEvent() {
        this.overlay.addEventListener("click", () => {
            this.leaveOverlay();
        });

        this.leftArrow.addEventListener("click", (event) => {
            event.stopPropagation();
            this.previousGalleryImage(this.overlayImageId);
        });

        this.rightArrow.addEventListener("click", (event) => {
            event.stopPropagation();
            this.nextGalleryImage(this.overlayImageId);
        });

        document.addEventListener("keydown", (event) => {
            if (!this.activeOverlay) {
                return;
            }
            if (event.key === "ArrowLeft") {
                this.previousGalleryImage(this.overlayImageId);
            } else if (event.key === "ArrowRight") {
                this.nextGalleryImage(this.overlayImageId);
            } else if (event.key === "Escape") {
                this.leaveOverlay();
            }

        })
    }


    /**
     * This will handle clicks in the image Gallery container
     * If in selection mode => select image and add it to selection array
     * If in normal mode => open Image Overlay and load preview image in overlay
     * Setup will only be once
     */
    setupImageContainerEvent() {
        if (this.activeGallery.isEventSetup) {
            return;
        }
        this.activeGallery.isEventSetup = true;

        this.activeGallery.galleryContainer.addEventListener("click", (event) => {
            const imageContainer = event.target.closest(".image-container");
            if (!imageContainer) return;

            const image = imageContainer.querySelector("img");
            if (!image) return;

            if (this.isSelectionMode) {
                const checkbox = imageContainer.querySelector("input");
                const imageId = image.dataset.id;
                if (event.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
                if (checkbox.checked) {
                    this.activeGallery.selectedImages.add(Number(imageId));
                } else {
                    this.activeGallery.selectedImages.delete(Number(imageId));
                }
                this.updateCounter();
                return;
            }

            // OVERLAY EVENT
            this.activeOverlay = true;
            const imageId = event.target.getAttribute("data-id");
            console.log('id clicked: ', imageId);
            this.overlayImageId = Number(imageId);
            this.loadSelectedImageInOverlay(imageId);

            if (!this.isSelectionMode) {
                this.overlay.classList.remove("hidden");
            }
        });
    }

    /**
     * Change tab css when changing the gallery folder
     */
    setupTabChangeEvent() {
        const inactiveCSS = "tab px-1 sm:px-4 text-tvg-blue-200 text-xs sm:text-sm hover:text-tvg-blue-100 cursor-pointer";
        const activeCSS = "tab px-1 sm:px-4 text-white text-xs sm:text-sm cursor-pointer";
        const tabs = document.querySelectorAll(".tab");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                this.activeTab.className = inactiveCSS;
                this.activeTab = tab;
                tab.className = activeCSS;
            })
        })
    }


    /**
     * Setup Upload-Button for managing multiple file uploads
     */
    setupUploadButton() {
        document.getElementById('uploadButton').addEventListener('click', () => {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', async (event) => {
            const files = event.target.files;
            const cleanFiles = [];
            const placeholders = [];
            const currentGallery = this.activeGallery;
            if (files.length > 0) {
                for (const file of files) {
                    if (!currentGallery.isSupportedFileType(file)) {
                        console.log(file.type + " is not supported");
                        continue;
                    }
                    cleanFiles.push(file);
                    const placeholder = GalleryImage.createPlaceholder();
                    placeholders.push(placeholder);
                    currentGallery.galleryContainer.appendChild(placeholder);
                }

                for (let i = 0; i < cleanFiles.length; i++) {
                    const imageData = await currentGallery.addImage(cleanFiles[i]);
                    if (!imageData) {
                        placeholders.at(i).remove();
                        continue;
                    }
                    const galleryImage = new GalleryImage(imageData);
                    galleryImage.toggleCheckbox(this.isSelectionMode);

                    const imageDiv = currentGallery.addImageToGallery(galleryImage);
                    placeholders.at(i).replaceWith(imageDiv);
                }

            }
            event.target.value = '';
        });

    }


    /**
     * Simply selecting all images in the gallery
     */
    setupSelectAllButton() {
        this.selectAll.addEventListener("click", () => {
            this.checkAllCheckboxes(this.activeGallery, true);
        })
    }
}