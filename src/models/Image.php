<?php

namespace models;

use Exception;

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
    public string|null $pathLow {
        get {
            return $this->pathLow;
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
        $this->pathLow = $data['pathLow'] ?? null;
        $this->pathThumb = $data['pathThumb'] ?? null;
        $this->pathPreview = $data['pathPreview'] ?? null;
    }

    /**
     * This will construct a new Image from upload file data
     * It will generate different images for different sizes
     * @param array $data - upload data
     * @param string $originalImage - original image name
     * @return self - new Image
     * @throws Exception
     */
    public static function constructFromUploadData(array $data, string $originalImage): self
    {
        $image = new self($data);
        $fileType = $image->type;
        $fileSize = $image->size;

        $uploadPath = 'uploads/folder_' . $image->fk_folder . '/';
        if(!is_dir($uploadPath)) {
            if(!mkdir($uploadPath, 0755, true)){
                throw new Exception("Upload directory could not be created");
            }
        }
        $newName = uniqid();

        // First check size
        $sizeLimit = 1000; // in KB
        $shouldConvert = false;
        if ($fileSize > $sizeLimit * 1024) {
            $shouldConvert = true;
        }
        if($fileSize > 1000 * 50 * 1024){
            throw new Exception("File size must be less than 50MB");
        }

        // handle different file Types
        $image->pathThumb = $uploadPath . 'thumbnail_' . $newName;
        $image->pathLow = $uploadPath . 'low_' . $newName;
        $image->pathPreview = $uploadPath . 'preview_' . $newName;
        switch ($fileType) {
            case 'image/bmp':
                $gdImage = imagecreatefrombmp($originalImage);
                break;
            case 'image/png':
                $gdImage = imagecreatefrompng($originalImage);
                break;
            case 'image/webp':
                $gdImage = imagecreatefromwebp($originalImage);
                break;
            case 'image/avif':
                $gdImage = imagecreatefromavif($originalImage);
                break;
            case 'image/jpeg':
                $gdImage = imagecreatefromjpeg($originalImage);
                break;
            case 'image/gif':
                $gdImage = imagecreatefromgif($originalImage);
                // Gifs are tolerated in original form up to 1MB size. Thumbnail and Preview remain the same.
                if(!$shouldConvert) {
                    $image->pathThumb .= '.gif';
                    $image->pathLow = $image->pathThumb;
                    $image->pathPreview = $image->pathThumb;
                    copy($originalImage, $image->pathThumb);
                    return $image;
                }
            case 'image/svg+xml':
                // SVG cant be converted with gd Image so only small files are tolerated in original form.
                if(!$shouldConvert){
                    $image->pathThumb .= '.svg';
                    $image->pathLow = $image->pathThumb;
                    $image->pathPreview = $image->pathThumb;
                    copy($originalImage, $image->pathThumb);
                    copy($originalImage, $image->pathPreview);
                    return $image;
                }
                throw new Exception("SVG file is bigger than $sizeLimit Bytes. Please convert or upload smaller SVG files.");
            default:
                throw new Exception('File type not supported');
        }
        // scale down image if needed
        $originalWidth = imagesx($gdImage);
        $originalHeight = imagesy($gdImage);
        $aspectRatio = $originalWidth / $originalHeight;
        $image->width = $originalWidth;
        $image->height = $originalHeight;
        $image->ratio = $aspectRatio;
        $previewImage = $gdImage;
        $thumbnailImage = $gdImage;
        $lowQualityImage = $gdImage;
        if($originalWidth > 1920 || $originalHeight > 1920) {
            $lowQualityImage = imagescale($gdImage, 1920);
            $previewImage = imagescale($gdImage, 1920);
        }
        if($originalWidth > 500 || $originalHeight > 500) {
            $thumbnailImage = imagescale($gdImage, 500);
        }

        // create different images for different previews
        $extension = $shouldConvert ? '.webp' : self::getFileExtension($fileType);
        $image->pathThumb = $image->pathThumb . '.webp';
        $image->pathLow = $image->pathLow . '.webp';
        $image->pathPreview = $image->pathPreview . $extension;
        imagewebp($thumbnailImage, $image->pathThumb, 85);
        imagewebp($lowQualityImage, $image->pathLow, 0);

        // finally ouput the image in webp or original form when small enough
        $previewQuality = 85;
        switch ($extension) {
            case '.jpg':
                imagejpeg($previewImage, $image->pathPreview, $previewQuality);
                break;
            case '.png':
                imagepng($previewImage, $image->pathPreview, $previewQuality);
                break;
            case '.gif':
                imagegif($previewImage, $image->pathPreview);
                break;
            case '.webp':
                imagewebp($previewImage, $image->pathPreview, $previewQuality);
                break;
            case '.avif':
                imageavif($previewImage, $image->pathPreview, $previewQuality);
                break;
            case '.svg':
                if($shouldConvert){
                    imagedestroy($gdImage);
                    throw new Exception('SVG file is too big');
                }
                copy($originalImage, $image->pathPreview);
                break;
            case '.bmp':
                imagebmp($previewImage, $image->pathPreview);
                break;
            default:
                imagedestroy($gdImage);
                throw new Exception('File type not supported');
        }
        imagedestroy($gdImage);
        ob_end_clean();
        return $image;
    }

    /**
     * Convert the mime type to a file extension format
     * @param string $fileType
     * @return string
     */
    private static function getFileExtension(string $fileType): string
    {
        return match ($fileType) {
            'image/avif' => '.avif',
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/svg+xml' => '.svg',
            'image/bmp' => '.bmp',
            default => '.webp', // Fallback
        };
    }


    /**
     * This will remove this image paths from the server
     * @return array
     */
    public function deleteImageData(): array
    {
        $pathThumb = $this->pathThumb;
        $pathPreview = $this->pathPreview;
        $pathLow = $this->pathLow;
        $deletedFiles = [];
        if(file_exists($pathThumb)) {
            $deletedFiles[] = unlink($pathThumb);
        }
        if(file_exists($pathLow)) {
            $deletedFiles[] = unlink($pathLow);
        }
        if(file_exists($pathPreview)) {
            $deletedFiles[] = unlink($pathPreview);
        }
        if(empty($deletedFiles)) {
            return ['success' => false, 'errors' => "Could not delete image data."];
        }else{
            return ['success' => true, 'deleted' => $deletedFiles];
        }
    }

    public function toJson(): string
    {
        return json_encode([
            'id' => $this->id,
            'pathLow' => $this->pathLow,
            'pathThumb' => $this->pathThumb,
            'pathPreview' => $this->pathPreview,
            'width' => $this->width,
            'height' => $this->height,
            'ratio' => $this->ratio,
        ]);
    }


}