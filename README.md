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

Usage
======

Will be there soon...