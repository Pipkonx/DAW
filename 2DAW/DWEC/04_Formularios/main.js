function sumar() {
    let num1 = parseInt(document.querySelector("#numero1").value);
    let num2 = parseInt(document.querySelector("#numero2").value);

    // if (document.querySelector("#numero1").length == null || document.querySelector("#numero2").length == null) {
    //     alert("Debes introducer un valor");
    // }

    // if (isNaN(num1) || isNan(num2)) {
    //         alert("Debes introducir dos valores");
    // }


    if (document.querySelector("#numero1").value == "" || document.querySelector("#numero2").value == "") {
        alert("Debes introducir dos valores");
    } else {
        let suma = num1 + num2;
        document.querySelector("#resultado").innerHTML = "La suma es : " + suma;
    }
}



var votos = [0, 0, 0];
var totalVotos = 0;

function votar() {
    let votacion = document.getElementsByName("votacion");
    for (let i = 0; i < votacion.length; i++) {
        if (votacion[i].checked) {
            // alert("Has votado " + votacion[i].value);
            votos[i]++;
            totalVotos++;
            // alert("Has votado " + votos[i]);
            // alert("Votos de si: " + votos[0] + "  votos de no: " + votos[1] + "  votos de s/n: " + votos[2]);
        }
        // let porsi = (votos[0] / totalVotos * 100) + "%";
        // let porno = (votos[1] / totalVotos * 100) + "%";
        // let porsino = (votos[2] / totalVotos * 100) + "%";
        let porsi = (votos[0] / totalVotos * 100).toFixed(2);
        let porno = (votos[1] / totalVotos * 100).toFixed(2);
        let porsino = (votos[2] / totalVotos * 100).toFixed(2);

        document.getElementById("votoSi").innerHTML = votos[0] + " / " + totalVotos + " " + porsi + "%";
        // document.getElementById("votoSi").innerHTML = votos[0] + " / " + totalVotos + " " + (votos[0] / totalVotos * 100).toFixed(2) + "%";
        document.getElementById("votoNo").innerHTML = votos[1] + " / " + totalVotos + "  " + porno + "%";
        document.getElementById("votoSiNo").innerHTML = votos[2] + " / " + totalVotos + "  " + porsino + "%";

        // document.getElementById("img1").style.width = 5 * porsi;
        document.getElementById("img1").width = 5 * porsi;
        document.getElementById("img2").width = 5 * porno;
        document.getElementById("img3").width = 5 * porsino;
    }
}

function anade() {
    let pais = document.querySelector("#pais");
    let opcion = document.createElement("option");
    opcion.value = pais.value;
    opcion.innerHTML = pais.value;
    document.getElementById("country").appendChild(opcion);
}