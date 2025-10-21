<?php
// Clase Hora
// Crea una clase Hora con atributos para las horas, los minutos y los segundos de la hora
// Incluye, al menos, los siguientes métodos:
// • Constructor predeterminado con el 00:00:00 como hora por defecto. En el
// constructor se podrán indicar horas, minutos y segundos.
// • Asigna(): Permitirá asignar una hora al objeto.
// • EsValida(): comprobará si la hora es correcta; si no lo es la ajustará. Será un
// método auxiliar (privado) que se llamará en el constructor parametrizado.
// • A_Segundos(): devolverá el número de segundos transcurridos desde la
// medianoche.
// • De_Segundos(int): hará que la hora sea la correspondiente a haber transcurrido
// desde la medianoche los segundos que se indiquen.
// • Segundos_Desde(Hora): devolverá el número de segundos entre la hora y la
// proporcionada.
// • Siguiente($nSegundos): Avanzará nSegundos la hora, por defecto será 1 si no se
// indica otra cosa.
// • Anterior($nSegundos): Retrocederá nSegundos la hora, por defecto será 1 si no se
// indica otra cosa.
// • Copia(): devolverá un clon de la hora.
// • CargaHoraSistema(): Cargará la hora del sistema.
// • ToString: Devolverá la hora en una cadena con formato HH:MM:SS.
// • EsIgual(Hora): indica si la hora es la misma que la proporcionada.
// • EsMenor(Hora): indica si la hora es anterior a la proporcionada.
// • EsMayor(Hora): indica si la hora es posterior a la proporcionada.
// Funciones útiles, estáticas, que nos ayudarán a trabajar con el objeto:
// • ConvierteASeguntos($hora, $min, $seg): Devuelve el número de seguntos que
// hay.
// • ConviertaAHora($segundos): Devuelve un array('hora'=>h, 'min'=>m, 'seg'=>s) que
// contiene las horas, minutos y segundos que se corresponde a una cantidad de
// segundos. Si el nº de segundos es mayor que 84600 (1 día), desprecia el día.