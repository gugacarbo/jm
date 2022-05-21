<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {

    $content = '
<div class="adminHomeContainer adminContainer">

<div class="item mh"></div>
<div class="item mh"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item g"></div>
<div class="item mv"></div>
<div class="item mv"></div>
<div class="item g"></div>
<div class="item mv"></div>
<div class="item mv"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>
<div class="item"></div>

  
</div>
<script src="includes/home/home.js"></script>';

    die($content);
}
