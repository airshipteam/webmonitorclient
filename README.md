# webmonitorclient
Interface for web monitor

Include this package to implement logging to the central webmonitor server. Follow steps below to include in your project

### COMPOSER.JSON
First include this repo ( put in top level ):
```json
"repositories": [{
	    "type": "vcs",
        "url": "https://github.com/airshipWebservices/webmonitorclient"
    }
],	
```
Add the following value to your require attribute:
```json
"airshipWebservices/webmonitorclient" : "dev-master"
```
This package is implemented following PSR-4 autoloading standard. Add the following value to the autoload/psr-4 param:
```json
"airshipwebservices\\webmonitorclient\\" : "src"
```

### USAGE
To use in your class via dependancy injection, implement the use keyword to include the library:
```php
use airshipwebservices\webmonitorclient\Logger;
```

Then inject with typehinting - eg Passing to your constructor to use class wide:
```php
public function __construct( Logger $_Logger ){
  $this->_logger = $_Logger;
}
```



