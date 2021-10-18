#Auth/OneKey
### A phpCAS Laravel wrapper for OneKey, an authentication system for healthcare professionals from IQVIA


[![Latest Version on Packagist](https://img.shields.io/packagist/v/rs/auth-onekey.svg?style=flat-square)](https://packagist.org/packages/rs/auth-onekey)
[![GitHub Tests Action Status](https://github.com/redsnapper/auth-onekey/workflows/run-tests/badge.svg)](https://github.com/redsnapper/auth-onekey/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/rs/auth-onekey.svg?style=flat-square)](https://packagist.org/packages/rs/auth-onekey)

---
This repo can be used to provide SSO authentication with [OneKey] (https://www.iqvia.com/locations/united-states/solutions/life-sciences/information-solutions/essential-information/onekey-reference-assets/onekey-web).

## Installation

You can install the package via composer:

```bash
composer require rs/auth-onekey
```


If you would like to override the default debug mode and callback urls, publish the config file:
```bash
php artisan vendor:publish --tag=onekey-config
```

Once the `onekey.php` has been published, update your `.env` file:
```php

ONE_KEY_DEBUG=<boolean>
ONE_KEY_CALLBACK_URL=<string:your-callback-url>
```

## Usage

Register your routes
```php
//web.php

Route::get('onekey/redirect', [OneKeyLoginController::class, 'redirectToProvider']);
Route::get('onekey/callback', [OneKeyLoginController::class, 'handleProviderCallback']);
```

Then in your controller call the `redirectToProvider()` method:

```php
//Http\Controllers\OneKeyLoginController.php

use RedSnapper\OneKey\OneKeyProvider;

public function redirectToProvider(OneKeyProvider $provider)
{
    return $provider->redirect();
}

```

Then, in the same controller, implement the `handleProviderCallback()` method. For example:

```php
//Http\Controllers\OneKeyLoginController.php

use RedSnapper\OneKey\OneKeyProvider;

//..//

public function handleProviderCallback(OneKeyProvider $provider)
{   // Get OneKey user from the provider
    $providerUser = $provider->user();
    
    //Map the one-key user with your User model, save in DB
    // or perform any other action
    $user = new User([
        'id' => $providerUser->getId(),
        'name' => $providerUser->getFullName(),
        'email' => $providerUser->getEmail(),
        'guard' => 'web'
    ]);
    
    session()->put($user->guard, $user->toArray());
    auth($user->guard)->login($user);

    return redirect()->intended();
}
```
**Make sure your callback url is first registered with the OneKey service.**

## Testing

```bash
vendor/bin/phpunit
```
or 
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [Licence File](LICENCE.md) for more information.
