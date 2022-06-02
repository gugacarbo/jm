<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 2) {
    die(json_encode(array('status' => 403,
    'message' => 'Acesso negado')));
}


include_once '../config/db_connect.php';


class config extends dbConnect
{
    public function __construct()
    {
    }

    public function setShippingConfig($cepOrigemFrete, $aditionalWeight, $alturaFrete, $larguraFrete, $comprimentoFrete)
    {
        $mysqli = $this->connect();
        $cepOrigemFrete = str_replace(['-', '.', ' ', ',','_'], '', $cepOrigemFrete);
        $cepOrigemFrete = $mysqli->real_escape_string($cepOrigemFrete);


        $configs = [
            "cepOrigemFrete" => $cepOrigemFrete,
            "aditionalWeight" => $aditionalWeight,
            "alturaFrete" => $alturaFrete,
            "larguraFrete" => $larguraFrete,
            "comprimentoFrete" => $comprimentoFrete
        ];
        foreach ($configs as $key => $value) {
            $sql = "UPDATE generalconfig SET value = ? WHERE config = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $value, $key);
            if ($stmt->execute()) {
                $stmt->close();
            } else {
                $stmt->close();
                return ((array('status' => 500, 'error' => 'Erro ao atualizar configurações')));
            }
        }
        return ((array('status' => 200)));
    }

    public function setFreeShipping($freteGratis, $fCidades, $fEstados)
    {
        $mysqli = $this->connect();


        if (empty($fCidades)) {
            $config["freteGratis"]["cidades"] = [];
        } else {
            $cidades = $fCidades;

            foreach ($cidades as $key => $value) {
                $cidades_[] = $value;
            }
            $config["freteGratis"]["cidades"] = $cidades_;
        }


        if (empty($fEstados)) {
            $config["freteGratis"]["estados"] = [];
        } else {
            $estados = $fEstados;

            foreach ($estados as $key => $value) {
                $estados_[] = $value;
            }
            $config["freteGratis"]["estados"] = $estados_;
        }

        $config["freteGratis"]["use"] = $freteGratis;
        $config["freteGratis"] = json_encode($config["freteGratis"]);
        //print_r($config);


        foreach ($config as $key => $value) {
            $sql = "UPDATE generalconfig SET value = ? WHERE config = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $value, $key);
            if ($stmt->execute()) {
                $stmt->close();
            } else {
                $stmt->close();
                return ((array('status' => 500, 'error' => 'Erro ao atualizar configurações')));
            }
        }
        return ((array('status' => 200)));
    }

    public function setAdminMailConfig($adminMail, $contactMail, $automaticMail, $automaticMailPass, $sendToMail)
    {
        $mysqli = $this->connect();
        $configs = [
            "adminMail" => $adminMail,
            "contactMail" => $contactMail,
            "automaticMail" => $automaticMail,
            "sendToAdminMail" => $sendToMail != [] ? json_encode($sendToMail) : '[]'
        ];
        $automaticMailPass != '' ? $configs['automaticMailPass'] = $automaticMailPass : '';
        foreach ($configs as $key => $value) {

            $sql = "UPDATE generalconfig SET value = ? WHERE config = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $value, $key);

            if (!$stmt->execute()) {
                $stmt->close();
                return ((array("status" => 500, "message" => "Erro ao alterar configuração")));
            }
            $stmt->close();
        }
        return ((array("status" => 200, "message" => "Configurações alteradas com sucesso")));
    }
    public function editAdminPassword($user, $old, $new)
    {
        $mysqli = $this->connect();

        $sql = "SELECT password FROM admin WHERE user = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['password'] == md5($old)) {
                $new = md5($new);
                $sql = "UPDATE admin SET password = ? WHERE user = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ss", $new, $user);
                if ($stmt->execute()) {
                    $stmt->close();
                    return ((array("status" => 201, "message" => "Senha alterada com sucesso")));
                } else {
                    $stmt->close();
                    return ((array("status" => 504, "message" => "Erro ao alterar senha")));
                }
            } else {
                return ((array("status" => 403, "message" => "Senha atual incorreta")));
            }
        } else {
            return ((array("status" => 404, "message" => "Usuário não encontrado")));
        }

    }
}




//> Edit mail Config
if (isset($_POST["adminMail"]) && isset($_POST["contactMail"]) && isset($_POST["automaticMail"])) {

    $adminMail = $_POST["adminMail"];
    $contactMail = $_POST["contactMail"];
    $automaticMail = $_POST["automaticMail"];

    if (isset($_POST["automaticPass"])) {
        $automaticMailPass = $_POST["automaticPass"];
    } else {
        $automaticMailPass = "";
    }

    if (isset($_POST["sendToAdminMail"])) {
        $sendToMail = $_POST["sendToAdminMail"];
    } else {
        $sendToMail = [];
    }

    $config = new config();
    $response = $config->setAdminMailConfig($adminMail, $contactMail, $automaticMail, $automaticMailPass, $sendToMail);
    die(json_encode($response));
    //*Edit Frete Gratis Config
} else if (isset($_POST['freteGratis']) && isset($_POST['cidades']) && isset($_POST['estados'])) {


    $freteGratis = $_POST['freteGratis'];
    $cidades = $_POST['cidades'];
    $estados = $_POST['estados'];

    $config = new config();
    $response = $config->setFreeShipping($freteGratis, $cidades, $estados);
    die(json_encode($response));

    //? Edit Frete Config
} else if (isset($_POST["cepOrigemFrete"]) && isset($_POST["aditionalWeight"]) && isset($_POST["alturaFrete"]) && isset($_POST["larguraFrete"]) && isset($_POST["comprimentoFrete"])) {

    $cepOrigemFrete = $_POST["cepOrigemFrete"];
    $aditionalWeight = $_POST["aditionalWeight"];
    $alturaFrete = $_POST["alturaFrete"];
    $larguraFrete = $_POST["larguraFrete"];
    $comprimentoFrete = $_POST["comprimentoFrete"];

    $config = new config();
    $response = $config->setShippingConfig($cepOrigemFrete, $aditionalWeight, $alturaFrete, $larguraFrete, $comprimentoFrete);
    die(json_encode($response));

    //! Change Password
}else if(($_POST["user"]) &&($_POST["currentPassword"]) && isset($_POST["newPassword"])){
    $user = $_POST["user"];
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];

    $config = new config();
    $response = $config->editAdminPassword($user, $currentPassword, $newPassword);
    die(json_encode($response));
    
} else {

    die(json_encode(array("status" => 400, "message" => "Bad Resquest")));
}
