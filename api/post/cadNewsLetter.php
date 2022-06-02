<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/db_connect.php';
class Newsletter extends dbConnect
{
    private $name_, $email_;

    public function __construct($name, $email)
    {
        $this->name_ = $name;
        $this->email_ = $email;

        $this->name_  = preg_replace('/[|\,\;\@\:"]+/', '', $name);
        $this->email_  = preg_replace('/[|\,\;\\\:"]+/', '', $email);
    }
    
    public function cadastrar()
    {
        $mysqli = $this->Conectar();

        $name = mysqli_real_escape_string($mysqli, $this->name_);
        $email = mysqli_real_escape_string($mysqli, $this->email_);

        $stmt = $mysqli->prepare("SELECT * FROM newsletter WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_ = $stmt->get_result();
        $stmt->close();
        if ($result_->num_rows > 0) {
            return(json_encode(array('status' => 200)));
        } else {
            $stmt = $mysqli->prepare("INSERT INTO newsletter (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            if ($stmt->execute()) {
                return(json_encode(array('status' => 201)));
            }
        }
    }
}





if (isset($_POST['name']) && isset($_POST['email'])) {

    if (isset($_SESSION['newsletterTry']) && $_SESSION['newsletterTry'] > 5) {
        $lastTry = date($_SESSION['newsletterLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 60) {
            $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
            $_SESSION['newsletterTry'] = 0;
        }

        die(json_encode(array('status' => 403)));
    } else {
        $_SESSION['newsletterTry'] = isset($_SESSION['newsletterTry']) ? $_SESSION['newsletterTry'] + 1 : 1;
        $_SESSION['newsletterLastTry'] = date("Y-m-d H:i:s");
        
        $news = new Newsletter($_POST['name'], $_POST['email']);
        die($news->cadastrar());
    }
} else {
    die(json_encode(array('status' => 400)));
}
