package modelo;

import java.io.Serializable;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class Clasificacion implements Serializable {
    private static final long serialVersionUID = 1L;
    
    private String email;
    private String nombre;
    private long tiempo;
    private LocalDateTime fecha;
    
    private static final DateTimeFormatter FORMATTER = DateTimeFormatter.ofPattern("dd/MM/yyyy");

    public Clasificacion(String email, String nombre, long tiempo) {
        this.email = email;
        this.nombre = nombre;
        this.tiempo = tiempo;
        this.fecha = LocalDateTime.now();
    }

    public Clasificacion(String email, String nombre, long tiempo, LocalDateTime fecha) {
        this.email = email;
        this.nombre = nombre;
        this.tiempo = tiempo;
        this.fecha = fecha;
    }

    public String getNombreJugador() {
        return nombre;
    }

    public String getEmail() {
        return email;
    }

    public long getTiempo() {
        return tiempo;
    }

    //FORMATTER para no mostrar la hora
    public String getFecha() {
        return fecha.format(FORMATTER);
    }
    
    public LocalDateTime getFechaDateTime() {
        return fecha;
    }

    public void setTiempo(long tiempo) {
        this.tiempo = tiempo;
    }

    public void setFecha(LocalDateTime fecha) {
        this.fecha = fecha;
    }

    @Override
    public String toString() {
        // para no mostrar la hora
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd/MM/yyyy");
        String fechaFormateada = fecha.format(formatter);
        
        return nombre + " - " + tiempo + "s - " + fechaFormateada;
    }
}
