import Adafruit_DHT

# Sensor type
sensor = Adafruit_DHT.DHT22

# GPIO pin number
pin = 5

humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)

if humidity is not None and temperature is not None:
    print(f"Temp={temperature:0.1f}*C  Humidity={humidity:0.1f}%")
else:
    print("Failed to retrieve data from sensor")
