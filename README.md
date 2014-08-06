# serverpilot-php v1.0.0

This simple PHP API client binds to ServerPilot's RESTful [API](https://github.com/ServerPilot/API) that allows you to manage [ServerPilot](https://serverpilot.io) resources. All responses return JSON objects, including errors.

## Installation

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:
```json
	{
		"require": {
			"daverogers/serverpilot-php": "*"
		}
	}
```
...and then install
```
	composer.phar install
```
Or you can include manually:
```php
	include_once('/path/to/this/lib/ServerPilot.php');
```

## Usage

With your API `key` and `id` from ServerPilot, set up the config values and pass them to the ServerPilot class. You may alternatively include a `'decode' => false` config value if you just want the raw JSON-encoded value returned.

```php
	$config = array(
		'id' => 'cid_YOURID',
		'key' => 'YOURKEY');
	$sp = new ServerPilot($config);
```
From there, you can call any number of functions to manage your ServerPilot servers, apps, system users, databases, etc.

###Get lists:
```php
$servers    = $sp->server_list();
$users      = $sp->sysuser_list();
$apps       = $sp->app_list();
$databases  = $sp->database_list();
```

###Get info on a particular resource:
```php
$server     = $sp->server_info('SERVERID');
$user       = $sp->sysuser_info('SYSUSERID');
$app        = $sp->app_info('APPID');
$database   = $sp->database_info('DBID');
$action     = $sp->action_info('ACTIONID');
```

###Create a resource:
```php
$server     = $sp->server_create('SERVERNAME');
$user       = $sp->sysuser_create('SERVERID', 'SYSUSERNAME', 'PASSWORD');
$app        = $sp->app_create('APPNAME', 'SYSUSERID', 'RUNTIME', ['DOMAINS']) );
$database   = $sp->database_create('APPID', 'DBNAME', 'DBUSER', 'DBPASSWORD');
```

###Update a resource:
```php
$server     = $sp->server_update('SERVERID', (bool)FIREWALL, (bool)SYSUPDATE);
$user       = $sp->sysuser_update('SYSUSERID', 'PASSWORD');
$app        = $sp->app_update('APPID', 'RUNTIME', ['DOMAINS']);
$database   = $sp->database_update('DBID', 'DBUSERID', 'PASSWORD');
```

###Delete a resource:
```php
$server     = $sp->server_delete('SERVERID');
$user       = $sp->sysuser_delete('SYSUSERID');
$app        = $sp->app_delete('APPID');
$database   = $sp->database_delete('DBID');
```

###SSL functions (requires paid plan):
```php
$ssl = $sp->ssl_add('APPID', 'PRIVATEKEY', 'CERTIFICATE', 'CACERTS');
$ssl = $sp->ssl_delete('APPID');
```

##Notes

ServerPilot site: [https://serverpilot.io/](https://serverpilot.io)  
ServerPilot's API doc: [https://github.com/ServerPilot/API](https://github.com/ServerPilot/API)  
This project's Packagist link: [https://packagist.org/packages/serverpilot/serverpilot-php](https://packagist.org/packages/serverpilot/serverpilot-php)  
Getting started with Composer: [https://getcomposer.org/doc/00-intro.md](https://getcomposer.org/doc/00-intro.md)  
If this isn't your style, check out James West's PHP lib here: [https://github.com/jameswestnz/ServerPilot-API-PHP-Wrapper](https://github.com/jameswestnz/ServerPilot-API-PHP-Wrapper)