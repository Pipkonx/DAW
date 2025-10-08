<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Insert title here</title>
</head>
<body>
	<h1>Procesando formulario</h1>
<?php if ($errores ) :?>
	<div style="border: 1px solid #ccc; color: red">
	<?php foreach($errores as $textoError) {
		echo "<li>".$textoError."</li>";
	}
	?>
	</ul></div>	
	</div>
<?php endif;?>
<FORM action="" METHOD="post">
		<p>Edad: 
		   <INPUT TYPE="text" NAME="edad" 
			  	 value="<?=isset($_POST['edad']) ? $_POST['edad'] : ''?>"><?php VerError('edad')?>
		</p>
		<p>Curso: <?=
			CreaSelect('curso',array(
				''=>'',
				'1asir'=>'1º ASIR',
				'2asir'=>'2º ASIR',
				'1daw'=>'1º DAW',
				'2daw'=>'2º DAW',),
				ValorPost('curso'))?>
		<!--select name="curso">
			<option value=""></option>
			<option value="1asir" 
				<?= ValorPost('curso')=='1asir' ? 
						' selected ' : 
						''?>
				>1º ASIR</option>
			<option value="2asir">2º ASIR</option>
			<option value="1daw">1º DAW</option>
			<option value="2daw">2º DAW</option>
			
		</select--><?php VerError('curso')?>
		<p>
		<input
			type="submit" name="botonVer" value="Enviar con ver texto">

		<button name="button" type="submit">Enviar SIN</button>
		</FORM>
</body>
</html>
