<?php

namespace controllers;

use Exception;
use models\Database;
use models\Image;
use models\ImageRepository;

class ApiController
{
    private ImageRepository $imageRepository;

    /**
     * Handle incoming API Requests from the router
     */
    public function __construct(){
        $pdo = Database::getInstance();
        $this->imageRepository = new ImageRepository($pdo);
    }


    /**
     * Get all images from one image folder
     * @param $params
     * @return void
     */
    public function getImages($params): void
    {
        $folderNr = $params['id'];
        $images = $this->imageRepository->getImages($folderNr);

        $data = [];
        foreach($images as $image){
             $data[] = json_decode($image->toJson());
        }
        http_response_code(200);
        echo json_encode($data);
        exit();
    }

    /**
     * Upload Image Files ($_FILES) to one image folder
     * @param $params
     * @return void
     */
    public function uploadImage($params): void
    {
        $folderNr = $params['id'];
        $file = $_FILES['file'];
        $name = $file['name'];
        $type = $file['type']; // zb image/jpeg
        $size = $file['size']; // in Bytes
        $tmpName = $file['tmp_name']; // Upload File Path
        $data = [
            'fk_folder' => $folderNr,
            'name' => $name,
            'type' => $type,
            'size' => $size,
        ];
        try{
            $image = Image::constructFromUploadData($data, $tmpName);
            $id = $this->imageRepository->addImage($image);
            $image->id = $id;
            http_response_code(200);
            echo $image->toJson();
            exit();
        } catch (Exception $e){
            http_response_code(500);
            echo "Fehler beim hochladen des Bildes: " . $e->getMessage();
            exit();
        }

    }


    /**
     * Delete multiple images by its ids with one request
     * @return void
     */
    public function deleteImages(): void
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        $imageIds = $data['imageIds'] ?? [];
        if (empty($imageIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'No image IDs provided']);
            return;
        }
        $response = [];
        foreach ($imageIds as $imageId) {
            $image = $this->imageRepository->getImageById($imageId);
            $success = $image->deleteImageData();
            if($success){
                $this->imageRepository->deleteImage($imageId);
                $response[] = $success;
            }
        }
        http_response_code(200);
        echo json_encode($response);
    }

}