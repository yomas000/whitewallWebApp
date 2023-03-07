<?php

namespace App\Controllers;
use App\Models\BrandModel;

class Brand extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get("logIn")){
            $brandModel = new BrandModel;
            $ids = $brandModel->getCollumn("id", 1);

            $brands = [];

            foreach($ids as $id){
                array_push($brands, $brandModel->getBrand($id, assoc: true));
            }

            $data = [
                "brands" => $brands
            ];

            return view('Brand', $data);
        }else{
            return view("errors/html/authError");
        }
    }

    public function post(){
        $session = session();
        if ($session->get("logIn")){
            $request = \Config\Services::request();
            $name = $request->getPost("id", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $session = session();
            $session->set("brand_name", $name);

            return json_encode(["success" => true]);
        }else{
            return json_encode(["success" => false]);
        }
    }
}

?>
