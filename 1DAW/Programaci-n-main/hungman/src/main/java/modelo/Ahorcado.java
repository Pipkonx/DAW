// palabra mascara intentos
// Ahorcado --> Ahorcado(), CompruebaLetra(char letra ): boolean, hasGanado(): boolean, hasPerdido(): boolean, getFallos():int , mascaraToString(): String  

// vamos a crear un diccionario creando un archivo con palabras y lo vamos a cargar como palabras en el juego del Ahorcado
package modelo;

public class Ahorcado {
    private String palabra;
    private char[] mascara;
    private int fallos;

    // ! coloca la mascara según la palabra
    private void setMascara() {
        this.mascara = new char[this.palabra.length()];
        for (int i = 0; i < this.mascara.length; i++) {
            mascara[i] = '_';
        }
    }

    // ! Comprueba que la palabra sea la misma
    public Ahorcado(String palabra) {
        this.palabra = palabra;
        setMascara();
        this.fallos = 0;
    }

    public boolean hasGanado() {
        for (int i = 0; i < this.mascara.length; i++) {
            if (this.mascara[i] == '_')
                return false;
        }
        return true;
    }

    public void actualizarFallos(int fallos) {
    this.fallos = fallos;
}

    // ! gests
    public int getFallos() {
        return this.fallos;
    }

    public String getPalabra() {
        return this.palabra;
    }

    // ! Método que devuelve la mascara en forma de String
    public String mascaraToString() {
        return new String(this.mascara);
    }

    // ! Método que comprueba si la letra que hemos introducido es una palabra
    public boolean CompruebaLetra(String input) {
        if (input.length() > 1) { // Si es una palabra completa
            if (this.palabra.equals(input)) {
                this.mascara = this.palabra.toCharArray(); // Descubrir toda la palabra
                return true;
            } else {
                this.fallos++;
                return false;
            }
        } else { // Para cuando sea una letra
            char letra = input.charAt(0);
            boolean acierto = false;
            for (int i = 0; i < this.palabra.length(); i++) {
                if (this.palabra.charAt(i) == letra) {
                    this.mascara[i] = letra;
                    acierto = true;
                }
            }
            if (!acierto) {
                this.fallos++;
            }
            return acierto;
        }
    }

    // ! Método que cuenta los fallos
    public boolean hasPerdido() {
        return this.fallos >= 6;
    }

    public void actualizarFallos() {
        this.fallos++;
    }
}