<?php

$numero = $_POST["numero"];

for ($i = 1; $i < 11 ; $i++ ) {
    echo $numero . " x " . $i . " = " . $numero * $i . "<br>";
}