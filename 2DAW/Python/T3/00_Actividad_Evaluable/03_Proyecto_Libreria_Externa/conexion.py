import yfinance as yf
# ! Tutorial https://fortinux.com/python/tutorial-yahoo-finance-para-consultar-acciones-y-titulos-de-la-bolsa/
# ! Tutorial 2 https://ranaroussi.github.io/yfinance/reference/yfinance.ticker_tickers.html
def obtener_precio_actual(simbolo):
    """
    Busca el precio más reciente de un activo (como BTC-USD o XRP)
    usando la librería yfinance.
    """
    try:
        # Creamos una conexión con el activo usando su identificador (ticker)
        activo = yf.Ticker(simbolo)
        
        # Pedimos el historial del último día
        # datos = activo.history(period="1d")
        datos = activo.fast_info
        
        # Si no hay datos o el tinker no existe devolvemos nad apara el error
        # if datos.empty:
            # return None
        
        # Cogemos el último precio de cierre disponible
        # precio_actual = datos['Close'].values[-1]

        # Si el tincker no existe last price 
        precio_actual = datos['last_price']

        if precio_actual is None or str(precio_actual) == 'nan':
            return None
        
        # Lo devolvemos redondeado a 2 decimales para que sea más legible
        return round(precio_actual, 2)
    except Exception as error:
        print(f"Error Problema al consultar el precio: {error}")
        return None