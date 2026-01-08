# El Vigilante de Criptos 🚀

Proyecto para automatizar un poco el seguimiento de las finanzas. La idea : una aplicación de consola que te avisa cuando una cripto o una acción llega al precio que tú quieras. Así no tienes que estar mirando la pantalla todo el día.

## ¿Qué hace este programa?
Básicamente, le dices qué moneda quieres vigilar (por ejemplo, `BTC-USD` para Bitcoin), le pones un precio límite y le dices si quieres que te avise cuando suba o cuando baje. El programa se queda mirando el precio por ti y te suelta un aviso cuando se cumple la condición.

### Lo más chulo:
- **Precios al momento**: Usa datos reales del mercado.
- **Tú mandas**: Configuras las alertas como te venga mejor.
- **Sin complicaciones**: Te avisa por consola de forma clara y directa.

## ¿Cómo lo pongo en marcha?
Primero, necesitas instalar la librería que hace la magia de los precios:

```bash
pip install yfinance
```

Luego, solo tienes que lanzar el archivo principal:

```bash
python principal.py
```

## ¿Cómo está organizado?
He dividido el código en tres partes para que sea más fácil de entender y de tocar si hace falta:
- **`conexion.py`**: Aquí es donde ocurre la charla con `yfinance` para traer el precio actual.
- **`alertas.py`**: Aquí está la lógica que compara los precios para ver si tiene que saltar el aviso.
- **`principal.py`**: Este es el que manejas tú. Es la interfaz que te pide los datos y donde corre todo el bucle de vigilancia.

## Las herramientas que he usado
- **yfinance**: Para sacar los datos del mercado sin romperme la cabeza.
- **time**: Para que el programa espere unos segundos entre consulta y consulta (y no agobiar a la API).
- **os**: Para limpiar la consola y que se vea todo bien ordenadito.
