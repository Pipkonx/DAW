const filas = document.querySelectorAll('tr');
filas.forEach((row, idx) => {
    // para qu eno coja la cabecera
    if (idx === 0) return;
    const celda = row.querySelector('td:nth-child(3)');

    if (!celda) return;
    const value = parseInt(celda.textContent);

    if (!isNaN(value) && value < 5) {
        celda.style.backgroundColor = '#ff000097';
    }
    if (isNaN(value)) {
        celda.style.backgroundColor = 'lightgray';
    }
});

const fila = document.querySelectorAll('tr');
fila.forEach(row => {
    const originalBg = row.style.backgroundColor;
    row.addEventListener('mouseenter', () => {
        row.style.backgroundColor = '#87cfeb87';
    });
    row.addEventListener('mouseleave', () => {
        row.style.backgroundColor = originalBg;
    });
});

const overlay = document.querySelector('.image-overlay');
const previewImg = document.querySelector('.preview-img');

document.querySelectorAll('tr').forEach((row, idx) => {
    if (idx === 0) return; // saltar cabecera
    const rutaCell = row.querySelector('td:nth-child(4)');
    if (!rutaCell) return;

    rutaCell.addEventListener('mouseenter', () => {
        const rawPath = rutaCell.textContent.trim();
        //split hace que separe la cadena en un array de substrings, usando como separador cualquier barra diagonal o barra invertida
        //pop() devuelve el último elemento del array, que en este caso es el nombre del archivo
        const fileName = rawPath.split(/[/\\]/).pop();
        previewImg.src = `images/${fileName}`;
        overlay.classList.add('show');
        requestAnimationFrame(() => {
            previewImg.classList.add('fade-in');
        });
    });

    rutaCell.addEventListener('mouseleave', () => {
        previewImg.classList.remove('fade-in');
        overlay.classList.remove('show');
    });
});


