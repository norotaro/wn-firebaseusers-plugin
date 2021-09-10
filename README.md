# Firebase for WinterCMS

Synchronize users between Firebase and `Winter.Users`.

- [Installation](#installation)
- [Usage](#usage)
- [Support](#support)
- [License](#license)

## Installation

First install the plugin with composer:
```sh
composer require norotaro/wn-firebaseusers-plugin
```

Then run the migration files with:

```sh
php artisan winter:up
```

## Usage
After configure [Firebase Plugin](https://github.com/norotaro/wn-firebase-plugin/#configuration) you can run the following command for synchronize user:

```sh
php artisan firebaseusers:sync
```

### Protect endpoints
You can use the `AuthenticationControl` middleware for protect an endpoint, this middleware expect a valid bearer token that is verified against Firebase.

```php
<?php
Route::middleware([\Norotaro\FirebaseUsers\Middlewares\AuthenticationControl::class])
    ->group(function () {
    // All routes here will require a valid Firebase bearer token
});
```

## Suport

- [Issue Tracker (WinterCMS Plugin)](https://github.com/norotaro/wn-firebaseusers-plugin/issues/)

## License

FirebaseUsers for WinterCMS is licensed under the [MIT License](LICENSE).

Your use of Firebase is governed by the [Terms of Service for Firebase Services](https://firebase.google.com/terms/).
