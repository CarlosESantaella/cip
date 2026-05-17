# CIP - Centro de Informática del Paraguay

Sitio WordPress para **cip.org.uy**.

## Stack

- WordPress 6.x
- PHP 7.2.34+
- MariaDB
- Tema: Astra + child theme (`astra-child`)
- Editor: Gutenberg + Spectra (Ultimate Addons for Gutenberg)
- Caché: LiteSpeed Cache

## Entorno local

- Servidor: Laragon (Windows)
- URL local: `http://cip-wordpress.test`
- Document root: `C:\laragon\www\cip-wordpress`

## Setup

```bash
# Importar base de datos
mysql -u root -p < u449780709_8LoRF.sql

# Ajustar credenciales en wp-config.php
# Search-replace dominio producción -> local
wp search-replace 'https://cip.org.uy' 'http://cip-wordpress.test' --all-tables
```

## Producción

- URL: https://cip.org.uy
- Hosting: Hostinger
