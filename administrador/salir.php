<?php 
session_start();
session_destroy();
header("Location: index.php");
exit(); // Siempre es buena práctica añadir exit después de header para evitar que el script siga ejecutándose.
?>
