<?php

namespace App\Controllers;
use App\Models\CategoryModel;
use App\Models\CollectionModel;
use App\Models\BrandModel;
use App\Controllers\Navigation;

class Category extends BaseController
{
    public function index()
    {
        $session = session();
        $catModel = new CategoryModel;
        $colModel = new CollectionModel;
        $brandModel = new BrandModel;
        $brandname = $session->get("brand_name");

        $ids = $catModel->getCollumn("id", $brandname);
        $colIds = $colModel->getCollumn("id", $brandname);

        $categories = [];

        foreach ($ids as $id){
            $category = $catModel->getCategory($id, assoc: true);

            foreach($colIds as $colId){
                $colCatId = $colModel->getCollection($colId, filter: ["category_id", "name"], assoc: true);

                if ($colCatId["category_id"] == $id){
                    $category["collectionName"] = $colCatId["name"];
                }
            }

            array_push($categories, $category);
        }


        $data = [
            "categories" => $categories,
        ];

        return Navigation::renderNavBar("Categories","categories", [true, "Images"]) . view('Category/Category_Detail', $data) . Navigation::renderFooter();
    }

    public function post(){
        $session = session();
        if ($session->get("logIn")){
            $request = \Config\Services::request();
            $catModel = new CategoryModel;
            $brandname = $session->get("brand_name");

            $id = $request->getPost("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $req = $request->getVar("UpperReq", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if ($req == "true"){
                return json_encode($catModel->getCollumn("name", $brandname));
            }

            $category = $catModel->getCategory($id, fetchBy: "name", assoc: true);

            return json_encode($category);
        }else{
            return json_encode(["success" => false]);
        }
    }

    public function update(){
        $categoryModel = new CategoryModel();
        $assets = new Assets();

        //delete caches
        if (file_exists("../writable/cache/CategoryCategory_Detail")) {
            unlink("../writable/cache/CategoryCategory_Detail");
            unlink("../writable/cache/CategoryCategory_List");
        }

        $post = $this->request->getPost(["id", "name", "description", "link"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);


        if (count($_FILES) > 0){
            //get rid of the assets/category/
            $oldName = (string)$categoryModel->getCategory($post["id"], filter: ["iconPath"]);
            $tmpPath = htmlspecialchars($_FILES["file"]["tmp_name"]);

            $type = $this->request->getPost("type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $type = explode("image/", (string)$type)[1];

            if ($type == "svg+xml"){
                $type = "svg";
            }

            if ($oldName == ""){
                $newName = $assets->saveCategory($tmpPath, $type);
            }else{
                $oldName = explode("category/", $oldName)[1];
                $newName = $assets->updateCategory($tmpPath, $type, $oldName);
            }

            if(!$newName){
                return json_encode(["success" => false, "message" => "File Error"]);
                exit();
            }

            $data = [
                "name" => $post["name"],
                "description" => $post["description"],
                "iconPath" => "assets/category/" . $newName,
                "link" => $post["link"]
            ];

            $categoryModel->updateCategory($post["id"], $data);
        }else{
            $data = [
                "name" => $post["name"],
                "description" => $post["description"],
                "link" => $post["link"]
            ];

            $categoryModel->updateCategory($post["id"], $data);
        }

        return json_encode(["success" => true]);
    }

    //delete collections
    public function delete()
    {
        $catModel = new CategoryModel();
        $session = session();
        $assets = new Assets();

        //bulk image or single
        if ($this->request->getPost("ids") != null) {
            $ids = filter_var_array(json_decode((string)$this->request->getPost("ids")), FILTER_SANITIZE_NUMBER_INT);
            $dbids = $catModel->getAllIds($session->get("brand_name"));

            $vallidIds = array_intersect($dbids, $ids);

            foreach ($vallidIds as $id) {
                $path = $catModel->getCategory($id, filter: ["iconPath"])[0];
                $name = explode("/", $path)[3];
                $assets->removeCategory($name);
                $catModel->delete($id);
            }
        } else {
            $id = $this->request->getPost("id", FILTER_SANITIZE_NUMBER_INT);
            $dbids = $catModel->getAllIds($session->get("brand_name"));

            foreach ($dbids as $dbid) {
                if ($dbid == $id) {
                    $path = $catModel->getCategory($id, filter: ["iconPath"])[0];
                    $name = explode("/", $path)[3];
                    $assets->removeCategory($name);
                    $catModel->delete($id);
                }
            }
        }
    }
}
