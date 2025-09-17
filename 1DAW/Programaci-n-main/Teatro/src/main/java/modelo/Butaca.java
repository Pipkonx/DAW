package modelo;

import java.util.Date;

public class Butaca {
    private int id;
    private int fila;
    private int numero;
    private boolean libre;
    private Date fecha_espectacculo;

    public Butaca() {
        this.id = 1;
        this.fila = 1;
        this.numero = 1;
        this.libre = false;
        this.fecha_espectacculo = new Date();
    }

    public int getId() {
        return this.id;
    }

    public int getFila() {
        return fila;
    }

    public void setFila(int fila) {
        this.fila = fila;
    }

    public int getNumero() {
        return numero;
    }

    public void setNumero(int numero) {
        this.numero = numero;
    }

    public boolean isLibre() {
        return libre;
    }

    public void setLibre(boolean libre) {
        this.libre = libre;
    }

    public Date getFecha_espectacculo() {
        return fecha_espectacculo;
    }

    public void setFecha_espectacculo(Date fecha_espectacculo) {
        this.fecha_espectacculo = fecha_espectacculo;
    }

    public void setId(int id) {
        this.id = id;
    }
}
