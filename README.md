# Skeleton
Skeleton is an [Inversion of Control (IoC)](https://en.wikipedia.org/wiki/Inversion_of_control) Library for PHP 5.6 and higher.

- Simple example project (Coming soon)
- Full documentation (Coming soon)

## Installation

```shell
composer require oktopost/skeleton
```
or inside *composer.json*
```json
"require": {
    "oktopost/skeleton": "^1.0"
}
```

## Basic Usage Example:

```php
<?php
require_once 'vendor/autoload.php';


interface IUserDAO
{
    public function load($id);
}

class UserDAO implements IUserDAO
{
    public function load($id)
    {
        // ...
    }
}


// This configuration should appear in it's own file. 
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(IUserDAO::class,	            UserDAO::class);
// or
$skeleton->set("Using any string as key",   UserDAO::class);


// Obtaining a new instance using
$service = $skeleton->get(IUserDAO::class);
// or
$service = $skeleton->get("Using any string as key");
```

In this case, **$service** will be set to a new instance of the **UserDAO** class that was created by Skeleton.

## Autoloading class

Given the following setup:

```php
<?php
require_once 'vendor/autoload.php';


interface IUserDAO {}
interface IUserService {}

class UserDAO implements IUserDAO {}


// Given the config
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(IUserDAO::class,	    UserDAO::class);
$skeleton->set(IUserService::class, UserService::class);
```

Instance of **serService** may be obtained *without* autoloading using:

```php
class UserService implements IUserService
{
    public function setUserDAO(IUserDAO $dao)
    {
    }
}

$instance = $skeleton->get(IUserService::class);
$instance->setUserDAO($skeleton->get(IUserDAO::class));
```

But with autoloading you can omit the call to setUserDAO using one of the following.

- Using Setter methods autolaoding

```php
$skeleton->enableKnot();

/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * Method must start with the word set, have only one parameter and the @autoload annotation.
     * Private and protected methods will be also autoloaded.
     */
    public function setUserDAO(IUserDAO $dao)
    {
    }
}

$instance = $skeleton->get(IUserService::class);
```

- Using data member autoloading.

```php
$skeleton->enableKnot();

/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * @var \Full\Path\To\IUserDAO
     * Importent: Full path must be defined under the @var annotation.
     */
    private $dao;
}

$instance = $skeleton->get(IUserService::class);
```

- Using \__constrcut autoloding.

```php
$skeleton->enableKnot();

class UserService implements IUserService
{
	public function __construct(IUserDAO $dao)
	{
		
	}
}

$instance = $skeleton->get(IUserService::class);
```
