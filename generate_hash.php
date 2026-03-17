<?php
$password = "S@mueletoo99";
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Hash bcrypt pour le mot de passe '$password' :\n";
echo $hash . "\n";
?>