<?php

namespace App\Controllers;
use App\Models\ImageModel;
use App\Models\CollectionModel;

class Image extends BaseController
{
    public function index()
    {
        // Grab all images from the database
        $imageModel = new ImageModel;
        $ids = $imageModel->getAllIds("Beautiful AI");
        $images = [];
        foreach ($ids as $id) {
            $image = [
                "id" => $id,
                "path" => $imageModel->getPathById($id)["path"], 
                "name" => $imageModel->getNameById($id), 
                "collection" => $imageModel->getCollById($id)["name"],
                "category" => $imageModel->getCatById($id)["name"]
            ];
            array_push($images, $image);
        }

        // compile data to be sent to view
        $data = [
            "images" => $images
        ];
        return view('Image/Image_Detail', $data);
    }

    public function post(){
        $request = \Config\Services::request();
        $imageModel = new ImageModel;
        $colModel = new CollectionModel;

        $id = $request->getVar("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $image = $imageModel->getImgByName($id);
        $collections = $colModel->getCollNames();
        $image = array_merge($image, ["collectionNames" => $collections]);

        return json_encode($image);
    }
}
