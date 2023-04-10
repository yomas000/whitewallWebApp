<?php

namespace App\Controllers;
use App\Models\BrandModel;

class Account extends BaseController
{
    public function index()
    {   
        $session = session();
        if ($session->get("logIn")){
            $brandModel = new BrandModel;

            $data = [
                "brandId" => $brandModel->getBrand($session->get("brand_name"), fetchBy: "name", filter: ["id"]),
                "pageTitle" => "Account"
            ];
            return view('Account', $data);
        }else{
            return view("errors/html/authError");
        }
        
    }
}

?>
