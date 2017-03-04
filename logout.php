<?php
session_start();
session_destroy();
setcookie("login","0",-1);
header("Location: /");
?>