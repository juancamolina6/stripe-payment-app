# Stripe Payment App

## Instrucciones para Ejecutar la Aplicación

### version de php

PHP 8.2.20

### version de composer

2.7.7

### version de laravel

laravel/framework": "^11.9

### Navegar al directorio del proyecto:

cd stripe-payment-app

### Instalar las dependencias:

composer install

Configurar las claves de API de Stripe en el archivo .env:

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
STRIPE_WEBHOOK_SECRET=your_webhook_secret

### Generar la clave de la aplicación:

php artisan key:generate

### Ejecutar las migraciones (si es necesario):

php artisan migrate

### Iniciar el servidor:

php artisan serve

## Configurar el webhook en el panel de Stripe

apuntando a https://localhost.com:puerto/webhook.
