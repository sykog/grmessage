<?php

$to = "2532235095@vtext.com";
$message = "This is a test text\n";
$headers = "From: LaterGators\n";
mail($to, '', $message, $headers);
?>