<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!@include __DIR__ . '/../vendor/autoload.php') {
    echo 'You must set up the project dependencies, run the following commands:' . PHP_EOL.
        'wget http://getcomposer.org/composer.phar' . PHP_EOL.
        'php composer.phar install' . PHP_EOL;

    exit(1);
}
