import pygame
import random
import time

# 1. Configuración inicial de PyGame
pygame.init()

# Dimensiones de la pantalla
ANCHO = 400
ALTO = 600
ventana = pygame.display.set_mode((ANCHO, ALTO))
pygame.display.set_caption("Esquiva los Obstáculos")

# Colores (R, G, B)
BLANCO = (255, 255, 255)
ROJO = (200, 0, 0)
AZUL = (0, 0, 255)
NEGRO = (0, 0, 0)

# 2. Configuración del Jugador
ancho_jugador = 50
alto_jugador = 50
x_jugador = ANCHO // 2 - ancho_jugador // 2
y_jugador = ALTO - alto_jugador - 10
velocidad_jugador = 5

# 3. Configuración de Obstáculos
ancho_obs = 50
alto_obs = 50
x_obs = random.randint(0, ANCHO - ancho_obs)
y_obs = -alto_obs
velocidad_obs = 5

# Variables de juego
puntos = 0
reloj = pygame.time.Clock()
fuente = pygame.font.SysFont("Arial", 25)
tiempo_inicio = time.time()
game_over = False

# 4. Bucle principal del juego
running = True
while running:
    # Gestión de eventos
    for evento in pygame.event.get():
        if evento.type == pygame.QUIT:
            running = False
            
    if not game_over:
        # Movimiento del jugador
        teclas = pygame.key.get_pressed()
        if teclas[pygame.K_LEFT] and x_jugador > 0:
            x_jugador -= velocidad_jugador
        if teclas[pygame.K_RIGHT] and x_jugador < ANCHO - ancho_jugador:
            x_jugador += velocidad_jugador

        # Movimiento del obstáculo
        y_obs += velocidad_obs

        # Si el obstáculo sale por abajo
        if y_obs > ALTO:
            y_obs = -alto_obs
            x_obs = random.randint(0, ANCHO - ancho_obs)
            puntos += 1  # Esquivado con éxito

        # Incrementar velocidad cada 10 segundos
        tiempo_actual = time.time()
        if int(tiempo_actual - tiempo_inicio) % 10 == 0 and int(tiempo_actual - tiempo_inicio) != 0:
            velocidad_obs += 0.01  # Pequeño incremento constante en el bucle

        # Detección de colisión
        jugador_rect = pygame.Rect(x_jugador, y_jugador, ancho_jugador, alto_jugador)
        obstaculo_rect = pygame.Rect(x_obs, y_obs, ancho_obs, alto_obs)

        if jugador_rect.colliderect(obstaculo_rect):
            game_over = True

    # 5. Dibujar en la pantalla
    ventana.fill(BLANCO) # Fondo blanco

    # Dibujar jugador (Cuadrado azul)
    pygame.draw.rect(ventana, AZUL, (x_jugador, y_jugador, ancho_jugador, alto_jugador))

    # Dibujar obstáculo (Rectángulo rojo)
    pygame.draw.rect(ventana, ROJO, (x_obs, y_obs, ancho_obs, alto_obs))

    # Mostrar puntuación
    texto_puntos = fuente.render(f"Puntos: {puntos}", True, NEGRO)
    ventana.blit(texto_puntos, (10, 10))

    if game_over:
        fuente_go = pygame.font.SysFont("Arial", 50, bold=True)
        texto_go = fuente_go.render("GAME OVER", True, NEGRO)
        ventana.blit(texto_go, (ANCHO // 2 - 140, ALTO // 2 - 50))
        # Para cerrar el juego después de perder, podrías pulsar una tecla o esperar
        
    # Actualizar pantalla
    pygame.display.flip()
    reloj.tick(60) # 60 FPS

pygame.quit()
