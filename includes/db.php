<?php
 $host = "localhost";
 $username = "root";
 $password = "";
 $dbnombre = "USCSS_Paladio";

 $conn = mysqli_connect($host, $username, $password, $dbnombre);


if (!$conn) {
    die("Error: " . mysqli_connect_error());
}

?>  