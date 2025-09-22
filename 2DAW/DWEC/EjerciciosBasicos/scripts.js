// * saber cuantas letras hay
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

// * dar una frase saber los numeros y letras y sumarlos
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
  let intento, contador = 0;

  while (intento != numeroAleatorio) {
    intento = prompt("Adivina el número (entre 1 y 100):");
    contador++;
    if (intento < numeroAleatorio) {
      alert("Intento: " + contador + "\nEl número es mayor");
    } else if (intento > numeroAleatorio) {
      alert("Intento: " + contador + "\nEl número es menor");
    }
  }
  alert("Acertaste! \nIntento: " + contador + "\nEl número era" + numeroAleatorio);
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


// * Ejercicio 5

function ejercicio5() {
  let array = [1, 2, 3, 4, 5];

  let operacion = prompt("Ingresa la operacion: sumar, restar, multiplicar o dividir").toLowerCase();

  let resultado = 0;

if (operacion !== "sumar" && operacion !== "restar" && operacion !== "multiplicar" && operacion !== "dividir") {
  alert("La operacion no es valida");
}

  if (operacion === "sumar") {
    for (let i = 0; i < array.length; i++) {
      resultado += array[i];
    }
    alert("El resultado de sumar " + array + " es: " + resultado);

  }

  if (operacion === "restar") {
    for (let i = 0; i < array.length; i++) {
      resultado -= array[i];
    }
    alert("El resultado de restar " + array + " es: " + resultado);
  }

  if (operacion === "multiplicar") {
    for (let i = 0; i < array.length; i++) {
      resultado *= array[i];
    }
    alert("El resultado de multiplicar " + array + " es: " + resultado);
  }

  if (operacion === "dividir") {
    for (let i = 0; i < array.length; i++) {
      resultado /= array[i];
    }
    alert("El resultado de dividir " + array + " es: " + resultado);
  }
}

// jugamos a agregar y eliminar de un array
function ejercicio6(accion) {
  let array = [1, 2, 3, 4, 5];
  if(accion === 'agregar') {
    array.push(prompt("Array: " + array + " Ingresa un numero para agregar a la array:"));
    alert("Array: " + array);
  }

  if (accion === 'eliminar' ) {
    array.pop(prompt("Array: " + array + ' Ingresa el numero a eliminar:'));
    alert("Array: " + array);
  }
}