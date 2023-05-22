<?php

namespace App\Controllers;
use App\Models\MenuModel;
use App\Models\BrandModel;
use App\Controllers\Navigation;
use Google\Service\CloudAsset\Asset;
use \HTMLPurifier_Config;
use \HTMLPurifier;

class Menu extends BaseController
{
    public function index()
    {
        $session = session();
        $menuModel = new MenuModel;
        $brandModel = new BrandModel;

        $ids = $menuModel->getCollumn("id", $session->get("brand_name"));

        $menuItems = [];

        foreach($ids as $id){
            array_push($menuItems, $menuModel->getMenuItem($id, assoc: true));
        }

        $data = [
            "menuItems" => $menuItems,
        ];

        return Navigation::renderNavBar("Menu Items", [true, "Menu Items"]) . view('Menu', $data) . Navigation::renderFooter();
    }

    public function post(){
        $menuModel = new MenuModel();
        $name = $this->request->getPost("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $menuItem = $menuModel->getMenuItem($name, fetchBy: "title", assoc: true);

        return json_encode($menuItem);
    }

    public function update(){
        //TODO: handle file Uploads
        $menuModel = new MenuModel();
        if (count($_POST) > 0){
            $post = $this->request->getPost(["id", "title", "sequence", "target"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                "title" => $post["title"],
                "sequence" => $post["sequence"],
                "target" => $post["target"]
            ];

            if ($data["target"] == "1"){
                $link = $this->request->getPost("link", FILTER_SANITIZE_URL);
                $data["link"] = $link;
                $data["internalContext"] = "";
            }else{
                $html = $this->request->getPost("internalContext");
                $config = HTMLPurifier_Config::createDefault();
                $purifier = new HTMLPurifier($config);

                $cleanHtml = $purifier->purify($html);
                $data["link"] = "";
                $data["internalContext"] = $cleanHtml;
            }

            $menuModel->update($post["id"], $data);
            return json_encode($data);
        }

        if (count($_FILES) > 0){ //TODO: this doesn't delete old files
            $assets = new Assets();
            $return = [];

            foreach($_FILES as $file){
                $tmpName = htmlspecialchars($file["tmp_name"]);
                $type = explode("/", $file["type"])[1];

                $name = $assets->saveMenu($tmpName, $type);

                $return = ["link" => "/assets/menu/" . $name];
            }

            return json_encode($return);
        }
    }
}

?>