#Auth/OneKey
### A phpCAS Laravel wrapper for OneKey, an authentication system for healthcare professionals from IQVIA


[![Latest Version on Packagist](https://img.shields.io/packagist/v/vendor_slug/package_slug.svg?style=flat-square)](https://packagist.org/packages/vendor_slug/package_slug)
[![GitHub Tests Action Status](https://github.com/redsnapper/socialite-swiss-rx/workflows/run-tests/badge.svg)](https://github.com/redsnapper/socialite-swiss-rx/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/vendor_slug/package_slug.svg?style=flat-square)](https://packagist.org/packages/vendor_slug/package_slug)

---
This repo can be used to provide SSO authentication with [OneKey] (https://www.iqvia.com/locations/united-states/solutions/life-sciences/information-solutions/essential-information/onekey-reference-assets/onekey-web).

## Installation

You can install the package via composer:

```bash
composer require redsnapper/auth-onekey
```


If you would like to override the default fixed service and callback urls, publish the config file:
```bash
php artisan vendor:publish --tag=onekey-config
```

Once the `onekey.php` has been published, update your `.env` file:
```php

ONE_KEY_SERVICE_URL=<your-service-url>
ONE_KEY_CALLBACK_URL=<your-callback-url>
```

## Usage

Register your routes
```php
//web.php

Route::get('onekey/callback', [OneKeyLoginController::class, 'callback'])->name('one-key.callback');
```

Then in your controller call the `callback()` method:

```php
//Http\Controllers\OneKeyLoginController.php

use RedSnapper\OneKey\OneKeyUser;
use RedSnapper\OneKey\PhpCASBridge;
use Illuminate\Http\RedirectResponse;


public function callback(): RedirectResponse
    {
        // Instantiate the wrapper class
        $provider = new PhpCASBridge;
        //Initialise the client
        $provider->getClient();
        $provider->setNoCasServerValidation()
            ->handleLogoutRequests()
            //Make the authentication call. Redirects to SSO if not authenticated.
            ->forceAuthentication();

        $this->handleProviderCallback($provider->getOneKeyUser());

        return redirect()->intended();
    }

    public function handleProviderCallback(OneKeyUser $providerData)
    {
        // Map OneKey returned user with your User model
        $hcp = new User([
            'id' => $providerData->getId(),
            'name' => $providerData->getFullName(),
            'email' => $providerData->getEmail(),
            'guard' => 'web'
        ]);
        //Authenticate your user using your own auth method, for example:
        session()->put($hcp->guard, $hcp->toArray());
        auth($hcp->guard)->login($hcp);
    }

```

If you want to set a custom fixed service url, you should call the `setFixedServiceUrl(string $url)` on the `PhpCASWrapper` class:

```php
//Http\Controllers\OneKeyLoginController.php

use RedSnapper\OneKey\PhpCASBridge;

$provider = new PhpCASBridge;
//Initialise the client
$provider->getClient();
$provider->setNoCasServerValidation()
    ->handleLogoutRequests()
    ->setFixedServiceUrl('example.com') // or setFixedServiceUrl() to default to
                                        // the value from the config/onekey.php
    //Make the authentication call. Redirects to SSO if not authenticated.
    ->forceAuthentication();
```

## Testing

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
