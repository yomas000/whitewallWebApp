<?php

namespace App\Controllers;
use App\Models\BrandModel;
use App\Controllers\Navigation;
use App\Models\UserModel;

class Brand extends BaseController
{
    public function index() //Change brand page
    {
        $session = session();
        if ($session->get("logIn")){
            $brandModel = new BrandModel;
            $ids = $brandModel->getCollumn("id", 1); //TODO: make accountID a session variable

            $brands = [];

            foreach($ids as $id){
                array_push($brands, $brandModel->getBrand($id, assoc: true));
            }

            $data = [
                "brandInfo" => $brands,
            ];

            return Navigation::renderNavBar("Brands") . view('brand/Brand', $data) . Navigation::renderFooter();
        }else{
            return view("errors/html/authError");
        }
    }

    public function branding($brandId){
        $session = session();
        if ($session->get("logIn")){

            return Navigation::renderNavBar("Branding", [true, "Brands"]) . view("brand/Branding") . Navigation::renderFooter();
        }else{
            return json_encode(["success" => false]);
        }
    }

    public function users($brandId){
        $session = session();
        if ($session->get("logIn")){
            $userModel = new UserModel();
            $userIds = $userModel->getCollumn("id", $session->get("brand_name")); //I have NO flipping idea why this works

            $users = [];

            foreach($userIds as $id){
                $user = $userModel->getUser($id, filter: ["name", "email", "id", "brand_id"]);
                array_push($users, $user);
            }

            $data = [
                "users" => $users
            ];

            return Navigation::renderNavBar("Brand Users", [true, "Users"]) . view("brand/Users", $data) . Navigation::renderFooter();
        }else{
            return json_encode(["success" => false]);
        }
    }

    public function userData(){
        $session = session();
        $msg = "Not Logged In";
        if ($session->get("logIn")){
            $request = \Config\Services::request();
            $userModel = new UserModel();

            $id = esc($request->getGet("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            $user = $userModel->getUser($id);
            $permissions = $userModel->getPermissions($id, $session->get("brand_name"));

            $data = [
                "user" => $user,
                "permissions" => $permissions
            ];

            return json_encode($data);
        }else{
            return json_encode(["success" => false, "msg" => $msg]);
        }
    }

    public function setBrand()
    {
        $session = session();
        if ($session->get("logIn")) {
            $request = \Config\Services::request();
            $name = esc($request->getPost("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $session = session();
            $session->set("brand_name", $name);

            return json_encode(["success" => true]);
        } else {
            return json_encode(["success" => false]);
        }
    }
}

?>
