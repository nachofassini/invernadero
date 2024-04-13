# Setup steps

Cada vez que se reinicia:

1. chequear nameserver en /etc/resolv.conf. Comentar la linea 127.0.0.1 y agregar `nameserver 8.8.8.8`
1. sudo pigpiod (para q funcione activar puertos GPIO desde php, sino: "socket_connect() failed ():Connection refused")
1. sudo php artisan serve --port 80 --host 192.168.0.47 (reemplazar con la ip local)

For MCP3008 to work, need to enable the SPI interface on the Raspberry Pi with raspi-config:
Run sudo raspi-config.
Select Interfacing Options.
Select SPI.
Select Yes to enable the SPI interface.
Select Finish to exit.

Para que las queues de laravel funcionen, setear supervisor https://laravel.com/docs/9.x/queues#supervisor-configuration or `php artisan queue:work --timeout=100`

Para que las tareas programadas funcionen, configurar scheduler https://laravel.com/docs/9.x/scheduling#running-the-scheduler or `php artisan schedule:work`
