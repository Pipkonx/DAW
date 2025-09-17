// *saber cuantas letras hay
function ejercicio1() {
  let frase = prompt("Escribe una frase:");
  let letra = prompt("Elige una letra");
  frase = frase.split("");
  let contador = 0;

  for (let i = 0; i < frase.length; i++) {
    if (frase[i] === letra) {
      contador++;
    }
  }
  alert("la letra " + letra + "de la frase aparece " + contador + " veces");
}

// * de una frase saber los numeros y letras y sumarlos
function ejercicio2() {
  let frase = prompt(
    "Introduce una frase formada por letras y numeros:"
  ).toLocaleLowerCase();
  let contadorNumero = 0,
    contadorLetra = 0,
    suma = 0;

  for (let i = 0; i < frase.length; i++) {
    if (frase[i] === " ") {
      continue;
    }
    if (frase[i] >= 0 && frase[i] <= 9) {
      contadorNumero++;
    } else if (frase[i] >= "a" && frase[i] <= "z") {
      contadorLetra++;
    }
    suma = contadorNumero + contadorLetra;
  }
  alert(
    "La frase: '" +
    frase +
    "' tiene " +
    contadorNumero +
    " números y " +
    contadorLetra +
    " letras" +
    " y la suma de los dos es " +
    suma
  );
}

// * adivina el número
// ? el mathfloor redonde el numero
function ejercicio3() {
  let numeroAleatorio = Math.floor(Math.random() * 100) + 1;
  //console.log(numeroAleatorio);
  let intento;

  while (intento != numeroAleatorio) {
    intento = prompt("Adivina el número (entre 1 y 100):");
    if (intento < numeroAleatorio) {
      alert("El número es mayor");
    } else if (intento > numeroAleatorio) {
      alert("El número es menor");
    }
  }
  alert("¡Has adivinado el número!");
}

//  * palindroma
function ejercicio4() {
  let palabra = prompt("Introduce una palabra:");
  let esPalindroma = true;

  for (let i = 0; i < palabra.length; i++) {
    if (palabra[i] !== palabra[palabra.length - 1 - i]) {
      esPalindroma = false;
    }
  }

  if (esPalindroma) {
    alert("La palabra es palíndroma");
  } else {
    alert("La palabra no es palíndroma");
  }
}
