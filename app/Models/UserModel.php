<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\BrandModel;

class UserModel extends Model
{
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $table = "user";
    protected $allowedFields = ["name", "password", "email", "phone", "icon", "status", "default_brand", "google_id"];

    public function getCollumn($column, $brandName, $getBy = []): mixed
    {
        $brandModel = new BrandModel();

        $brandId = $brandModel->getBrand($brandName, "name", ["id"]);

        $builder = $this->db->table('user');
        $builder->join("branduser", "branduser.user_id = user.id", "inner");
        $builder->select($column)->where("brand_id", $brandId);

        if (count($getBy) > 0) {
            $keys = array_keys($getBy);
            foreach ($keys as $key) {
                $builder->where($key, $getBy[$key]);
            }
        }

        $return = $builder->get()->getResultArray();

        $returnArray = [];

        foreach ($return as $thing) {
            array_push($returnArray, $thing);
        }

        return $returnArray;
    }

    public function getUser($id, $fetchBy = "user.id", $filter = [], $assoc = true): mixed
    {
        $builder = $this->db->table('user');
        $builder->join("branduser", "branduser.user_id = user.id", "inner");
        // $builder->join("usersettings", "usersettings.user_id = user.id", "inner");

        if (count($filter) > 0) {
            $builder->select($filter)->where($fetchBy, $id);
            $user = $builder->get()->getResultArray()[0];

            if (!$assoc) {
                if (count($filter) > 0) {
                    $array = [];

                    foreach ($filter as $thing) {
                        array_push($array, $user[$thing]);
                    }

                    return $array;
                } else {
                    if (count($filter) == 1) {
                        return $user[$filter[0]];
                    }else{
                        return $user;
                    }
                }
            } else {
                if (count($filter) == 1) {
                    return $user[$filter[0]];
                } else {
                    return $user;
                }
            }
        } else {
            $builder->select("*")->where($fetchBy, $id);
            $brand = $builder->get()->getResultArray()[0];
            return $brand;
        }
    }

    public function getAdmin($userId, $brandName){
        $builder = $this->db->table('branduser');
        $brandModel = new BrandModel();
        $brandId = $brandModel->getBrand($brandName, "name", ["id"]);

        $builder->select("admin")->where("brand_id", $brandId)->where("user_id", $userId);

        return $builder->get()->getResultArray()[0]["admin"];
    }

    public function getPermissions($userId, $brandName, $area=[], $permissions = ["p_view", "p_add", "p_edit", "p_remove"]): array {
        $brandModel = new BrandModel();
        $brandID = $brandModel->getBrand($brandName, "name", ["id"]);

        if (count($area) > 0){

            $builder = $this->db->table('permissions');

            $return = [];
            
            foreach($area as $areaName){
                $builder->select($permissions)->where("user_id", $userId)->where("brand_id", $brandID)->where("area", $areaName);
                $query = $builder->get()->getResultArray()[0];

                $return[$areaName] = $query;
            }

            return $return;

        }else if (count($permissions) < 4) {
            $builder = $this->db->table('permissions');
            $builder->join("branduser", "branduser.user_id = permissions.user_id AND branduser.brand_id = permissions.brand_id", "inner");

            array_push($permissions, "area");
            $builder->select($permissions)->where("permissions.user_id", $userId)->where("permissions.brand_id", $brandID);
            $query = $builder->get()->getResultArray();

            $return = [];

            foreach ($query as $permission) {
                $areaPermissions = [];
                foreach ($permissions as $area) {
                   $areaPermissions[$area] = $permission[$area];
                }
                $return[$permission["area"]] = $areaPermissions;
            }

            return $return;
        }else{
            $builder = $this->db->table('permissions');
            $builder->join("branduser", "branduser.user_id = permissions.user_id AND branduser.brand_id = permissions.brand_id", "inner");

            array_push($permissions, "area");
            $builder->select("*")->where("permissions.user_id", $userId)->where("permissions.brand_id", $brandID);
            $query = $builder->get()->getResultArray();

            $return = [];

            foreach ($query as $permission) {
                $return[$permission["area"]] = [
                    "view" => $permission["p_view"],
                    "add" => $permission["p_add"],
                    "edit" => $permission["p_edit"],
                    "remove" => $permission["p_remove"],
                ];
            }

            return $return;
        }
    }
}