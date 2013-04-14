[![Build Status](https://travis-ci.org/IgorTimoshenko/IMTDataGridBundle.png?branch=master)](https://travis-ci.org/IgorTimoshenko/IMTDataGridBundle)

# IMTDataGridBundle #

## Overview ##

This bundle provides integration with [IMTDataGrid][1].

## Installation ##

### 1. Using Composer (recommended) ###

To install `IMTDataGridBundle` with [Composer][2] just add the following to
your `composer.json` file:

```json
{
    // ...
    "require": {
        // ...
        "imt/data-grid-bundle": "dev-master"
        // ...
    }
    // ...
}
```

Then, you can install the new dependencies by running [Composer][2]'s update
command from the directory where your `composer.json` file is located:

```sh
$ composer.phar update imt/data-grid-bundle
```

Now, [Composer][2] will automatically download all required files, and install
them for you. All that is left to do is to update your `AppKernel.php` file, and
register the new bundle:

```php
<?php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new IMT\DataGridBundle\IMTDataGridBundle(),
    // ...
);
```

## Usage ##

...

## License ##

This bundle is released under the MIT license. See the complete license in the
bundle:

    Resources/meta/LICENSE

[1]: http://github.com/IgorTimoshenko/IMTDataGrid
[2]: http://getcomposer.org