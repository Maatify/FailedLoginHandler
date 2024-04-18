[![Current version](https://img.shields.io/packagist/v/maatify/failed-login-handler)][pkg]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/maatify/failed-login-handler)][pkg]
[![Monthly Downloads](https://img.shields.io/packagist/dm/maatify/failed-login-handler)][pkg-stats]
[![Total Downloads](https://img.shields.io/packagist/dt/maatify/failed-login-handler)][pkg-stats]
[![Stars](https://img.shields.io/packagist/stars/maatify/failed-login-handler)](https://github.com/maatify/failed-login-handler/stargazers)

[pkg]: <https://packagist.org/packages/maatify/failed-login-handler>
[pkg-stats]: <https://packagist.org/packages/maatify/failed-login-handler/stats>

# PostValidatorJsonCode

maatify.dev Failed Login Handler, known by our team


# Installation

```shell
composer require maatify/failed-login-handler
```
    
## Important
Don't forget to use \App\DB\DBS\DbConnector;


Don't forget to create Class App\Assist\AppFunctions

```php
<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-01-13
 * Time: 09:36 AM
 * https://www.Maatify.dev
 */

namespace App\Assist;

use Maatify\Functions\GeneralFunctions;

class AppFunctions
{
    const pagination_limit = 10;

    public static function PortalUrl(): string
    {
        return GeneralFunctions::HostUrl() . 'portal/';
    }

    public static function SiteUrl(): string
    {
        return GeneralFunctions::HostUrl();
    }

    public static function HeaderMeta(string $title, string $description): void
    {
        echo '
        <meta name="description" content="' . $description . '">
        <meta property="og:title" content="' . $title . '" />
        <meta property="og:site_name" content="' . $title . '">
        <meta property="og:type" content="website" />
        <meta property="og:url" content="' . GeneralFunctions::CurrentUrl() . '">
        <meta property="og:image" content="' . GeneralFunctions::HostUrl() . 'images/logo.png" />
        <meta property="og:description" content="' . $description . '">
        <title>' . $title . '</title> 
            ';
    }

    public static function CurrentDateTime(): string
    {
        return date("Y-m-d H:i:s", time());
    }

    public static function CurrentTime(): string
    {
        return date("H:i:s", time());
    }

    public static function CurrentDate(): string
    {
        return date("Y-m-d", time());
    }

    public static function IP(): string
    {
        return GeneralFunctions::UserIp();
    }

    public static function DefaultDateTime(): string
    {
        return '1900-01-01 00:00:00';
    }

}

```