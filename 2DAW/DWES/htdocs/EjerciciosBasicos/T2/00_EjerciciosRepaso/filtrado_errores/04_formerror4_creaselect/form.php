<?php
include "utilsforms.php";
$hayError=FALSE;
$errores=array();

if ( ! $_POST ) 
{ // Si no han enviado el fomulario
	include 'fomulario.php';
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
		$errores['edad']='Edad > 18';
	}
	
	if (ValorPost('curso')=='')
	{
		$errores['curso']='Introduzca curso';
	}
	
	if ($errores)
	{
		// Hay error
		include 'fomulario.php';
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
