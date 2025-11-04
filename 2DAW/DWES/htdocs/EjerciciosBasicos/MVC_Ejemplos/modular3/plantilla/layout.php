
<!-- PLANTILLA DEFINICIÓN DE LA ESTRUCTURA DE LA PÁGINA -->
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Mi pagina modular</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</head>
<body>
<div id="wrapper">      
    <div id="header">       
    <?php 
        /*  EL CÓDIGO HTML DEL HEADER LLEGA EN UNA VARIABLE */
        echo $header_html
    ?>        
    </div>  
    <div id="menu" style="float:left; width:10em; border:1px solid #eee; background:#ccffcc; margin:1em .2em; border-radius: 5px">
    <?php 
        /*  EL CÓDIGO HTML DEL MENU LLEGA EN UNA VARIABLE */
        echo $menu_html
    ?>            
    </div>  
    <div id="cuerpo"><?php 
        /*  EL CÓDIGO HTML DEL CUERPO LLEGA EN UNA VARIABLE */
        echo $cuerpo_html
    ?>        
    </div>
    <div id="footer" style="clear:both">          
        <a href="http://ieslamarisma.net/aulav" target="_blank">Desarrollo web entorno servidor</a> I.E.S. La Marisma
    </div>
</div>

</body>
</html>
