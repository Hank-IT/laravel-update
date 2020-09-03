# An update solution for Laravel apps

This Laravel package helps you with deploying updates to your application.

## Installation
```
composer require mrcrankhank/laravel-update
```

If you run Laravel <= 5.4, add the service provider to the 'providers' array in config/app.php:
```
MrCrankHank\LaravelUpdate\UpdateServiceProvider
```

Publish config file
```
php artisan vendor:publish
```

Configure your 'ignore_dirs' and 'ignore_files' in `config/update.php`.

## Usage
1. Build your application
2. Call `php artisan update:generate-json-file`, this will generate a json file containing all paths to the files which are part of your current app.
3. Copy your new build over the current deployed application.
4. Call `php artisan update:run`, this generates a second json file with all current files and compares it with the one
   created in your deployment process. You will get a list of all files which are currently there,
   but are not part of the update you just deployed (of course you can ignore specific directories). You will then be able to
   delete the files which are not part of your installation. This prevents any deleted files from libraries lying around in 
   your installation.

> The Update command also migrates the database, clears the cache and puts your app into maintenance mode while updating.

The exact commands are (in this order):

```
php artisan down
php artisan migrate --force
php artisan clear-compiled
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan up
```

##### Deployment process
```
php artisan update:generate-json-file
```

##### Update
Extract your update over the productive installation
```
php artisan update:run
```

## Compatibility
This package was developed in Laravel 5.5, but should work in older versions as well.

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
