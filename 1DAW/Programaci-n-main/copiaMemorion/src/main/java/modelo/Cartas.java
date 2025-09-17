package modelo;

import javafx.scene.control.Button;

public class Cartas extends Button {
    private int id;
    private boolean volteada;

    public Cartas() {
        this.id = -1;  
        this.volteada = false;
    }
    
    public Cartas(int id) {
        this.id = id;
        this.volteada = false;
    }

    public void setId(int id) {
        this.id = id;
    }
    
    public boolean isVolteada() {
        return volteada;
    } 
    
    public void setVolteada(boolean volteada) {
        this.volteada = volteada;
    }
}
