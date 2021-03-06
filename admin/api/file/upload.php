<?php
header('Content-Type: application/json; charset=utf-8');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM" . $_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 2) {
    die(json_encode(array(
        'status' => 403,
        'message' => 'Acesso negado'
    )));
} else {
    if (isset($_FILES['file']['name']) && isset($_GET['dir'])) {
        $validDir = ['/about/', "/img/banners/", "/img/products/"];
        $directory = $_GET['dir'];

        if (!in_array($directory, $validDir)) {
            die(json_encode(array('status' => 403)));
        }

        $file = $_FILES['file']['tmp_name'];

        if (isset($_GET['md5'])) {
            $md5 = $_GET['md5'];
            die(upload($file, $directory, $md5));
        } else {
            die(upload($file, $directory));
        }
    } else {
        die(json_encode(array("status" => 400, "message" => "Nenhum arquivo enviado")));
    }
}


function upload($file_, $dir_, $md5_ = 'true', $valid_extensions = array())
{
    $imgExt = array('.jpg', '.jpeg', '.png');
    $textExt =  array('.ino');
    $validExtAll = array_merge($imgExt, $textExt);
    $valid_extensions = $validExtAll;


    $dir = $target_path = $_SERVER['DOCUMENT_ROOT'] . $dir_;

    $filename = $_FILES['file']['name'];
    $imageFileType = strrchr($filename, ".");

    if ($md5_ == 'true') {
        $location = ($dir . md5($filename . time()) . $imageFileType);
    } else {
        $location = $dir . $filename;
    }

    if (in_array(strtolower($imageFileType), $valid_extensions)) {

        $path = str_replace($target_path = $_SERVER['DOCUMENT_ROOT'], "", $location);

        if (in_array(strtolower($imageFileType), $imgExt)) {
            compressImage($file_, $location, 100);
            return (json_encode(array("status" => 202, "location" => $path)));
        } else if (move_uploaded_file($file_, $location)) {

            return (json_encode(array("status" => 202, "location" => $path)));
        } else {
            return (json_encode(array("status" => 500, "message" => "Erro ao salvar imagem")));
        }
    } else {
        return (json_encode(array("status" => 400, "message" => $imageFileType . " Extens??o inv??lida")));
    }
}

/*list($width_orig, $height_orig, $tipo, $atributo) = getimagesize($location);
            if ($height_orig < 0) {
                //curl post to delete.php
                include "delete.php";
                delete($path);

                return (json_encode(array("status" => "error", "message" => "A imagem deve ter no m??ximo 500px de altura.")));
            }*/

function compressImage($source_path, $destination_path, $quality)
{
    $info = getimagesize($source_path);

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source_path);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source_path);
    }

    imagejpeg($image, $destination_path, $quality);

    return $destination_path;
}
