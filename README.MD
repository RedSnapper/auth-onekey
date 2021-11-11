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


But default, the package is set to the `debug = false` mode. If you would like to override it, publish the config file:
```bash
php artisan vendor:publish --tag=onekey-config
```

Once the `onekey.php` config file has been published, update your `.env` file:
```php

ONE_KEY_DEBUG=<boolean>
```

## Usage

Register your route
```php
//web.php

Route::get('onekey/callback', [OneKeyLoginController::class, 'handleProviderCallback']);
```

Then, implement the `handleProviderCallback()` method in your controller. For example:

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
###All available methods for getting user data:

```php
$user = $provider->user();

$user->getId(); //?string 
$user->getEmail(); //?string 
$user->getFirstName(); //?string 
$user->getLastName(); //?string 
$user->getFullName(); //?string 
$user->getCity(); //?string 
$user->getProfession(); //?string 
$user->getPhone(); //?string
$user->isHCP(); //bool
$user->trustLevel(); //int

$user->getRaw(); //array
```

## Testing

```bash
vendor/bin/phpunit
```
or 
```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.MD) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.MD) for details.

## License

The MIT License (MIT). Please see [Licence File](LICENCE.MD) for more information.
