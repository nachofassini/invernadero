# SPDX-FileCopyrightText: 2021 ladyada for Adafruit Industries
# SPDX-License-Identifier: MIT

import adafruit_dht
import board

# Initial the dht device, with data pin connected to:
dhtDevice = adafruit_dht.DHT22(board.D4)

# you can pass DHT22 use_pulseio=False if you wouldn't like to use pulseio.
# This may be necessary on a Linux single board computer like the Raspberry Pi,
# but it will not work in CircuitPython.
# dhtDevice = adafruit_dht.DHT22(board.D18, use_pulseio=False)

try:
    # Print the values to the serial port
    temperature = dhtDevice.temperature
    humidity = dhtDevice.humidity
    print(f"Temp={temperature:0.1f}*C  Humidity={humidity:0.1f}%")

except RuntimeError as error:
    # Errors happen fairly often, DHT's are hard to read, just keep going
    print(error.args[0])
except Exception as error:
    dhtDevice.exit()
    raise error
