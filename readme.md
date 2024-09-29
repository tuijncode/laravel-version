# Laravel Version

<a href="https://packagist.org/packages/tuijncode/laravel-version"><img src="https://poser.pugx.org/tuijncode/laravel-version/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/tuijncode/laravel-version"><img src="https://poser.pugx.org/tuijncode/laravel-version/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/tuijncode/laravel-version"><img src="https://poser.pugx.org/tuijncode/laravel-version/license.svg" alt="License"></a>

![Html](https://cdn.tuijncode.com/github/laravel-version.png)

With Laravel Version, you can retrieve various versions within your project, which is particularly useful if you have multiple projects and need to identify which ones require updates.

## Install

Install the package via Composer:

```sh
composer require tuijncode/laravel-version
```

Publish the package’s configuration file:

```sh
php artisan vendor:publish
```
Select the following option:

```
Provider: Tuijncode\LaravelVersion\ServiceProvider
```

Add your token to the .env file:

```
TUIJNCODE_LARAVEL_VERSION_TOKEN="your-token"
```

## Usage

https://example.com/tuijncode/laravel-version?token=your-token

## Response (JSON)

```yaml
{
    "status": "OK",
    "versions":
        {
            "webserver":
                {
                    "name": "Apache",
                    "version": "Apache\/2.4.58 (Unix) mod_wsgi\/4.9.4 Python\/3.11 mod_fastcgi\/mod_fastcgi-SNAP-0910052141 OpenSSL\/1.1.1u"
                },
            "laravel":
                {
                    "name": "Laravel",
                    "version": "11.23.5"
                },
            "database":
                {
                    "name": "mysql",
                    "version": "8.0.35"
                },
            "php":
                {
                  "name": "cgi-fcgi",
                  "version": "8.2.20"
                }
        }
}
```
