<?php

require_once '_includes/authenticate.php';

if (isset($_SESSION['authenticated'])){
    header('Location: home.php');
}