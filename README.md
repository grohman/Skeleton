# Skeleton
Skeleton is an [Inversion of Controller (IoC)](https://en.wikipedia.org/wiki/Inversion_of_control) Library for PHP 5.6 and higher.

Skeleton consists of the next main features:

1. Autoloading the skeletons configuration.
2. Resolving class data *members*, *setter methods* and *\__constructor* parameters when a class is obtained through skeleton.
3. The **GlobalSkeleton** singleton class allows to connect different *skeleton* instances to access classes of another library.

## Basic Usage Example:

```php
interface IMyInterface
{
	
}

class MyInterfaceImplementer implements IMyInterface
{
	
}

$skeleton = new \Skeleton\Skeleton();
$skeleton->set(IMyInterface::class, MyInterfaceImplementer::class);

$instance = $skeleton->get(IMyInterface::class);
```

In this case **$instance** will be set to a new instance of the **MyInterfaceImplementer** class.
Note that **IMyInterface** interface was used as the key her, both to register and later obtain an instance of the implementing class. However for **Skeleton** it's unimportant what key is used. Using

```php
$skeleton->set("some key", MyInterfaceImplementer::class);
$instance = $skeleton->get("some key");
```

would produce the same result. 

## Resolving class members, setters and constructor
### Data Members

Folloing will work on private, protected and public data members.

```php
namespace SomeNamespace;


interface IMyInterface {}
class MyInterfaceImplementer implements IMyInterface {}


/**
 * @autoload
 */
class MyClassWithDataMember
{
	/**
	 * @autoload
	 * @var /SomeNamespace/IMyInterface
	 */
	private $member;
	
	protected function setMember(IMyInterface $member) 
	{
		
	}
	
	public function __construct(IMyInterface $member = null)
	{
		
	}
}

$skeleton = new \Skeleton\Skeleton();
$skeleton->enableKnot();
$skeleton->set(IMyInterface::class, MyInterfaceImplementer::class);


// To get an instance of MyClassWithDataMember with $member set to an instance
// of MyInterfaceImplementer use on of following: 

$instance = $skeleton->load(new MyClassWithDataMember(null));

// or

$instance = $skeleton->load(MyClassWithDataMember::class);

// or

$skeleton->set('myClass', MyClassWithDataMember::class);
$instance = $skeleton->get('myClass');
```

In the above code the **$member** property will be set to a new instance of **MyInterfaceImplementer** class, 
the method **setMember** will be called with a new instance of **MyInterfaceImplementer** and in the last two cases the **__constructor** will be called with a new instance of the **MyInterfaceImplementer** class.

To Enable autolaoding:

1. To enable the autoloading feature, the **enableKnot()** methods must be called on the skeleton instance.
2. For data members to be autoloaded, each data member must contain the **@autoload** or **@magic** annotation, and the **@var** annotation must be provided with full path to the target interface name (_@var /SomeNamespace/IMyInterface_ will not work).
3. For methods to be called on loading, the method name must start with **set**, the method parameters must be of the target inerfaces type and the **@autoload** or **@magic** annotation must be called on this method.
4. Constructor will be always autoloaded (unless knot was not enabled on the skeleton instance), and the annotation for them is not needed.
