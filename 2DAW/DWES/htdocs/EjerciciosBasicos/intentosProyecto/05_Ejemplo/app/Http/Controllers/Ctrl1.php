<?php
namespace App\Http\Controllers;

use App\Models\ModeloVacio;


class Ctrl1 {
    public function action1()
    {
        echo "<p>En Acción 1</p>";

        echo "<p>Numero del modelo:";
        echo ModeloVacio::unNumero();
    }
}

