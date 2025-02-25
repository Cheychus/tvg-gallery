<?php

namespace models;

use GdImage;

class Image
{
    public int|null $id {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
        }
    }
    public int|null $fk_folder {
        get {
            return $this->fk_folder;
        }
    }
    public string|null $name {
        get {
            return $this->name;
        }
    }
    public string|null $type {
        get {
            return $this->type;
        }
    }
    public string|null $size {
        get {
            return $this->size;
        }
    }
    public int|null $width {
        get {
            return $this->width;
        }
    }
    public int|null $height {
        get {
            return $this->height;
        }
    }
    public float|int|null $ratio {
        get {
            return $this->ratio;
        }
    }
    public string|null $pathOriginal {
        get {
            return $this->pathOriginal;
        }
    }
    public string|null $pathThumb {
        get {
            return $this->pathThumb;
        }
    }
    public string|null $pathPreview {
        get {
            return $this->pathPreview;
        }
    }

    /**
     * This class represents an Image constructed from database data
     * @param array $data - database image data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->fk_folder = $data['fk_folder'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->size = $data['size'] ?? null;
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
        $this->ratio = $data['ratio'] ?? null;
        $this->pathOriginal = $data['pathOriginal'] ?? null;
        $this->pathThumb = $data['pathThumb'] ?? null;
        $this->pathPreview = $data['pathPreview'] ?? null;
    }

    /**
     * This will construct a new Image from upload file data
     * It will generate different images for different sizes
     * @param array $data - upload data
     * @param string $originalImage - original image name
     * @return self - new Image
     */
    public static function constructFromUploadData(array $data, string $originalImage): self
    {
        $image = new self($data);

        $newName = uniqid() . '.webp';
        $uploadPath = 'uploads/folder_' . $image->fk_folder . '/';
        $image->pathOriginal = $uploadPath . 'original_' . $newName;
        $image->pathPreview = $uploadPath . 'preview_' . $newName;
        $image->pathThumb = $uploadPath . 'thumb_' . $newName;

        // Convert Image to thumbnail, preview and add width, height
        $gdImage = self::createImage($originalImage);
        $originalWidth = imagesx($gdImage);
        $originalHeight = imagesy($gdImage);
        $aspectRatio = $originalWidth / $originalHeight;
        $image->width = $originalWidth;
        $image->height = $originalHeight;
        $image->ratio = $aspectRatio;

        $previewImage = imagescale($gdImage, 1080);
        $thumbnailImage = imagescale($gdImage, 500);
        imagewebp($thumbnailImage, $image->pathThumb, 80);
        imagewebp($previewImage, $image->pathPreview, 100);
        imagedestroy($gdImage);

        return $image;
    }

    /**
     * This will handle different image file types and outputs a GdImage for further image handling
     * @param $file - image File (jpg, png ...)
     * @return GdImage|false
     */
    private static function createImage($file): GdImage|false
    {
        $fileType = mime_content_type($file);
        switch ($fileType) {
            case 'image/png':
                $image = imagecreatefrompng($file);
                break;
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            default:
                echo "File type not supported";
                return false;
        }
        return $image;
    }

    /**
     * This will remove this image paths from the server
     * @return array
     */
    public function deleteImageData(): array
    {
        $pathThumb = $this->pathThumb;
        $pathPreview = $this->pathPreview;
        // $pathOriginal = $this->pathOriginal;

        $deletedFiles = [];
        $errors = [];

        foreach ([$pathThumb, $pathPreview] as $path) {
            if ($path && file_exists($path)) {
                if (unlink($path)) {
                    $deletedFiles[] = $path;
                } else {
                    $errors[] = "Konnte Datei nicht löschen: $path";
                }
            } else {
                $errors[] = "Datei nicht gefunden: $path";
            }
        }

        if (empty($errors)) {
            return ['success' => true, 'deleted' => $deletedFiles];
        } else {
            return ['success' => false, 'errors' => $errors];
        }
    }

    public function toJson(): string
    {
        return json_encode([
            'id' => $this->id,
            'pathOriginal' => $this->pathOriginal,
            'pathThumb' => $this->pathThumb,
            'pathPreview' => $this->pathPreview,
            'width' => $this->width,
            'height' => $this->height,
            'ratio' => $this->ratio,
        ]);
    }


}