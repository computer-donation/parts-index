# Parts Index [![Build Status][actions_badge]][actions_link] [![Coverage Status][coveralls_badge]][coveralls_link] [![PHP Version][php-version-image]][php-version-url]

This project index data (computers & devices) from https://linux-hardware.org/.

## Installation

```shell
composer install
bin/console doctrine:schema:update --force
bin/console app:index-cpu --no-debug
bin/console app:index-pci --no-debug
symfony serve
```

## Test

```shell
bin/phpunit
```

## License

[MIT](https://github.com/computer-donation/parts-index/blob/main/LICENSE)

[actions_badge]: https://github.com/computer-donation/parts-index/workflows/main/badge.svg
[actions_link]: https://github.com/computer-donation/parts-index/actions

[coveralls_badge]: https://coveralls.io/repos/computer-donation/parts-index/badge.svg?branch=main&service=github
[coveralls_link]: https://coveralls.io/github/computer-donation/parts-index?branch=main

[php-version-url]: https://packagist.org/packages/computer-donation/parts-index
[php-version-image]: http://img.shields.io/badge/php-8.1.0+-ff69b4.svg
