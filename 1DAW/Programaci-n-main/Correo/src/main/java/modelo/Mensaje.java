package modelo;

import java.sql.Timestamp;

public class Mensaje {
    private String emisor;
    private String receptor;
    private String mensaje;
    private Timestamp fecha;

    public Mensaje(String emisor, String receptor, String mensaje, Timestamp fecha) {
        this.emisor = emisor;
        this.receptor = receptor;
        this.mensaje = mensaje;
        this.fecha = fecha;
    }

    // Constructor adicional que establece la fecha actual automáticamente
    public Mensaje(String emisor, String receptor, String mensaje) {
        this.emisor = emisor;
        this.receptor = receptor;
        this.mensaje = mensaje;
        this.fecha = new Timestamp(System.currentTimeMillis());
    }

    public String getEmisor() {
        return emisor;
    }

    public void setEmisor(String emisor) {
        this.emisor = emisor;
    }

    public String getReceptor() {
        return receptor;
    }

    public void setReceptor(String receptor) {
        this.receptor = receptor;
    }

    public String getMensaje() {
        return mensaje;
    }

    public void setMensaje(String mensaje) {
        this.mensaje = mensaje;
    }

    public Timestamp getFecha() {
        return fecha;
    }

    public void setFecha(Timestamp fecha) {
        this.fecha = fecha;
    }

    @Override
    public String toString() {
        return "De: " + emisor + " Para: " + receptor + "\nFecha: " + fecha + "\n" + mensaje;
    }
}
