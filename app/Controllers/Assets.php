<?php

namespace App\Controllers;

use App\Models\BrandModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Assets extends BaseController {
    
    /**
     * Constructor
     *
     * @access    public
     */

     private $imgPath;
     private $collPath;
    private $collTmbPath;
     private $catPath;
     private $userPath;
     private $menuPath;
     private $session;
     private $imgTmbPath;
    private $brandPath;
    private $accountId;
    private $brand_id;

    public function __construct()
    {
        $this->session = session();
        $request = \Config\Services::request();

        $userModel = new UserModel();
        $brandModel = new BrandModel();
        
        if ($this->session->get("logIn")){
            $accountId = $userModel->getUser($this->session->get("user_id"), filter: ["account_id"]);
            $brandId = $this->session->get("brand_id");

            $this->brand_id = $brandId;
            $this->accountId = $accountId;
        }else{
            $apikey = $request->getGetPost("apikey");

            if (is_null($apikey) && array_key_exists("x-api-key", getallheaders())) {
                $apikey = getallheaders()["x-api-key"];
            }

            if (!is_null($apikey)) {
                $brand = $brandModel->getBrand($apikey, "apikey", ["id", "account_id"], true);
                $accountId = $brand["account_id"];
                $brandId = $brand["id"];
                $this->brand_id = $brandId;
                $this->accountId = $accountId;
            }else{
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "No valid API key found"]);
                exit;
            }
        }

        $this->imgPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/images/";
        $this->imgTmbPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/images/thumbnails/";
        $this->catPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/images/categories/";
        $this->userPath = getenv("BASE_PATH") . $accountId . "/users/";
        $this->collPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/images/collections/";
        $this->collTmbPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/images/collections/thumbnails/";
        $this->menuPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/menu/";
        $this->brandPath = getenv("BASE_PATH") . $accountId . "/" . $brandId . "/branding/";

        if (getenv("BASE_PATH")){
            if (!file_exists(getenv("BASE_PATH") . $accountId . "/" . $brandId)){
                mkdir(getenv("BASE_PATH") . $accountId . "/" . $brandId, 0777, true);
            }
            if (!file_exists($this->imgPath)){
                mkdir($this->imgPath, recursive: true);
            }
            if (!file_exists($this->imgTmbPath)) {
                mkdir($this->imgTmbPath, recursive: true);
            }
            if (!file_exists($this->catPath)) {
                mkdir($this->catPath, recursive: true);
            }
            if (!file_exists($this->userPath)) {
                mkdir($this->userPath, recursive: true);
            }
            if (!file_exists($this->collPath)) {
                mkdir($this->collPath, recursive: true);
            }
            if (!file_exists($this->menuPath)) {
                mkdir($this->menuPath, recursive: true);
            }
            if (!file_exists($this->brandPath)) {
                mkdir($this->brandPath, recursive: true);
            }
            if (!file_exists($this->collTmbPath)) {
                mkdir($this->collTmbPath, recursive: true);
            }
        }
    }

    function getAppLog()
    {
            $path = getenv("BASE_PATH") . $this->accountId . "/" . $this->brand_id . "/branding/";
            $file = "app-log.txt";

            if (file_exists($path . $file)) {

                header("Content-Type: " . "text/plain");
                readfile($path . $file);
                exit;
            } else {
                return view("errors/html/error_404", ["message" => "sorry we can't find that log file"]);
            }
    }

    function getAppKeystore()
    {
        $path = getenv("BASE_PATH") . $this->accountId . "/" . $this->brand_id . "/branding/";
        $file = "my-upload-key.keystore";

        if (file_exists($path . $file)) {

            header("Content-Type: " . "application/keychain_access");
            readfile($path . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that keystore"]);
        }
    }

    function getAPK($type){
        if ($type == "apk" || $type == "aab"){
            $path = getenv("BASE_PATH") . $this->accountId . "/" . $this->brand_id . "/branding/";
            $file = "app-release." . $type;

            if (file_exists($path . $file)) {

                if ($type == "apk"){
                    header("Content-Type: " . "application/vnd.android.package-archive");
                }else{
                    header("Content-Type: " . "application/x-authorware-bin");
                }
                readfile($path . $file);
                exit;
            } else {
                return view("errors/html/error_404", ["message" => "sorry we can't find that image"]);
            }
        }
    }

    /**
     * Returns image files
     *
     * @access    public
     * @param    string    file path
     */
    function images($file)
    {
        if (file_exists($this->imgPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            // echo $type;

            header("Content-Type: " . "image/" . $type);
            readfile($this->imgPath . $file);
            exit;
        }else{
            return view("errors/html/error_404", ["message" => "sorry we can't find that image"]);
        }
    }

    /**
     * Returns image files
     *
     * @access    public
     * @param    string    file path
     */
    function branding($brandID, $file)
    {
        $path = getenv("BASE_PATH") . $this->accountId . "/" . $this->brand_id . "/branding/";

        if (file_exists($path . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            // echo $type;

            header("Content-Type: " . "image/" . $type);
            readfile($path . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that image"]);
        }
    }

    function imageThumbnail($file){
        if (file_exists($this->imgTmbPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            // echo $type;

            header("Content-Type: " . "image/" . $type);
            readfile($this->imgTmbPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that thumbnail"]);
        }
    }

    function catImages($file)
    {
        if (file_exists($this->catPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            header("Content-Type: " . "image/" . $type);
            readfile($this->catPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that file"]);
        }
    }

    function colImages($file)
    {
        if (file_exists($this->collPath . $file)){

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            header("Content-Type: " . "image/" . $type);
            readfile($this->collPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that file"]);
        }
    }

    function collectionThumbnail($file)
    {
        if (file_exists($this->collTmbPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            header("Content-Type: " . "image/" . $type);
            readfile($this->collTmbPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that file"]);
        }
    }

    function user($file){
        if (file_exists($this->userPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            header("Content-Type: " . "image/" . $type);
            readfile($this->userPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that file"]);
        }
    }

    function menu($file)
    {
        if (file_exists($this->menuPath . $file)) {

            $matches = [];
            preg_match("/\.(.*)/", $file, $matches);

            $type = $this->mapType($matches[1]);

            header("Content-Type: " . "image/" . $type);
            readfile($this->menuPath . $file);
            exit;
        } else {
            return view("errors/html/error_404", ["message" => "sorry we can't find that file"]);
        }
    }

    public function saveProfilePhoto($userId, $tmpPath, $extention): string
    {
        move_uploaded_file($tmpPath, $this->userPath . $userId . "." . $extention);
        return "/assets/user/" . $userId . "." . $extention;
    }

    /**
     * Saves the image file to the correct directory and creates a thumbnail
     *
     * @param string $tmpPath | The temp path from the file upload
     * @param string $type | the type of image (png, jpg, bmp, webp)
     * @return string | the unique file name to save in database
     */
    public function saveImage($tmpPath, $type){

        $filename = tempnam($this->imgPath, '');
        unlink($filename); // Need to delete the created tmp file, just want the name

        $file = explode(".tmp", $filename)[0];
        $file = $file . "." . $type;

        if(move_uploaded_file($tmpPath, $file)){

            $dst = $this->generateThumbnail($file, $type);

            if (PHP_OS == "Linux"){
                $name = explode("images/", $file)[1];
                imagejpeg($dst, $this->imgTmbPath . $name);
                return $name;
            }else{
                $name = explode("images\\", $file)[1];
                imagejpeg($dst, $this->imgTmbPath . $name);
                return $name;
            }
        }else{
            return false;
        }
        
    }

    public function generateThumbnail($file, $type){
        $tmbsizey = 300;
        $tmbsizex = (int)$tmbsizey / 1.778;

        $src = $this->correctImageOrientation($file, $type);

        $srcheight = imagesy($src);
        $srcwidth = (int)$srcheight / 1.778;

        $src = imagecrop($src, ["x" => ((imagesx($src) / 2) - ($srcwidth / 2)), "y" => 0, "width" => $srcwidth, "height" => $srcheight]);

        $dst = imagecreatetruecolor($tmbsizex, $tmbsizey);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $tmbsizex, $tmbsizey, $srcwidth, $srcheight);

        return $dst;
    }

    private function correctImageOrientation($filename, $type){
        try {
            if ($type == "JPG" || $type == "jpg" || $type == "jpeg" || $type == "tiff" || $type == "tiff"){
                $exif = exif_read_data($filename);
                if ($exif && isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];
                    $img = $this->imagecreatefromfile($filename);
                    if ($orientation != 1) {
                        $deg = 0;
                        switch ($orientation) {
                            case 3:
                                $deg = 180;
                                break;
                            case 6:
                                $deg = 270;
                                break;
                            case 8:
                                $deg = 90;
                                break;
                        }
                        if ($deg) {
                            $img = imagerotate($img, $deg, 0);
                            return $img;
                        }
                    }else{
                        return $img;
                    }
                }else{
                    throw new RuntimeException("Image Malformated (exif data missing)");
                }
            }else{
                return $this->imagecreatefromfile($filename);
            }
        }catch (\Exception $e){
            return $this->imagecreatefromfile($filename);
        }
    }

    private function imagecreatefromfile($filename) {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException('File "' . $filename . '" not found.');
        }

        try {
            switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
                case "JPG":
                case 'jpeg':
                case 'jpg':
                    return imagecreatefromjpeg($filename);
                    break;

                case 'png':
                    return imagecreatefrompng($filename);
                    break;

                case 'gif':
                    return imagecreatefromgif($filename);
                    break;

                case 'webp':
                    return imagecreatefromwebp($filename);
                    break;

                default:
                    throw new \InvalidArgumentException('File "' . $filename . '" is not valid jpg, png, webp or gif image.');
                    break;
            }
        }catch (\Exception $e){
            throw new \InvalidArgumentException('Error creating file: ' . $filename );
        }
    }

    /**
     * updated the image by deleting the old one then saving the new one
     *
     * @param string $tmpPath | the temporary path of the new image
     * @param string $type | the type of the image (png, jpg, bmp, webp)
     * @param string $oldPath | the old name of the image
     * @return string name of the new file
     */
    public function updateImage($tmpPath, $type, $oldPath)
    {
        unlink($this->imgTmbPath . $oldPath);
        unlink($this->imgPath . $oldPath);
        return $this->saveImage($tmpPath, $type);
    }

    public function removeImage($fileName){
        unlink($this->imgPath . $fileName);
        unlink($this->imgTmbPath . $fileName);
    }

    /**
     * updated the image by deleting the old one then saving the new one
     *
     * @param string $tmpPath | the temporary path of the new image
     * @param string $type | the type of the image (png, jpg, bmp, webp)
     * @param string $oldPath | the old name/path of the image
     * @return string name of the new file
     */
    public function updateCollection($tmpPath, $type, $oldPath){
        if (is_file($this->collTmbPath . $oldPath)){
            unlink($this->collTmbPath . $oldPath);
        }
        unlink($this->collPath . $oldPath);
        return $this->saveCollection($tmpPath, $type);
    }

    /**
     * Saves the image file to the correct directory
     *
     * @param string $tmpPath | The temp path from the file upload
     * @param string $type | the type of image (png, jpg, bmp, webp)
     * @return string | the unique file name to save in database
     */
    public function saveCollection($tmpPath, $type)
    {

        $filename = tempnam($this->collPath, '');
        unlink($filename); // Need to delete the created tmp file, just want the name

        $file = explode(".tmp", $filename)[0];
        $file = $file . "." . $type;

        if (move_uploaded_file($tmpPath, $file)) {
            $dst = $this->generateThumbnail($file, $type);

            if (PHP_OS == "Linux") {
                $name = explode("collections/", $file)[1];
                imagejpeg($dst, $this->collTmbPath . $name);
                return $name;
            } else {
                $name = explode("collections\\", $file)[1];
                imagejpeg($dst, $this->collTmbPath . $name);
                return $name;
            }
        } else {
            return false;
        }
    }

    public function removeCollection($name){
        if (file_exists($this->collPath . $name)){
            unlink($this->collPath . $name);
        }
        if (file_exists($this->collTmbPath . $name)){
            unlink($this->collTmbPath . $name);
        }
    }

    /**
     * updated the image by deleting the old one then saving the new one
     *
     * @param string $tmpPath | the temporary path of the new image
     * @param string $type | the type of the image (png, jpg, bmp, webp)
     * @param string $oldPath | the old name/path of the image
     * @return string name of the new file
     */
    public function updateCategory($tmpPath, $type, $oldPath)
    {
        unlink($this->catPath . $oldPath);
        return $this->saveCategory($tmpPath, $type);
    }

    public function removeCategory($name)
    {
        if (file_exists($this->catPath . $name)) {
            unlink($this->catPath . $name);
        }
    }

    /**
     * Saves the image file to the correct directory
     *
     * @param string $tmpPath | The temp path from the file upload
     * @param string $type | the type of image (png, jpg, bmp, webp)
     * @return string | the unique file name to save in database
     */
    public function saveCategory($tmpPath, $type)
    {

        $filename = tempnam($this->catPath, '');
        unlink($filename); // Need to delete the created tmp file, just want the name

        $file = explode(".tmp", $filename)[0];
        $file = $file . "." . $type;

        if (move_uploaded_file($tmpPath, $file)) {
            if (PHP_OS == "Linux") {
                return explode("categories/", $file)[1];
            } else {
                return explode("categories\\", $file)[1];
            }
        } else {
            return false;
        }
    }

    /**
     * Saves the image file to the correct directory
     *
     * @param string $tmpPath | The temp path from the file upload
     * @param string $type | the type of image (png, jpg, bmp, webp)
     * @return string | the unique file name to save in database
     */
    public function saveMenu($tmpPath, $type)
    {

        $filename = tempnam($this->menuPath, '');
        unlink($filename); // Need to delete the created tmp file, just want the name

        $file = explode(".tmp", $filename)[0];
        $file = $file . "." . $type;

        if (move_uploaded_file($tmpPath, $file)) {
            if (PHP_OS == "Linux") {
                return explode("menu/", $file)[1];
            } else {
                return explode("menu\\", $file)[1];
            }
        } else {
            return false;
        }
    }

    /**
     * Saves the image file to the correct directory
     *
     * @param string $tmpPath | The temp path from the file upload
     * @param string $type | the type of image (png, jpg, bmp, webp)
     * @return string | the unique file name to save in database
     */
    public function saveBrandImg($tmpPath, $type, $name, $brandID=-1)
    {   
        $session = session();
        $file=null;
        if ($brandID == -1){
            $file = $this->brandPath . $name . "." . $type;
        }else{
            $userModel = new UserModel();
            $accountId = $userModel->getUser($session->get("user_id"), filter: ["account_id"]);
            $file = getenv("BASE_PATH") . $accountId . "/" . $brandID . "/branding/" . $name . "." . $type;
        }

        if (move_uploaded_file($tmpPath, $file)) {
            return $name . "." . $type;
        } else {
            return false;
        }
    }

    /**
     * updated the image by deleting the old one then saving the new one
     *
     * @param string $tmpPath | the temporary path of the new image
     * @param string $type | the type of the image (png, jpg, bmp, webp)
     * @param string $oldPath | the old name/path of the image
     * @return string name of the new file
     */
    public function updateBrandImg($tmpPath, $type, $name, $brandID=-1)
    {   
        if ($brandID == -1){
            unlink($this->brandPath . $name);
        }else{
            $userModel = new UserModel();
            $accountId = $userModel->getUser($this->session->get("user_id"), filter: ["account_id"]);
            $file = getenv("BASE_PATH") . $accountId . "/" . $brandID . "/" . "branding/" . $name;
            unlink($file);
        }
        $name = explode(".", $name)[0];
        return $this->saveBrandImg($tmpPath, $type, $name, $brandID);
    }

    /**
     * Remove's all of a brand's assoiated files
     *
     * @param int $brandID
     * @return void
     */
    public function removeBrand($brandID){
        $userModel = new UserModel();
        $accountId = $userModel->getUser($this->session->get("user_id"), filter: ["account_id"]);
        $path = getenv("BASE_PATH") . $accountId . "/" . $brandID;
        
        helper('filesystem');
        return delete_files($path, true);
    }

    public function removeUser($userID){
        $userModel = new UserModel();
        $accountId = $userModel->getUser($userID, filter: ["account_id", "icon"]);

        //delete if not a url
        if (preg_match("/^http/", $accountId["icon"]) == "0" && $accountId["icon"] != ""){
            $icon = explode("/", $accountId["icon"])[3];
            $path = getenv("BASE_PATH") . $accountId["account_id"] . "/users/" . $icon;
            unlink($path);
        }
    }

    //image upload csv
    public function makeCSV($columns) {
        $session = session();
        $brandModel = new BrandModel();
        $brandName = $brandModel->getBrand($session->get("brand_id"), filter: ["name"]);

        $time = time();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . preg_replace('/\s+/', '', $brandName) . date("Ymd", $time) . '.csv"');
        $fp = fopen($this->imgPath . "../images.csv", 'wb');
        fputcsv($fp, $columns);
        fclose($fp);
        return false;
    }

    public function writeLineCSV($data){
        $fp = fopen($this->imgPath . "../images.csv", 'a');
        fputcsv($fp, $data);
        fclose($fp);
    }

    public function getCSV(){
        return $this->imgPath . "../images.csv";
    }

    public function deleteCSV(){
        unlink($this->imgPath . "../images.csv");
    }

    private function mapType($inputType): string {
        return match ($inputType) {
            "png" => "png",
            "svg", => "svg+xml",
            "jpg", "jpeg", "jfif" => "jpg",
            "webp" => "webp",
            "ico", "cur" => "x-icon",
            "bmp" => "bmp",
            "tiff", "tif" => "tiff",
            "gif", "giff" => "gif",
            "vnd.adobe.photoshop" => "vnd.adobe.photoshop",
            default => "octet-stream"
        };
    }
}
