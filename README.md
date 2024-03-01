# Table changelog

The table changelog library provides an abstract implementation of table row changes in application database.
It should be use to track columns changes in sql database and centralize the logs in project defined storage.


# Usage

The library provide simply APIs for registering and logging changes in your project. To register a driver that can
be used to log changes:

```php
<?php

// ...
use Drewlabs\Changelog\Logger;
// ...
// At the root of your application or when your application bootstraps

Logger::getInstance()->registerDriver(new MyLogger);

// or using a factory function
Logger::getInstance()->registerDriver(function() {
    return MyLogger
}, 'myLog');
```

**Note** `Logger` is a singleton instance that register loggers globally in your application, and should
be use with care if one does not want to share state.

After registering your logger in the global handler, you can log any table changes by calling the `logChange` method of the global logger:

```php

// In some part of the code where we need to log table column changes
Logger::getInstance()->logChange('table_name', 'model_key', 'property_name', 'old_value', 'current_value', 'logged_by');
```

**Note** Take a look at a driver implementation compatible with laravel eloquent library and `php-amqplib/php-amqplib` compatible implementation for message queues.