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
  if (accion === 'agregar') {
    array.push(prompt("Array: " + array + " Ingresa un numero para agregar a la array:"));
    alert("Array: " + array);
  }

  if (accion === 'eliminar') {
    array.pop(prompt("Array: " + array + ' Ingresa el numero a eliminar:'));
    alert("Array: " + array);
  }
}


function ejercicio7() {
  let array = [1, 2, 3, 4, 5];
  let arrayPar = [];
  for (let i = 0; i < array.length; i++) {
    if (array[i] % 2 === 0) {
      arrayPar.push(array[i] * 2);
    }
  }
  alert("Array original: " + array + "\nArray par: " + arrayPar)
}

function ejercicio8() {
  let array1 = [2, 3, 8, 6, 1];
  let array2 = [10, 3, 6, 20];
  let arrayRepe = [];
  for (let i = 0; i < array1.length; i++) {
    for (let x = 0; x < array2.length; x++) {
      if (array1[i] === array2[x]) {
        arrayRepe.push(array1[i]);
      }
    }
  }
  alert("Array 1: " + array1 + "\nArray 2: " + array2 + "\nNumeros repetidos de los Arrays: " + arrayRepe);
}

//? Concateno ambas array luego lo recorro agregando a otro array los numeros que no estan repetidos
// * indexOf() compara el valor con los elementos de un array
// * concat() para unir los dos arrays

function ejercicio9() {
  let array1 = [2, 3, 8, 6, 1];
  let array2 = [10, 3, 6, 20];
  let array3 = array1.concat(array2);
  let arrayNoRepe = [];
  for (let i = 0; i < array3.length; i++) {
    if (arrayNoRepe.indexOf(array3[i]) === -1) {
      arrayNoRepe.push(array3[i]);
    }
  }
  alert("Array 1: " + array1 + "\nArray 2: " + array2 + "\nNumeros no repetidos de los Arrays: " + arrayNoRepe);
}

function Persona(nombre, edad, genero) {
  this.nombre = nombre;
  this.edad = edad;
  this.genero = genero;
}

function ejercicio10() {
  let persona1 = new Persona("Rafa", 25, "masculino");
  let persona2 = new Persona("Pepito", 30, "masculino");
  let persona3 = new Persona("Maria", 25, "femenino");

  let cadena = "";
  let personas = [persona1, persona2, persona3];

  for (let i = 0; i < personas.length; i++) {
    cadena += personas[i].nombre + " " + personas[i].edad + " " + personas[i].genero + "\n";
  }
  alert(cadena);
}