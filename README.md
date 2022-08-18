# Parts Index [![Build Status][actions_badge]][actions_link] [![Coverage Status][coveralls_badge]][coveralls_link] [![Version][version-image]][version-url] [![PHP Version][php-version-image]][php-version-url]

This project index data (computers & devices) from https://linux-hardware.org/.

## Installation

```shell
composer install
bin/console doctrine:schema:update --force
symfony serve
```

## License

[MIT](https://github.com/computer-donation/parts-index/blob/main/LICENSE)

[actions_badge]: https://github.com/computer-donation/parts-index/workflows/main/badge.svg
[actions_link]: https://github.com/computer-donation/parts-index/actions

[coveralls_badge]: https://coveralls.io/repos/computer-donation/parts-index/badge.svg?branch=main&service=github
[coveralls_link]: https://coveralls.io/github/computer-donation/parts-index?branch=main

[version-url]: https://packagist.org/packages/computer-donation/parts-index
[version-image]: http://img.shields.io/packagist/v/computer-donation/parts-index.svg?style=flat

[php-version-url]: https://packagist.org/packages/computer-donation/parts-index
[php-version-image]: http://img.shields.io/badge/php-8.1.0+-ff69b4.svg

[expression-language]: https://symfony.com/doc/current/components/expression_language.html
