[![Build Status](https://travis-ci.org/Oktopost/Skeleton.svg?branch=master)](https://travis-ci.org/Oktopost/Skeleton)

# Skeleton
Skeleton is an [Inversion of Control (IoC)](https://en.wikipedia.org/wiki/Inversion_of_control) Library for PHP 7.1. 

[![Build Status](https://travis-ci.org/Oktopost/Skeleton.svg?branch=master)](https://travis-ci.org/Oktopost/Skeleton)
[![Coverage Status](https://coveralls.io/repos/github/Oktopost/Skeleton/badge.svg?branch=master)](https://coveralls.io/github/Oktopost/Skeleton?branch=master)

- [Simple example project](https://github.com/Oktopost/Example-Skeleton)


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
// src/Proj/Base/IUserDAO.php
interface IUserDAO
{
    public function load($id);
}

// src/Proj/DAO/UserDAO.php
class UserDAO implements IUserDAO
{
    public function load($id)
    {
        // ...
    }
}


// skeleton-config.php
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(Proj\Base\IUserDAO::class, Proj\DAO\UserDAO::class);
// or
$skeleton->set("Using any string as key", Proj\DAO\UserDAO::class);


// Obtaining a new instance using
$service = $skeleton->get(Proj\DAO\IUserDAO::class);
// or
$service = $skeleton->get("Using any string as key");
```

In this case, **$service** will be set to a new instance of the **UserDAO** class that was created by Skeleton.

## Autoloading class

Given the following setup:

```php
// src/Proj/Base/IUserDAO.php
interface IUserDAO {}

// src/Proj/Base/IUserService.php
interface IUserService {}

// src/Proj/DAO/UserDAO.php
class UserDAO implements IUserDAO {}


// skeleton-config.php
$skeleton = new \Skeleton\Skeleton();
$skeleton->set(Proj\Base\IUserDAO::class,     Proj\DAO\UserDAO::class);
$skeleton->set(Proj\Base\IUserService::class, Proj\Service\UserService::class);
```

Instance of **UserService** may be obtained *without* autoloading using:

```php
// src/Proj/Service/UserService.php
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

### Using setter methods autolaoding

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * Method must start with the word "set", have only one parameter and the @autoload annotation.
     * Private and protected methods will be also autoloaded.
     */
    public function setUserDAO(IUserDAO $dao)
    {
    }
}

// example.php
$instance = $skeleton->get(IUserService::class);
```

### Using data member autoloading.

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
/**
 * @autoload
 */
class UserService implements IUserService
{
    /**
     * @autoload
     * @var \Full\Path\To\IUserDAO
     * Important: Full path must be defined under the @var annotation.
     */
    private $dao;
}

// example.php
$instance = $skeleton->get(IUserService::class);
```

### Using \__construct autoloading.

In this case the *autoload* annotation is not required for the class name nor for the \__construct method.

```php
// skeleton-config.php
$skeleton->enableKnot();

// src/Proj/Service/UserService.php
class UserService implements IUserService
{
    public function __construct(IUserDAO $dao)
    {
    }
}

// example.php
$instance = $skeleton->get(IUserService::class);
```
