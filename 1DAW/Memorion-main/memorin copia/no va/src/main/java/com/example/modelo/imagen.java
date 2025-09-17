package com.example.modelo;

import javafx.scene.image.Image;

public class Imagen {
    private final Image imagen;

    /**
     * Carga una imagen desde la carpeta de recursos /imagenesCartas/
     * @param nombreArchivo El nombre del archivo, por ejemplo "camavinga.jpg"
     */
    public Imagen(String nombreArchivo) {
        String ruta = "/imagenesCartas/" + nombreArchivo;
        var url = getClass().getResource(ruta);
        if (url == null) {
            throw new IllegalArgumentException("No se encontró la imagen: " + ruta);
        }
        this.imagen = new Image(url.toExternalForm());
    }

    public Image getImage() {
        return imagen;
    }
}