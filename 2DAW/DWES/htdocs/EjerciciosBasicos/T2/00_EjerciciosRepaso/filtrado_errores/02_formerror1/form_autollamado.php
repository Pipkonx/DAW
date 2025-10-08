<?php
include "utilsforms.php";

$hayError=FALSE;

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
	
	if (! is_numeric($edad) || $edad<18)
	{
		$hayError=TRUE;
		// Hay error
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
