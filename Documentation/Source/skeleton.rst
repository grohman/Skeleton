Skeleton
--------

.. image:: https://travis-ci.org/Oktopost/Skeleton.svg?branch=master
    :target: https://travis-ci.org/Oktopost/Skeleton

.. toctree::
	:caption: Contents:
	:maxdepth: 1

Skeleton is an IoC_ library for PHP. 
providing IoC container, injecting dependencies into loaded classes.



Installation
	.. code:: bash
		
		composer require oktopost/skeleton
	

Testing
	.. code:: bash
		
		git clone git@github.com:Oktopost/Skeleton.git
		cd Skeleton
		composer install
		composer test-cover


Composer shortcuts
	- `composer test` - run tests without building coverage data.
	- `composer test-cover` - run tests and build coverage information.

Example
	.. code-block:: php
        
		// Source/App/Base/IUserDAO.php
		interface IUserDAO
		{
			public function load(string $id): User;
		}
		
		// Source/App/DAO/UserDAO.php
		class UserDAO implements IUserDAO
		{
			public function load(string $id): User
			{
				// ...
			}
		}
		
		
		// skeleton-config.php
		$skeleton = new \Skeleton\Skeleton();
		$skeleton->set(App\Base\IUserDAO::class, App\DAO\UserDAO::class);
		
		
		// Obtaining a new instance
		$service = $skeleton->get(Proj\DAO\IUserDAO::class);


.. _IoC: https://en.wikipedia.org/wiki/Inversion_of_control