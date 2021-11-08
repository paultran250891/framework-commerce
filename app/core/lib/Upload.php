<?php

namespace app\core\lib;

use app\core\App;
use app\core\request\Request;

class Upload
{

    public function show(Request $request)
    {
        $name = $request->getBody()['name'];
        echo json_encode(scandir(App::$rootPath . "/resources/img/$name"));
    }

    public function upload(Request $request)
    {
        // echo exec('whoami');
        // return;
        $namePath  = $request->getBody()['name'];
        $namePath = !empty($namePath) ? "$namePath/" : '';
        $target_dir = App::$rootPath . "/resources/img/$namePath";
        $nameFile = rand(1000, 9999) . '.jpg';
        $target_file = $target_dir . $nameFile;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image

        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            // $result =  "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $result['msg'] =  "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        while (file_exists($target_file)) {
            $nameFile = rand(1000, 9999) . '.jpg';
            $target_file = $target_dir . $nameFile;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $result['msg'] =  "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $result['msg'] =  "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $result['msg'] =  "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $result['res'] = true;
                $result['url'] =  "/img/$namePath$nameFile";
            } else {
                $result['msg'] =  "Sorry, there was an error uploading your file.";
            }
        }
        echo json_encode($result);
    }
}
