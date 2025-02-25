<?php
namespace controllers;

use JetBrains\PhpStorm\NoReturn;

class SiteController
{
    const string VIEW_PATH = __DIR__ . '/../views/';

    /**
     * Home Page
     * @return void
     */
    #[NoReturn] public function home(): void
    {
        require self::VIEW_PATH . 'home.html';
        exit();
    }

    /**
     * Gallery Page
     * @return void
     */
    #[NoReturn] public function gallery(): void
    {
        require self::VIEW_PATH . 'gallery.php';
        exit();
    }



}
