serverpilot-php v1.0
====================

This PHP API client binds to ServerPilot's RESTful [API](https://github.com/ServerPilot/API) that allows you to manage [ServerPilot](https://serverpilot.io) resources. All responses return JSON objects, including errors.

Installation
------------

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:

	{
		"require": {
			"serverpilot/serverpilot-php": "*"
		}
	}

...and then install

	composer.phar install

Or you can include manually:

	include_once('/path/to/this/lib/ServerPilot.php');

Usage
-----

With your API `key` and `id` from ServerPilot, set up the config values and pass them to the ServerPilot class. You may alternatively include a `'decode' => false` config value if you just want the raw JSON-encoded value returned.

	$config = array(
		'id' => 'cid_YOURID',
		'key' => 'YOURKEY');
	$sp = new ServerPilot($config);

From there, you can call any number of functions to manage your ServerPilot servers, apps, system users, databases, etc.

Get lists:

	$sp->server_list();
	$sp->sysuser_list();
	$sp->app_list();
	$sp->database_list();

Get info on a particular resource:

	$sp->server_info('SERVERID');
	$sp->sysuser_info('SYSUSERID');
	$sp->app_info('APPID');
	$sp->database_info('DBID');
	$sp->action_info('ACTIONID');

Create a resource:

	$sp->server_create('SERVERNAME');
	$sp->sysuser_create('SERVERID', 'SYSUSERNAME', 'PASSWORD');
	$sp->app_create('APPNAME', 'SYSUSERID', 'RUNTIME', ['DOMAINS']) );
	$sp->database_create('APPID', 'DBNAME', 'DBUSER', 'DBPASSWORD');

Update a resource:

	$sp->server_update('SERVERID', (bool)FIREWALL, (bool)SYSUPDATE);
	$sp->sysuser_update('SYSUSERID', 'PASSWORD');
	$sp->app_update('APPID', 'RUNTIME', ['DOMAINS']);
	$sp->database_update('DBID', 'DBUSERID', 'PASSWORD');

Delete a resource:

	$sp->server_delete('SERVERID');
	$sp->sysuser_delete('SYSUSERID');
	$sp->app_delete('APPID');
	$sp->database_delete('DBID');

SSL functions (requires paid plan):

	$sp->ssl_add('APPID', 'PRIVATEKEY', 'CERTIFICATE', 'CACERTS');
	$sp->ssl_delete('APPID');