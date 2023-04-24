<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\CategoryModel;

class ImageModel extends Model
{
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $useTimestamps = true;
    protected $dateFormat    = 'timestamp';
    protected $createdField  = 'dateCreated';
    protected $updatedField  = 'dateUpdated';

    public function getAllIds($brandName){
        $builder = $this->db->table('brand');
        $builder->select("id")->where("name", $brandName);
        $brandID = $builder->get()->getResultArray()[0];

        $builder = $this->db->table('image');
        $builder->select("id")->where("brand_id", $brandID);
        $query = $builder->get()->getResultArray();

        $ids = [];

        foreach($query as $id){
            array_push($ids, $id["id"]);
        }

        return $ids;
    }

    public function getCollumn($column, $brandName, $getBy=[]){ //TODO: Needs to be joined with wallpaper table
        $builder = $this->db->table('brand');
        $builder->select("id")->where("name", $brandName);
        $brandID = $builder->get()->getResultArray()[0];

        $builder = $this->db->table('image');
        $builder->select($column);
        $builder->join("wallpaper", "image.id = wallpaper.id");
        $builder->where("brand_id", $brandID);

        if (count($getBy) > 0){
            $keys = array_keys($getBy);
            foreach ($keys as $key) {
                $builder->where($key, $getBy[$key]);
            }
        }

        $return = $builder->get()->getResultArray();

        $returnArray = [];

        foreach($return as $thing){
            array_push($returnArray, $thing[$column]);
        }

        return $returnArray;
    }

    public function getImage($id, String $fetchBy="id", Array $filter = [], Bool $assoc=false){
        $builder = $this->db->table('image');
        if (count($filter) > 0){
            $builder->select($filter)->where("image." . $fetchBy, $id);
            $builder->join('wallpaper', 'image.id = wallpaper.id');
            $imgID = $builder->get()->getResultArray()[0];

            if (!$assoc){
                if (count($filter) > 1){
                    $array = [];

                    foreach ($filter as $thing){
                        array_push($array, $imgID[$thing]);
                    }

                    return $array;
                }else{
                    if (count($filter) == 1) {
                        return $imgID[$filter[0]];
                    }else{
                        return $imgID;
                    }
                }
            }else{
                return $imgID;
            }
        }
        else{
            $builder->select("*")->where("image." . $fetchBy, $id);
            $builder->join('wallpaper', 'image.id = wallpaper.id');
            $imgID = $builder->get()->getResultArray();
            return $imgID;
        }
    }

    public function getImgByName($id){
        $builder = $this->db->table('wallpaper');
        $builder->select("id, name, description, dateUpdated, collection_id")->where("name", $id);
        $img = $builder->get()->getResultArray()[0];

        $builder = $this->db->table('image');
        $builder->select("imagePath, externalPath")->where("id", $img["id"]);
        $link = $builder->get()->getResultArray()[0];

        // foreach ($link as $thing){
        //     array_push($img, $thing);
        // }

        $img = array_merge($img, $link);

        return $img;
    }
}