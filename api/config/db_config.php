<?php
abstract class dbConfig
{
    public $JMserver = "localhost", $JMuser = "root", $JMpassword = "", $JMdbname = "jm22";

    public function connect()
    {
        $this->JMserver = "localhost";
        $this->JMuser = "root";
        $this->JMpassword = "";
        $this->JMdbname = "jm22";
    }
}
