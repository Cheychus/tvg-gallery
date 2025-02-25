<?php

namespace controllers;

use JetBrains\PhpStorm\NoReturn;
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
    #[NoReturn] public function getImages($params): void
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
    #[NoReturn] public function uploadImage($params): void
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
        $image = Image::constructFromUploadData($data, $tmpName);
        $id = $this->imageRepository->addImage($image);
        $image->id = $id;
        http_response_code(200);
        echo $image->toJson();
        exit();
    }

    /**
     * Delete one image by its id
     * @param $params
     * @return void
     */
    #[NoReturn] public function deleteImage($params): void
    {
        $id = $params['id'];
        $image = $this->imageRepository->getImageById($id);
        $response = $image->deleteImageData();

        if($response['success']){
            $this->imageRepository->deleteImage($id);
            http_response_code(200);
        } else {
            http_response_code(404);
        }
        echo json_encode($response);
        exit();
    }

}