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

//!Corregir
// Solicitaamos una frase formada por letras y números y tiene que decirnos cuántas letras y cuantos números tiene y la suma de éstos
function ejercicio2() {
  let frase = prompt("Introduce una frase formada por letras y numeros:");
  let numero = 0;
 
}

// el mathfloor redonde el numero
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

//!Corregir
//  sabr si una palabra es palindroma
function ejercicio4() {
  let palabra = prompt("Introduce una palabra:");
  let esPalindroma = true;

  if (esPalindroma) {
    alert("La palabra es palíndroma");
  } else {
    alert("La palabra no es palíndroma");
  }
}
