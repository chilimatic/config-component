# config-component
chilimatic config-component


The File Object is built for multi-domain architectures:

The idea is pretty simple you create a config directory, 
every config file has the extension ```.cfg``` the object will created a list based
 on the given host_id of the server. 
if no host_id is set as parameter it will try to determinate the id via ```$_SERVER ['HTTP_HOST']```
or it will try to look for the ```host``` parameter use from the cli

```bash 
php myscript.php host=www.example.com
``` 

if still no host_id is found it will throw an exception.

```php
$config = new File(
    [
        \chilimatic\lib\Config\File::CONFIG_PATH_INDEX  => __DIR__ . '/test/data',
        \chilimatic\lib\Config\File::HOST_ID_KEY        => 'www.example.com' 
    ]
);
```

It will than generate a set of possible configs based on the delimiter, 
the delimiter is ```*``` by default you can switch it to ```all``` for windows machines.

the generated set in our case would look as follows:

```php
[ 
    '*.cfg', 
    '*.example.com.cfg', 
    'www.example.com.cfg'
]
```
as mentioned you can change the delimiter for windows machines to ```all```
```php
[ 
    'all.cfg', 
    'all.example.com.cfg', 
    'www.example.com.cfg'
]
```
the *.cfg will be used on every system, and than it will be overwritten hierarchical

The values are stored in a doublylinked-list so all values do remain but in 
general the config returns the last appended value to a specific key

```php
$config = new File(
    [
        \chilimatic\lib\Config\File::CONFIG_PATH_INDEX  => __DIR__ . '/test/data',
        \chilimatic\lib\Config\File::HOST_ID_KEY        => 'www.example.com' 
    ]
);
$config->set('key1', ['some', 'data']);
$config->set('key1', implode(' ', $config->get('key1')));

echo $config->get('key1'); // will return 'some data'
```
the config implements its own parser which allows you to write configs like
```
#cache settings
cache_type = 'memcached'
cache_settings = { "server_list" : [{"host" : "127.0.0.1", "port" : "11211", "weight" : 1 }] }
// it is possible to read serialized data but as we all know this can lead to chaos :) 
// so stick to json if possible
serializedString = a:2:{s:2:"my";s:3:"key";s:5:"value";O:8:"stdClass":0:{}}
```
comments are defined as ```// or #``` and will be added to the node;


The library includes a standard factory pattern that can be accessed static

```php
\chilimatic\lib\Config\ConfigFactory::make(
    'file',
     [
             \chilimatic\lib\Config\File::CONFIG_PATH_INDEX  => __DIR__ . '/test/data',
             \chilimatic\lib\Config\File::HOST_ID_KEY        => 'www.example.com' 
     ]
 ); // returns the same as the example above
```

As well as a singelton config wrapper which utilizes the factory to create a config that 
allows you static access.

```php
// passed reference
$config = \chilimatic\lib\Config\Config::getInstance(
    'file',
     [
             \chilimatic\lib\Config\File::CONFIG_PATH_INDEX  => __DIR__ . '/test/data',
             \chilimatic\lib\Config\File::HOST_ID_KEY        => 'www.example.com' 
     ]
 );
// also accessable via
Config::set('my variable', 'my value);
Config::get('my variable');
```

you can have a look at the benchmark.php and the unit tests. 


if you just wanna uses ini files you can use the ini adapter
```php
$config = new Ini(
    [
         Ini::FILE_INDEX => __DIR__ . '/test/data/'
    ]
);
```
if you just pass a directory it will scan for all files with an ```.ini``` extension
It will merge them recursive based on the scanned order. 
and than pass it to the node graph structure inside of the config object

if you pass a specific file it will only use this file.

Planned for the next steps are new engines right now it's a dom like structure with indexes
this can be overkill and maybe other people prefer a flat hashmap structure.  
Maybe a json adapter will be cool too.