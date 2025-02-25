<?php

namespace models;

use PDO;

class ImageRepository
{

    private PDO $pdo;

    /**
     * This class is managing the communication with the database and is responsible for
     * adding, deleting and getting images
     * @param $pdo - database object
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * This will select every image data corresponding to the folderId and return an array of new Images
     * @param $folderId
     * @return array|Image
     */
    public function getImages($folderId): array|Image
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Images WHERE fk_folder = ?");
        $stmt->execute([$folderId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $imageArray = [];
        foreach ($images as $image) {
            $imageArray[] = new Image($image);
        }
        return $imageArray;
    }

    /**
     * Use this to add an Image to the database
     * @param Image $image
     * @return int
     */
    public function addImage(Image $image): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO Images (
                    fk_folder,
                    name,
                    type,
                    size, 
                    width,
                    height,
                    ratio,
                    pathOriginal,
                    pathThumb,
                    pathPreview
                    ) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
                $image->fk_folder,
                $image->name,
                $image->type,
                $image->size,
                $image->width,
                $image->height,
                $image->ratio,
                $image->pathOriginal,
                $image->pathThumb,
                $image->pathPreview
            ]
        );
        return $this->pdo->lastInsertId();
    }

    /**
     * This will get an image by its id
     * @param int $id
     * @return Image|false
     */
    public function getImageById(int $id): Image | false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Images WHERE id = ?");
        $stmt->execute([$id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$data){
            return false;
        }
        return new Image($data);
    }

    /**
     * This will delete an image by its id
     * @param int $id
     * @return bool
     */
    public function deleteImage(int $id) : bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM Images WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }


}