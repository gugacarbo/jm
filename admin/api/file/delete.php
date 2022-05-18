<?php
if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => '403')));
} else {
 

    if (isset($_POST['file'])) {
        $filename = $_POST['file'];
        die(delete($filename));
    } else {
        echo (json_encode(array("status" => "error", "message" => "Nenhum arquivo enviado")));
    }


    function delete($file)
    {
        $target_path = $_SERVER['DOCUMENT_ROOT'] . $file;
        if (file_exists($target_path)) {
            unlink($target_path);
            return (json_encode(array("status" => "success", "message" => "Arquivo deletado com sucesso")));
        } else {
            return (json_encode(array("status" => "error", "message" => "Arquivo não encontrado")));
        }
    }
}
