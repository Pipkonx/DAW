// Provincias: https://raw.githubusercontent.com/IagoLast/pselect/master/data/provincias.json
// Poblaciones: https://raw.githubusercontent.com/IagoLast/pselect/master/data/municipios.json

function cargarProvincia() {
    fetch('https://raw.githubusercontent.com/IagoLast/pselect/master/data/provincias.json')
        .then((response) => response.json())
        .then((data) => {
            const select = document.querySelector("#provincia");
            for (let i = 0; i < data.length; i++) {
                const option = document.createElement("option");
                option.value = data[i].id;
                option.textContent = data[i].nm;
                select.appendChild(option);
            }
        })
        .catch((error) => console.error(error));
}


function cargarPoblacion() {
    fetch('https://raw.githubusercontent.com/IagoLast/pselect/master/data/municipios.json')
        .then((response) => response.json())
        .then((data) => {
            const select = document.querySelector("#poblacion");
            const provincia = document.querySelector("#provincia").value;
            select.innerHTML = "";
            for (let i = 0; i < data.length; i++) {
                if (data[i].id.startsWith(provincia)) {
                    // console.log(data[i].id);
                    const option = document.createElement("option");
                    option.value = data[i].id;
                    option.textContent = data[i].nm;
                    select.appendChild(option);
                }
            }
        })
        .catch((error) => console.error(error));
}