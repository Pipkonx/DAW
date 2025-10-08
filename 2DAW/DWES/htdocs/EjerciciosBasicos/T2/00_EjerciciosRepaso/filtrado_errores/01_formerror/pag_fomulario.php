<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Insert title here</title>
</head>
<body>
	<h1>Procesando formulario</h1>
<?php if ($hayError ) :?>
	<div style="border: 1px solid #ccc; color: red">Hay errores en los
		campos</div>
<?php endif;?>
<FORM action="" METHOD="post">
		<p>Edad: 
		   <INPUT TYPE="text" NAME="edad" 
			  	 value="<?=isset($_POST['edad']) ? $_POST['edad'] : ''?>">
		</p>
		<p>
			<label><INPUT TYPE="radio" NAME="sexo" VALUE="M" CHECKED>Mujer</label>
			<INPUT TYPE="radio" NAME="sexo" VALUE="H">Hombre
		<p>
			Fecha Nac: <input type="date" name="fecha" />
		</p>
		<p>
		<input TYPE="checkbox" NAME="extras[]" VALUE="garaje"
			<?php
				if (in_array('garaje', $_POST['extras']))
					echo " checked ";
			?>
		>Garaje 
		<INPUT TYPE="checkbox" NAME="extras[]" VALUE="piscina"
			<?php
				if (in_array('piscina', $_POST['extras']))
					echo " checked ";
			?>
		>Piscina 
		<INPUT TYPE="checkbox" NAME="extras[]" VALUE="jardin"
			<?php
				if (in_array('jardin', $_POST['extras']))
					echo " checked ";
			?>
		>Jardín 
		<p>
		<input
			type="submit" name="botonVer" value="Enviar con ver texto">

		<button name="button" type="submit">Enviar SIN</button>
		</FORM>
</body>
</html>
