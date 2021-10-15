# Laravel Docs Generator

This package provide smart commands for Swagger / OpenApi annotations to generate documentation

# Installation

You can install package via composer. Add repository to your composer.json

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mindz-team/laravel-docs-generator"
        }
    ],

And run

    composer require mindz-team/laravel-docs-generator

Publish config file

    php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
    php artisan vendor:publish  --provider="Mindz\LaravelDocsGenerator\LaravelDocsGeneratorServiceProvider" --tag="config"

## Cookie based auth in SWAGGER

To use swagger based on cookie based auth modify file `resources/views/vendor/l5-swagger/index.blade.php` and set `requestInterceptor` as follows

    requestInterceptor: function (request) {
        request.headers['X-XSRF-TOKEN'] = getCookie('XSRF-TOKEN');
        return request;
    },

and add method

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = decodeURIComponent(document.cookie).split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

# Perquisites

Package is based on [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) library. Make sure you are familiar with its
installation and configuration guide.

# Usage

A package contains set of commands that helps to generate swagger annotations and speed up documentation process
radically.

## Init documentation

It generates a file (default. Annotations.php) that initializes swagger documentation.

```shell
php artisan docs-generate:init-documentation
```

Arguments:

`none`

Options:

`--file` - name of file that stores Annotations to initiates swagger. Default is `annotations`

It returns a list of variables with prefilled values that should be pasted into `config/l5-swagger.php` in `constants`
section. It is importatn that if you do not want to use default values you should change them in `contig/l5-swagger.php`
or use constants defined in `.env`

## Crud controller documentation

It Generate swagger documentation for crud controller.

```shell
php artisan docs-generate:crud-controller
```

Arguments:

`name` - Resource name

Options:

`--schema` - Custom schema name for resource (default is based on resource name)<br>
`--tag` - Documentation tag for resource (default is based on resource name)<br>
`--only` - Comma separated action that should only be generated in case your controller does not provide whole CRUD<br>
`--except` - Comma separated action that should **not** be generated in case your controller does not provide whole
CRUD<br>
`--security` - Endpoint security. Default is `bearerAuth`

Command generates ready to go annotations. To complete docs you should fill your schema fill according to example. In case you do not know how to generate annotations please refer to [this](https://github.com/zircote/swagger-php/tree/master/Examples/petstore-3.0/models) document.
If no limitations have been provided action should generate `Index.php`,`Show.php`,`Store.php`,`Update.php`,`Delete.php` files in resource based folder name.

## Single endpoint documentation

Generate swagger documentation for single endpoint

```shell
php docs-generate:endpoint
```

Arguments:

`path` - Endpoint you want to document without prefix (ex. `/users` or `/users/{user}`)

Options:

`--summary` - Short description of endpoint<br>
`--method` - Http methods like: GET,POST,UPDATE,DELETE<br>
`--tag` - Documentation tag for resource (default is based on last path segment)<br>
`--security` - Endpoint security. Default is `bearerAuth`
`--success` - Response code in case of success. Default is `200`

Command generates single file based on `tag` and `path`. Which contains documentation for single endpoint. Dy default this endpoint is schemaless. Custom schema is available only for `POST` and `UPDATE` methods.

Additionally - if you uses parameter in the path like `/users/{user}` the query string parameter will be created automatically with name according to path parameter and default (integer) type


## Fortify endpoints documentation

Generate swagger documentation fortify functionalities from [laravel/fortify](https://github.com/laravel/fortify) package 

```shell
php docs-generate:fortify
```

Arguments:

`none`

Options:

`none`

To determine which endpoints documentation should be published set them in config file `config/docs-generator.php`


# Configuration

You can determine where annotations files should be stored in your file system using config file published during installation `config/docs-generator.php`

# Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

# Security

issue tracker.

# Credits

Author: Roman Szymański [r.szymanski@mindz.it](mailto:r.szymanski@mindz.it)

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
