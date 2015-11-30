Installation
============

1. Install server
```sudo apt-get install rabbitmq-server```

2. Install amqp lib
```
sudo apt-get install librabbitmq-dev librabbitmq1
```

or

```
 git clone git://github.com/alanxz/rabbitmq-c.git
 cd rabbitmq-c
 # Enable and update the codegen git submodule
 git submodule init
 git submodule update
 # Configure, compile and install
 autoreconf -i && ./configure && make && sudo make install
```

3. Install and enable php amqp extension 
```pecl install amqp```

4. Require this vendor in your `composer.json`, make `composer update`
and add `EventBus` in `application.config.php` in modules.

Configuration
-----

Copy `src/EventBus/PortAdapter/ZF2/config/amqp.local.php.dist` from vendor to your 
`config/autoload` and rename it to `amqp.local.php`. Then change settings if necessary.

Usage
======

Will be there soon...