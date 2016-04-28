# Check the HTTP status code for an URL or a set of URL 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/remiheens/http-status-checker.svg?style=flat-square)](https://packagist.org/packages/remiheens/http-status-checker)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/remiheens/HTTPStatusChecker.svg?style=flat-square)](https://scrutinizer-ci.com/g/remiheens/http-status-checker)
[![Total Downloads](https://img.shields.io/packagist/dt/remiheens/http-status-checker.svg?style=flat-square)](https://packagist.org/packages/remiheens/http-status-checker)

This repository provide a CLI tool to check HTTP status for a set of URL.
You can test if the redirection provides you a specific code, if the location is in a right scheme, etc.

## Install

Via Composer

``` bash
composer global require remiheens/http-status-checker
```

## Usage

Test a redirection
```bash
http-status-checker test http://github.com 302 https
```

Test a HTTP Code
```bash
http-status-checker test https://github.com/404 404
```

Test a set of URL

```bash
http-status-checker scan sample.list
```
Be sure, to have separate fields with tabulation. 


It outputs a line per link found.

When check is finished a summary will be shown.

## Security

If you discover any security related issues, please email remi@heens.org instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
