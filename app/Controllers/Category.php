<?php

namespace App\Controllers;
use App\Models\CategoryModel;
use App\Models\CollectionModel;
use App\Models\BrandModel;
use App\Controllers\Navigation;
use App\Models\UserModel;
use RuntimeException;

class Category extends BaseController
{
    public function index()
    {
        $session = session();
        $catModel = new CategoryModel;
        $colModel = new CollectionModel;
        $brandModel = new BrandModel;
        $brandname = $session->get("brand_id");

        $ids = $catModel->getCollumn("id", $brandname);
        $colIds = $colModel->getCollumn("id", $brandname);

        $categories = [];

        foreach ($ids as $id){
            $category = $catModel->getCategory($id, assoc: true);

            if ($category["name"] == "Default Category"){
                continue;
            }

            try {
                $category["collectionName"] = $colModel->getCollection($id, ["name"], "category_id");
            } catch (\Throwable $th) {
                $category["collectionName"] = "None";
            }

            array_push($categories, $category);
        }

        //filter for spesific ID
        if ($this->request->getGet("id") != null) {
            $catId = $this->request->getGet("id", FILTER_SANITIZE_NUMBER_INT);
            $ids = $catModel->getCollumn("id", $session->get("brand_id"));
            foreach ($ids as $id) {
                if ($id == $catId) {
                    $category = $catModel->getCategory($id, assoc: true);
                    
                    try {
                        $category["collectionName"] = $colModel->getCollection($id, ["name"], "category_id");
                    } catch (\Throwable $th) {
                        $category["collectionName"] = "None";
                    }
                    //empty the array
                    $categories = [];
                    $categories[0] = $category;
                }
            }
        }


        $data = [
            "categories" => $categories,
        ];

        return Navigation::renderNavBar("Categories","categories") . view('Category/Category_Detail', $data) . Navigation::renderFooter();
    }

    public function post(){
        $session = session();
        if ($session->get("logIn")){
            $request = \Config\Services::request();
            $catModel = new CategoryModel;
            $brandname = $session->get("brand_id");

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
        $session = session();

        //delete caches
        if (file_exists("../writable/cache/CategoryCategory_Detail")) {
            unlink("../writable/cache/CategoryCategory_Detail");
            unlink("../writable/cache/CategoryCategory_List");
        }

        if ($this->request->getPost("allactive") !== null) {
            $ids = $categoryModel->getCollumn("id", $session->get("brand_id"));
            foreach ($ids as $id) {
                $categoryModel->update($id, ["active" => $this->request->getPost("allactive", FILTER_VALIDATE_BOOL)]);
            }
            exit;
        }

        // try {
            $brandModel = new BrandModel();
            $userModel = new UserModel();
            $post = $this->request->getPost(["id", "name", "description", "link"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $active = $this->request->getPost("active", FILTER_VALIDATE_BOOL);
            $permission = $userModel->getPermissions($session->get("user_id"), $session->get("brand_id"), ["categories"], ["p_add"]);

            if ($post["id"] == "undefined" && $permission){
                $data = [
                    "name" => $post["name"],
                    "description" => $post["description"],
                    "link" => $post["link"],
                    "brand_id" => $session->get("brand_id"),
                    "active" => $active
                ];

                if (count($this->request->getFiles()) > 0){
                    $file = $this->request->getFile("file");

                    if (!$file->isValid()){
                        throw new RuntimeException("Invalid File");
                    }

                    $name = $assets->saveCategory($file->getTempName(), $file->guessExtension());
                    $data["iconPath"] = "/assets/category/" . $name;
                }

                $categoryModel->save($data);
                return json_encode(["success" => true]);
                die;
            }


            if (count($_FILES) > 0){
                //get rid of the assets/category/
                $oldName = (string)$categoryModel->getCategory($post["id"], filter: ["iconPath"]);
                $tmpPath = htmlspecialchars($_FILES["file"]["tmp_name"]);

                $type = $this->request->getPost("type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $type = explode("image/", (string)$type)[1];

                if ($type == "svg+xml"){
                    $type = "svg";
                }

                if ($oldName == "" || is_null($oldName)){
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
                    "iconPath" => "/assets/category/" . $newName,
                    "link" => $post["link"],
                    "active" => $active
                ];

                $categoryModel->updateCategory($post["id"], $data);
            }else{
                $data = [
                    "name" => $post["name"],
                    "description" => $post["description"],
                    "link" => $post["link"],
                    "active" => $active
                ];

                $categoryModel->updateCategory($post["id"], $data);
            }

            return json_encode(["success" => true]);
        // }catch (\Exception $e){
        //     http_response_code(400);
        //     return json_encode($e->getMessage());
        //     die;
        // }
    }

    //delete collections
    public function delete()
    {
        try {
            $catModel = new CategoryModel();
            $colModel = new CollectionModel();
            $session = session();
            $assets = new Assets();

            //bulk image or single
            if ($this->request->getPost("ids") != null) {
                $ids = filter_var_array(json_decode((string)$this->request->getPost("ids")), FILTER_SANITIZE_SPECIAL_CHARS);
                $dbids = $catModel->getCollumn("name", $session->get("brand_id"));

                $vallidIds = array_intersect($dbids, $ids);

                if (count($vallidIds) > 1) {
                    array_shift($vallidIds);
                }

                $colIds = $colModel->getAllIds($session->get("brand_id"));
                
                foreach($vallidIds as $id){
                    $category = $catModel->getCategory($id, "name", ["iconPath", "id"], true);

                    //foreign key check
                    foreach($colIds as $colId){
                        $collCatId = $colModel->getCollection($colId, ["category_id"]);
                        if ($collCatId == $category["id"]){
                            throw new RuntimeException("You can not delete a category that contains collections");
                        }
                    }

                    //delete assets
                    if ($category["iconPath"] != "" && preg_match("/http/", $category["iconPath"]) != 1) {
                        $name = explode("/", $category["iconPath"])[3];
                        $assets->removeCategory($name);
                    }

                    $catModel->delete($category["id"]);
                }

            } else {
                $id = $this->request->getPost("id", FILTER_SANITIZE_SPECIAL_CHARS);

                if ($id != null){
                    $category = $catModel->getCategory($id, assoc: true);
                    $dbids = $catModel->getCollumn("id", $session->get("brand_id"));

                    $validId = array_intersect($dbids, [$id]);

                    //foreign key check
                    $colIds = $colModel->getAllIds($session->get("brand_id"));
                    foreach($colIds as $colId){
                        $collection = $colModel->getCollection($colId, assoc: true);
                        if ($category["id"] == $collection["category_id"]){
                            throw new RuntimeException("You can not delete a category that contains collections");
                        }
                    }

                    //delete assets
                    if ($category["iconPath"] != "" && preg_match("/http/", $category["iconPath"]) != 1) {
                        $name = explode("/", $category["iconPath"])[3];
                        $assets->removeCategory($name);
                    }

                    $catModel->delete($validId);
                }
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            exit;
        }
    }
}
