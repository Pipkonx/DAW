<?php
// Clase Hora

// Crea una clase Hora con atributos para las horas, los minutos y los segundos de la hora

// Incluye, al menos, los siguientes métodos:
//      • Constructor predeterminado con el 00:00:00 como hora por defecto. En el constructor se podrán indicar horas, minutos y segundos.
//      • Asigna(): Permitirá asignar una hora al objeto.
//      • EsValida(): comprobará si la hora es correcta; si no lo es la ajustará. Será un método auxiliar (privado) que se llamará en el constructor parametrizado.
//      • A_Segundos(): devolverá el número de segundos transcurridos desde la medianoche.
//      • De_Segundos(int): hará que la hora sea la correspondiente a haber transcurrido desde la medianoche los segundos que se indiquen.
//      • Segundos_Desde(Hora): devolverá el número de segundos entre la hora y la proporcionada.
//      • Siguiente($nSegundos): Avanzará nSegundos la hora, por defecto será 1 si no se indica otra cosa.
//      • Anterior($nSegundos): Retrocederá nSegundos la hora, por defecto será 1 si no se indica otra cosa.
//      • Copia(): devolverá un clon de la hora.
//      • CargaHoraSistema(): Cargará la hora del sistema.
//      • ToString: Devolverá la hora en una cadena con formato HH:MM:SS.
//      • EsIgual(Hora): indica si la hora es la misma que la proporcionada.
//      • EsMenor(Hora): indica si la hora es anterior a la proporcionada.
//      • EsMayor(Hora): indica si la hora es posterior a la proporcionada. 

// Funciones útiles, estáticas, que nos ayudarán a trabajar con el objeto:
//      • ConvierteASeguntos($hora, $min, $seg): Devuelve el número de seguntos que hay.
//      • ConviertaAHora($segundos): Devuelve un array('hora'=>h, 'min'=>m, 'seg'=>s) que contiene las horas, minutos y segundos que se corresponde a una cantidad de segundos. Si el nº de segundos es mayor que 84600 (1 día), desprecia el día.



//Clase Hora
class Hora
{
    private int $hora;
    private int $min;
    private int $seg;

    // Constructor predeterminado (00:00:00) o con parámetros
    // __construct es para inicializar los atributos de la clase
    public function __construct(int $h = 0, int $m = 0, int $s = 0)
    {
        //recordar que hay que poner siempre this para referirnos a los atributos de la clase
        $this->Asigna($h, $m, $s);
    }

    // Asigna una hora al objeto
    public function Asigna(int $h, int $m, int $s): void
    {
        $this->hora = $h;
        $this->min  = $m;
        $this->seg  = $s;
        $this->EsValida();
    }

    // Ajusta la hora si no es válida
    private function EsValida(): void
    {
        if ($this->seg >= 60) {
            //intdiv es para dividir enteros y devolver el cociente
            $this->min += intdiv($this->seg, 60);
            $this->seg  = $this->seg % 60;
        } elseif ($this->seg < 0) {
            $this->min += intdiv($this->seg, 60) - 1;
            // abs es para devolver el valor absoluto de un numero
            $this->seg  = 60 - abs($this->seg) % 60;
        }

        if ($this->min >= 60) {
            $this->hora += intdiv($this->min, 60);
            $this->min   = $this->min % 60;
        } elseif ($this->min < 0) {
            $this->hora += intdiv($this->min, 60) - 1;
            $this->min   = 60 - abs($this->min) % 60;
        }

        if ($this->hora >= 24) {
            $this->hora = $this->hora % 24;
        } elseif ($this->hora < 0) {
            $this->hora = 24 - abs($this->hora) % 24;
        }
    }

    // Devuelve los segundos desde medianoche
    public function A_Segundos(): int
    {
        return $this->hora * 3600 + $this->min * 60 + $this->seg;
    }

    // Establece la hora desde segundos desde medianoche
    public function De_Segundos(int $totalSegundos): void
    {
        $totalSegundos = $totalSegundos % 86400;
        if ($totalSegundos < 0) $totalSegundos += 86400;
        $this->hora = intdiv($totalSegundos, 3600);
        $resto      = $totalSegundos % 3600;
        $this->min  = intdiv($resto, 60);
        $this->seg  = $resto % 60;
    }

    // Segundos entre esta hora y la proporcionada
    public function Segundos_Desde(Hora $otra): int
    {
        $diff = $this->A_Segundos() - $otra->A_Segundos();
        if ($diff < 0) $diff += 86400;
        return $diff;
    }

    // Avanza n segundos (por defecto 1)
    public function Siguiente(int $nSegundos = 1): void
    {
        $this->De_Segundos($this->A_Segundos() + $nSegundos);
    }

    // Retrocede n segundos (por defecto 1)
    public function Anterior(int $nSegundos = 1): void
    {
        $this->Siguiente(-$nSegundos);
    }

    // Devuelve un clon de la hora
    public function Copia(): Hora
    {
        return clone $this;
    }

    // Carga la hora actual del sistema
    public function CargaHoraSistema(): void
    {
        $now = getDate();
        $this->Asigna($now['hours'], $now['minutes'], $now['seconds']);
    }

    // Representación en cadena HH:MM:SS
    // __toString es para devolver una cadena con la representación del objeto
    public function __toString(): string
    {
        // sprintf es para formatear cadenas
        return sprintf('%02d:%02d:%02d', $this->hora, $this->min, $this->seg);
    }

    // Comparaciones
    public function EsIgual(Hora $otra): bool
    {
        return $this->A_Segundos() === $otra->A_Segundos();
    }

    public function EsMenor(Hora $otra): bool
    {
        return $this->A_Segundos() < $otra->A_Segundos();
    }

    public function EsMayor(Hora $otra): bool
    {
        return $this->A_Segundos() > $otra->A_Segundos();
    }

    // Métodos estáticos útiles
    public static function ConvierteASegundos(int $h, int $m, int $s): int
    {
        return $h * 3600 + $m * 60 + $s;
    }

    public static function ConviertaAHora(int $segundos): array
    {
        $segundos = $segundos % 86400;
        if ($segundos < 0) $segundos += 86400;
        $h = intdiv($segundos, 3600);
        $r = $segundos % 3600;
        $m = intdiv($r, 60);
        $s = $r % 60;
        return ['hora' => $h, 'min' => $m, 'seg' => $s];
    }


    //Funciones de la siguiente actividada
    // Añade minutos a la hora actual del objeto (no estático)
    public function incrementarMinutos(int $numero): int
    {
        return $this->A_Segundos() + $numero * 60;
    }

    // Devuelve los minutos de la hora
    public function getMinutos(): int
    {
        return $this->min;
    }

    // Devuelve la hora (componente)
    public function getHora(): int
    {
        return $this->hora;
    }
}

//* uso
$hora = new Hora();
$constructor = new Hora(0, 0, 0);

echo $hora . "<br>";
echo $constructor . "<br>";

$hora = new DateTime();
$constructor = new DateTime("today 00:00:00");

echo $hora->format('H:i:s') . "<br>";
echo $constructor->format("H:i:s") . "<br>";
