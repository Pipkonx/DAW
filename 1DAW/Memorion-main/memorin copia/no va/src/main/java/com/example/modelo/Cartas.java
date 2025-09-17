package com.example.modelo;

import javafx.scene.control.Button;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;

// Extender de Button para tener acceso a setPrefSize, setMaxSize, etc.
public class Cartas extends Button {
    private final String valor;
    private final Image imagenFrontal;
    private final Image imagenTrasera;
    private boolean descubierta = false;
    private boolean encontrada = false;
    
    public Cartas(String valor, Image frontal, Image trasera) {
        this.valor = valor;
        this.imagenFrontal = frontal;
        this.imagenTrasera = trasera;
        
        // Configurar la apariencia inicial
        ImageView imageView = new ImageView(trasera);
        imageView.setFitWidth(90);
        imageView.setFitHeight(90);
        imageView.setPreserveRatio(true);
        this.setGraphic(imageView);
        
        // Eliminar el fondo y bordes predeterminados del botón
        this.setStyle("-fx-background-color: transparent; -fx-border-color: transparent;");
    }
    
    public void mostrar() {
        if (!encontrada && !descubierta) {
            ImageView imageView = new ImageView(imagenFrontal);
            imageView.setFitWidth(90);
            imageView.setFitHeight(90);
            imageView.setPreserveRatio(true);
            this.setGraphic(imageView);
            descubierta = true;
        }
    }
    
    public void ocultar() {
        if (!encontrada) {
            ImageView imageView = new ImageView(imagenTrasera);
            imageView.setFitWidth(90);
            imageView.setFitHeight(90);
            imageView.setPreserveRatio(true);
            this.setGraphic(imageView);
            descubierta = false;
        }
    }
    
    public void marcarEncontrada() {
        encontrada = true;
        // Opcional: cambiar apariencia para cartas encontradas
        this.setOpacity(0.7);
    }
    
    public boolean esDescubierta() {
        return descubierta;
    }
    
    public boolean esEncontrada() {
        return encontrada;
    }
    
    public String getValor() {
        return valor;
    }
    
    public Image getImagenFrontal() {
        return imagenFrontal;
    }
}
        