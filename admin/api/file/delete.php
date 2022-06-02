<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])  || ($_SESSION['admin']) < 2) {
    die(json_encode(array('status' => 403)));
} else {
 

    if (isset($_POST['file'])) {
        $filename = $_POST['file'];
        die(delete($filename));
    } else {
        echo (json_encode(array("status" => 400, "message" => "Nenhum arquivo enviado")));
    }


}

function delete($file)
{
    $target_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($target_path)) {
        unlink($target_path);
        return (json_encode(array("status" => 200, "message" => "Arquivo deletado com sucesso")));
    } else {
        return (json_encode(array("status" => 504, "message" => "Arquivo não encontrado")));
    }
}