# Setup steps

Cada vez que se reinicia:

1. chequear nameserver en `/etc/resolv.conf`. Comentar la linea 127.0.0.1 y agregar `nameserver 8.8.8.8`
1. sudo php artisan serve --port 80 --host 192.168.0.47 (reemplazar con la ip local)

Activar pigpiod (para q funcione activar puertos GPIO desde php, sino: "socket_connect() failed ():Connection refused")

1. (antes) sudo pigpiod
1. (ahora): se creó un servicio en `/etc/systemd/system/pigpiod.service` y luego (sudo systemctl enable pigpiod, sudo systemctl start pigpiod)

```bash
  [Unit]
  Description=Pigpio daemon
  After=network.target

  [Service]
  ExecStart=/usr/local/bin/pigpiod
  ExecStop=/bin/systemctl kill -s SIGKILL pigpiod
  Type=forking

  [Install]
  WantedBy=multi-user.target
```

For MCP3008 to work, need to enable the SPI interface on the Raspberry Pi with raspi-config:
Run sudo raspi-config.
Select Interfacing Options.
Select SPI.
Select Yes to enable the SPI interface.
Select Finish to exit.

Para que las queues de laravel funcionen, setear supervisor https://laravel.com/docs/9.x/queues#supervisor-configuration or `php artisan queue:work --timeout=100`

1. sudo supervisorctl start laravel-worker:\*
1. sudo supervisorctl stop laravel-worker:\*

Para que las tareas programadas funcionen, configurar scheduler https://laravel.com/docs/9.x/scheduling#running-the-scheduler or `php artisan schedule:work`

### Web server

Se creó un servicio para correr `php artisan serve` automaticamente

```
[Unit]
Description=Artisan Serve

[Service]
ExecStart=/usr/bin/php /home/ubuntu/invernadero/artisan serve --host=0.0.0.0 --port=8000
WorkingDirectory=/home/ubuntu/invernadero
User=root
Group=root
Restart=always

[Install]
WantedBy=multi-user.target
```

-   sudo systemctl enable artisan-serve
-   sudo systemctl start artisan-serve
-   sudo systemctl stop artisan-serve

Se desactivaron dos servicios de laravel valet
sudo systemctl disable php8.1-fpm.service
sudo systemctl disable dnsmasq
sudo systemctl start nginx

### Steps after deploying

1. cd /home/ubuntu/invernadero
1. sudo systemctl stop artisan-serve
1. php artisan down
1. git pull origin master
1. sudo php artisan cache:clear
1. php artisan queue:restart
1. php artisan up
1. sudo systemctl start artisan-serve
