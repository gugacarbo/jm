<?php
include "api/config/db_connect.php";


class Visitante extends dbConnect
{
    private $Id, $Ip, $Data, $Hora, $Limite, $mysqli;

    #Construtor para setar atributos
    public function __construct()
    {
        $this->Id = 0;
        $this->Ip = $_SERVER['REMOTE_ADDR'];
        $this->Data = date("Y/m/d");
        $this->Hora = date("H:i:s");
        $this->Limite = 20*60;
        $this->mysqli = $this->Conectar();
    }

    #Verifica se o usuário recebeu visita recentemente
    public function VerificaUsuario()
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM visitas WHERE ip = ? AND data = ? ORDER BY id DESC");


        $stmt->bind_param("ss", $this->Ip, $this->Data);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 0) {
            $this->InserindoVisitas();
        } else {
            $result = $result->fetch_assoc();
            $HoraDB = strtotime($result['hora']);
            $HoraAtual = strtotime($this->Hora);
            $HoraSubtracao = $HoraAtual - $HoraDB;

            if ($HoraSubtracao > $this->Limite) {
                $this->InserindoVisitas();
            } else {
            }
        }

        $visitantes = $this->mysqli->prepare("SELECT COUNT(*) FROM visitas WHERE data = NOW()");
        $visitantes->execute();
        $visitantes->bind_result($visitantes);
        $visitantes->fetch();
        //return ($visitantes);
    }

    private function InserindoVisitas()
    {
        $stmt =  $this->mysqli->prepare("INSERT INTO visitas (ip, data, hora) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->Ip, $this->Data, $this->Hora);
        $stmt->execute();
        if ($stmt->affected_rows == 1) {
        }
        $stmt->close();
    }
}
