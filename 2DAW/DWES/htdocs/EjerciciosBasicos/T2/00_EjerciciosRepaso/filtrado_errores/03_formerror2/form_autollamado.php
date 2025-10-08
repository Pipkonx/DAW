<?php
include "utilsforms.php";

$hayError=FALSE;
$errores=[];

if ( ! $_POST ) 
{ // Si no han enviado el fomulario
	include 'pag_fomulario.php';
}
else 
{
	if (isset($_POST['edad']))
	{
		$edad=$_POST['edad'];
	}
	else
	{
		$edad='';
	}
	
	// Filtramos errores
	if (! is_numeric($edad) || $edad<18)
	{ // Error en edad
		$errores['edad']='El valor debe ser númerico y mayor o igual que 18';
		$hayError=TRUE;
	}
	if (! isset($_POST['extras']) )
	{
		$errores['extras']='No hay seleccionado ningún extra';
		$hayError=TRUE;
	}

	if ($hayError)
	{ // Hay error
		include 'pag_fomulario.php';
	}
	else 
	{ // NO hay error
		echo "<h1>Pagina resultados</h1>";
		echo "<p>Has enviado los datos</p>";
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
	}
}
