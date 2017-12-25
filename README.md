# Hue Christmas light - Make the lights be all christmas-y

This is a bit of a toy I use to make the lights in the house christmas-y.

It's written in PHP.

## Usage

```
$ php xmas.php
nini.php minuites-to-lights-out hue-ip hue-password lights-to-use-regex
```

## Setup

```
php -r "readfile('https://getcomposer.org/installer');" | php
php composer.phar install
```

Phue has [a guide on how to create a hue password][guide]

[guide]: https://github.com/sqmk/Phue#issuing-commands-testing-connection-and-authorization
