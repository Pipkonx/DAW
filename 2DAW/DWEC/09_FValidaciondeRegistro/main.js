document
  .getElementById("miFormulario")
  .addEventListener("submit", function (event) {
    let errores = [];
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmarContrasenna = document.getElementById("confirmarContrasenna").value;
    const nombre = document.getElementById("nombre").value;

    if (!email) {
      errores.push("El email es obligatorio.");
    }
    if (!password || password.length < 8) {
      errores.push("La contraseña debe tener al menos 8 caracteres.");
    }

    for (let i = 0; i < nombre.length; i++) {
      if (nombre.charAt(i).match(/[0-9]/)) {
        // if (nombre[i] >= '0' && nombre[i] <= '9') {
        errores.push("No puedes poner un numero en el nombre");
      }
    }

    if (errores.length > 0) {
      event.preventDefault(); // Previene el envío del formulario
      document.getElementById("errores").innerHTML = errores.join("<br>");
    }

    if (password !== confirmarContrasenna) {
      event.preventDefault();
      errores.push("La contrasenna no coincide");
      document.getElementById("errores").innerHTML = errores.join("<br>");
    }

    if (errores.length == 0) {
      document.getElementById("errores").innerHTML = "El formulario se envio"
    }
    
  });
