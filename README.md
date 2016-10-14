# serverpilot-php
[![Latest Stable Version](https://poser.pugx.org/daverogers/serverpilot-php/v/stable.svg)](https://packagist.org/packages/daverogers/serverpilot-php) [![Total Downloads](https://poser.pugx.org/daverogers/serverpilot-php/downloads.svg)](https://packagist.org/packages/daverogers/serverpilot-php) [![License](https://poser.pugx.org/daverogers/serverpilot-php/license.svg)](https://packagist.org/packages/daverogers/serverpilot-php)


This simple PHP API client binds to ServerPilot's RESTful [API](https://github.com/ServerPilot/API) that allows you to manage [ServerPilot](https://serverpilot.io) resources. All responses return JSON objects, including errors.

## Installation

You can install the bindings via [Composer](http://getcomposer.org/). Add this to your `composer.json`:
```json
{
	"require": {
		"daverogers/serverpilot-php": "1.*"
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
**General**

 * [Connect](#connect)
 * [Catch errors](#catch-errors)
 * [Actions](#actions)
 
**Servers**

  * [List all servers](#list-all-servers)
  * [Retrieve an existing server](#retrieve-an-existing-server)
  * [Connect a new server](#connect-a-new-server)
  * [Update an existing server](#update-an-existing-server)
  * [Remove an existing server](#remove-an-existing-server)
 
**System users**

  * [List all system users](#list-all-system-users)
  * [Retrieve an existing system user](#retrieve-an-existing-system-user)
  * [Create a new system user](#create-a-new-system-user)
  * [Update an existing system user](#update-an-existing-system-user)
  * [Remove an existing system user](#remove-an-existing-system-user)
 
**Apps**

  * [List all apps](#list-all-apps)
  * [Retrieve an existing app](#retrieve-an-existing-app)
  * [Create a new app](#create-a-new-app)
  * [Update an existing app](#update-an-existing-app)
  * [Remove an existing app](#remove-an-existing-app)
 
**Databases**

  * [List all databases](#list-all-databases)
  * [Retrieve an existing database](#retrieve-an-existing-database)
  * [Create a new database](#create-a-new-database)
  * [Update an existing database](#update-an-existing-database)
  * [Remove an existing database](#remove-an-existing-database)
 
**SSL**

  * [Add custom SSL to an app](#add-custom-ssl-to-an-app)
  * [Remove custom SSL from an app](#remove-custom-ssl-from-an-app)
  * [Enable AutoSSL for an app](#enable-autossl-for-an-app)

### Connect

With your API `key` and `id` from ServerPilot, set up the config values and pass them to the ServerPilot class. You may alternatively include a `'decode' => false` config value if you just want the raw JSON-encoded value returned.

```php
	$config = array(
		'id' => 'YOURID',
		'key' => 'YOURKEY'
	);
	$sp = new ServerPilot($config);
```
From there, you can call any number of functions to manage your ServerPilot servers, apps, system users, databases, etc.

### Catch errors
If there's a  problem with any request a `ServerPilotException` is thrown.

You can retrieve the error message with `getMessage()` and the actual HTTP code with `getCode()`.

```php
try {
    $servers    = $sp->server_list();
} catch(ServerPilotException $e) {
    echo $e->getCode() . ': ' .$e->getMessage();
}
```

### Actions
Actions are a record of work done on ServerPilot resources. These can be things like the creation of an App, deploying SSL, deleting an old Database, etc.

All methods that modify a resource will return an `actionid` which can be used to track the status of said action.

**Possible values of an action status**

|Status|Description                                   |
|-------|---------------------------------------------|
|`open`   |Action has not completed yet.              |
|`success`|Action was completed successfully.         |
|`error`  |Action has completed but there were errors.|


```php
$sp->action_info('ACTIONID');
```

```json
{
  "data":
  {
    "id": "g3kiiYzxPgAjbwcY",
    "serverid": "4zGDDO2xg30yEeum",
    "status": "success",
    "datecreated": 1403138066
  }
}
```

### Servers

#### List all servers

```php
$servers    = $sp->server_list();
```

```json
{
  "data": [
  {
    "id": "FqHWrrcUfRI18F0l",
    "name": "www1",
    "autoupdates": true,
    "firewall": true,
    "lastaddress": "1.2.3.4",
    "lastconn": 1403130552,
    "datecreated": 1403130551
  }, {
    "id": "4zGDDO2xg30yEeum",
    "name": "vagrant",
    "autoupdates": true,
    "firewall": true,
    "lastaddress": "1.2.3.4",
    "lastconn": 1403130554,
    "datecreated": 1403130553
  }]
}
```


#### Retrieve an existing server

```php
$server    = $sp->server_info('SERVERID');
```

```json
{
  "data": 
 {
     "id": "UXOSIYrdtL4cSGp3",
     "name": "www2",
     "autoupdates": true,
     "firewall": true,
     "lastaddress": "1.2.3.4",
     "lastconn": 1403130554,
     "datecreated": 1403130553
   }
}
```

#### Connect a new server

Use this method to tell ServerPilot that you plan to connect a new server.

```php
$server    = $sp->server_create('SERVERNAME');
```

When the request goes through successfully you should get this returned: 

```json
{
  "actionid": "tW2fu4hjHnsix6Rn",
    "data":
    {
      "id": "`UXOSIYrdtL4cSGp3`",
      "name": "www2",
      "autoupdates": true,
      "firewall": true,
      "lastaddress": null,
      "lastconn": null,
      "datecreated": 1403130553,
      "apikey": "nqXUevYSkpW09YKy7CY7PdnL14Q1HIlAfniJZwzjqNQ"
    }
}
```

With `data.id` and `data.apikey` you can run the serverpilot installer on the server you just registered.

```
$ export SERVERID=UXOSIYrdtL4cSGp3
$ export SERVERAPIKEY=nqXUevYSkpW09YKy7CY7PdnL14Q1HIlAfniJZwzjqNQ
$ sudo apt-get update && sudo apt-get -y install wget ca-certificates && \
  sudo wget -nv -O serverpilot-installer https://download.serverpilot.io/serverpilot-installer && \
  sudo sh serverpilot-installer \
    --server-id=$SERVERID \
    --server-apikey=$SERVERAPIKEY
``` 

#### Update an existing server
There are 2 options you can change on each server; firewall and auto updates.

Both of these options are `booleans` (if you don't want to change an option you can define it as `null`. 

```php
$response    = $sp->server_update('SERVERID', 'FIREWALL':bool, 'AUTOUPDATES':bool);
```

```json
{
  "data": 
 {
     "id": "UXOSIYrdtL4cSGp3",
     "name": "www2",
     "autoupdates": true,
     "firewall": true,
     "lastaddress": "1.2.3.4",
     "lastconn": 1403130554,
     "datecreated": 1403130553
   }
}
```

#### Remove an existing server

```php
$response    = $sp->server_delete('SERVERID');
```

```json
{
  "data": {}
}
```


### System users

#### List all system users

```php
$systemUsers    = $sp->sysuser_list();
```

```json
{
  "data": 
  [
    {
      "id": "PdmHhsb3fnaZ2r5f",
      "name": "serverpilot",
      "serverid": "FqHWrrcUfRI18F0l"
    },
    {
      "id": "RvnwAIfuENyjUVnl",
      "name": "serverpilot",
      "serverid": "4zGDDO2xg30yEeum"
    }]
}
```


#### Retrieve an existing system user

```php
$systemUser    = $sp->sysuser_info('SERVERID');
```

```json
{
  "data":
  {
      "id": "PPkfc1NECzvwiEBI",
      "name": "derek",
      "serverid": "FqHWrrcUfRI18F0l"
    }
}
```

#### Create a new system user

**Parameters**

| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `serverid`      | `string`       | **Required**. The id of the Server.
| `name` | `string`       | **Required**.  The name of the System User. Length must be between 3 and 32 characters. Characters can be of lowercase ascii letters, digits, or a dash ('abcdefghijklmnopqrstuvwxyz0123456789-'), but must start with a lowercase ascii letter. `user-32` is a valid name, while `3po` is not.
| `password`   | `string`       | The password of the System User. If user has no password, they will not be able to log in with a password. No leading or trailing whitespace is allowed and the password must be at least 8 and no more than 200 characters long.


```php
$systemUser    = $sp->sysuser_create('SERVERID', 'NAME', 'PASSWORD');
```

When the request goes through successfully you should get this returned: 

```json
{
  "actionid": "nnpgQoNzSK11fuTe",
    "data":
    {
      "id": "PPkfc1NECzvwiEBI",
      "name": "derek",
      "serverid": "FqHWrrcUfRI18F0l"
    }
}
```

#### Update an existing system user

**Parameters**

| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `serverid`      | `string`       | **Required**. The id of the Server.
| `password`   | `string`       | The password of the System User. If user has no password, they will not be able to log in with a password. No leading or trailing whitespace is allowed and the password must be at least 8 and no more than 200 characters long.

Every parameter except for app id is optional (meaning that by providing `null` nothing will be changed). 

```php
$response    = $sp->sysuser_update('SERVERID', 'PASSWORD');
```

```json
{
  "actionid": "OF42xCWkKcaX3qG2",
    "data":
    {
      "id": "RvnwAIfuENyjUVnl",
      "name": "serverpilot",
      "serverid": "4zGDDO2xg30yEeum"
    }
}
```

#### Remove an existing system user

```php
$response    = $sp->sysuser_delete('SYSUSERID');
```

```json
{
  "actionid": "9tvygrrXZulYuizz",
  "data": {}
}
```


### Apps

#### List all apps

```php
$apps    = $sp->app_list();
```

```json
{
  "data": [
            {
              "id": "c77JD4gZooGjrF8K",
              "datecreated": 1403139066,
              "name": "blog",
              "sysuserid": "RvnwAIfuENyjUVnl",
              "domains": ["www.myblog.com", "blog.com"],
              "ssl": null,
              "serverid": "4zGDDO2xg30yEeum",
              "runtime": "php7.0"
            },
            {
              "id": "B1w7yc1tfUPQLIKS",
              "datecreated": 1403143012,
              "name": "store",
              "sysuserid": "RvnwAIfuENyjUVnl",
              "domains": ["www.mystore.com", "mystore.com"],
              "ssl": {
                "key": "-----BEGIN PRIVATE KEY----- ...",
                "cert": "-----BEGIN CERTIFICATE----- ...",
                "cacerts": "-----BEGIN CERTIFICATE----- ...",
                "auto": false,
                "force": false
              },
              "serverid": "4zGDDO2xg30yEeum",
              "runtime": "php7.0"
            }]
}
```


#### Retrieve an existing app

```php
$app    = $sp->app_info('APPID');
```

```json
{
  "data":
    {
      "id": "UXOSIYrdtL4cSGp3",
      "name": "www2",
      "autoupdates": true,
      "firewall": true,
      "lastaddress": "1.2.3.4",
      "lastconn": 1403130554,
      "datecreated": 1403130553
    }
}
```

#### Create a new app

**Parameters**


| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `name`      | `string`       | **Required**. The nickname of the App. Length must be between 3 and 30 characters. Characters can be of lowercase ascii letters and digits.
| `sysuserid` | `string`       | **Required**. The System User that will "own" this App. Since every System User is specific to a Server, this implicitly determines on which Server the App will be created.
| `runtime`   | `string`       | **Required**. The PHP runtime for an App. Choose from `php5.4`, `php5.5`, `php5.6`, `php7.0`, or `php7.1`.
| `domains`   | `array`        | An array of domains that will be used in the webserver's configuration. If you set your app's domain name to *example.com*, Nginx and Apache will be configured to listen for both *example.com* and *www.example.com*. **Note**: The complete list of domains must be included in every update to this field.
| `wordpress`   | `array`        | An array containing the following keys: ` site_title` , ` admin_user` , ` admin_password` , and ` admin_email` 


```php
$app    = $sp->app_create('APPNAME', 'SYSUSERID', 'RUNTIME', 'DOMAINS', 'WORDPRESS');
```

When the request goes through successfully you should get this returned: 

```json
{
  "actionid": "dIrCNoWunW92lPjw",
    "data":
    {
      "id": "nlcN0TwdZAyNEgdp",
      "datecreated": 1403143012,
      "name": "gallery",
      "sysuserid": "RvnwAIfuENyjUVnl",
      "domains": ["www.example.com", "example.com"],
      "ssl": null,
      "serverid": "4zGDDO2xg30yEeum",
      "runtime": "php7.0"
    }
}
```

#### Update an existing app

**Parameters**

| Name      | Type           | Description
| --------- | :------------: | :---------------------------------------
| `runtime` | `string`       | The PHP runtime for an App. Choose from `php5.4`, `php5.5`, `php5.6`, `php7.0`, or `php7.1`.
| `domains` | `array`        | An array of domains that will be used in the webserver's configuration. If you set your app's domain name to *example.com*, Nginx and Apache will be configured to listen for both *example.com* and *www.example.com*. **Note**: The complete list of domains must be included in every update to this field.

Every parameter except for app id is optional (meaning that by providing `null` nothing will be changed). 

```php
$response    = $sp->app_update('APPID', 'RUNTIME', 'DOMAINS');
```

```json
{
  "actionid": "KlsNzLikw3BRvShc",
  "data":
  {
    "id": "nlcN0TwdZAyNEgdp",
    "datecreated": 1403143012,
    "name": "gallery",
    "sysuserid": "RvnwAIfuENyjUVnl",
    "domains": ["www.example.com", "example.com"],
    "ssl": null,
    "serverid": "4zGDDO2xg30yEeum",
    "runtime": "php5.6"
  }
}
```

#### Remove an existing app

```php
$response    = $sp->app_delete('APPID');
```

```json
{
  "actionid": "88Ypexhx28Y63eyA",
  "data": {}
}
```



### Databases

#### List all databases

```php
$databases    = $sp->database_list();
```

```json
{
  "data":
  [
    {
      "id": "hdXkAZchuj27Hm1L",
      "name": "wordpress",
      "appid": "c77JD4gZooGjrF8K",
      "serverid": "4zGDDO2xg30yEeum",
      "user": {
        "id": "vt08Qz9kjOC3RVLr",
        "name": "robert"
      }
    }
  ]
}
```


#### Retrieve an existing database

```php
$app    = $sp->database_info('DBID');
```

```json
{
  "data":
  {
      "id": "8PV1OIAlAW3jbGmM",
      "name": "gallerydb",
      "appid": "nlcN0TwdZAyNEgdp",
      "serverid": "4zGDDO2xg30yEeum",
      "user": {
        "id": "k2HWtU33mpUsfOdA",
        "name": "arturo"
      }
    }
}
```

#### Create a new database

**Parameters**


| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `appid` | `string`           | **Required**. The id of the App.
| `name`      | `string`       | **Required**. The name of the database. Length must be between 3 and 64 characters. Characters can be of lowercase ascii letters, digits, or a dash ('abcdefghijklmnopqrstuvwxyz0123456789-').
| `username`   | `string`       | **Required**. The name of the Database User. Length must be at most 16 characters.
| `password`   | `string`        | **Required**. The password of the Database User. No leading or trailing whitespace is allowed and the password must be at least 8 and no more than 200 characters long.


```php
$app    = $sp->database_create('APPID', 'NAME', 'USERNAME', 'PASSWORD');
```

When the request goes through successfully you should get this returned: 

```json
{
  "actionid": "gPFiWP9hFNUxvT70",
    "data":
    {
      "id": "8PV1OIAlAW3jbGmM",
      "name": "gallerydb",
      "appid": "nlcN0TwdZAyNEgdp",
      "serverid": "4zGDDO2xg30yEeum",
      "user": {
        "id": "k2HWtU33mpUsfOdA",
        "name": "arturo"
      }
    }
}
```

#### Update an existing database

**Parameters**

| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `appid` | `string`           | **Required**. The id of the App.
| `userid`      | `string`       | **Required**. The id of the Database User.
| `password`   | `string`        | **Required**. The *new* password of the Database User. No leading or trailing whitespace is allowed and the password must be at least 8 and no more than 200 characters long.

Every parameter except for app id is optional (meaning that by providing `null` nothing will be changed). 

```php
$response    = $sp->database_update('DBID', 'USERID', 'PASSWORD');
```

```json
{
  "actionid": "VfH12ukDJFv0RZAO",
    "data":
    {
      "id": "8PV1OIAlAW3jbGmM",
      "name": "gallerydb",
      "appid": "nlcN0TwdZAyNEgdp",
      "serverid": "4zGDDO2xg30yEeum",
      "user": {
        "id": "k2HWtU33mpUsfOdA",
        "name": "arturo"
      }
    }
}
```

#### Remove an existing database

```php
$response    = $sp->database_delete('APPID');
```

```json
{
  "actionid": "88Ypexhx28Y63eyA",
  "data": {}
}
```

### SSL

#### Add custom SSL to an app


**Parameters**

| Name        | Type           | Description
| ----------- | :------------: | :---------------------------------------
| `appid` | `string`           | **Required**. The id of the App.
| `key`      | `string`       | **Required**. The contents of the private key.
| `cert`   | `string`        | **Required**. The contents of the certificate.
| `cacerts`   | `string`        | The contents of the CA certificate(s). If none, null is acceptable.


```php
$ssl    = $sp->ssl_add('APPID', 'KEY', 'CERT', 'CACERTS);
```

```json
{
  "actionid": "BzcMNZ9sdBY62vTd",
    "data":
    {
      "key": "-----BEGIN PRIVATE KEY----- ... -----END PRIVATE KEY-----",
      "cert": "-----BEGIN CERTIFICATE----- ... -----END CERTIFICATE-----",
      "cacerts": "-----BEGIN CERTIFICATE----- ... -----END CERTIFICATE-----"
    }
}
```

#### Remove custom SSL from an app

```php
$ssl    = $sp->ssl_delete('APPID');
```

#### Enable AutoSSL for an app

AutoSSL can only be enabled when an AutoSSL certificate is available for an app.

Additionally, AutoSSL cannot be enabled when an app currently has a custom SSL certificate. To enable AutoSSL when an app is already using a custom SSL, first delete the app's custom SSL certificate.

**Note** that disabling AutoSSL is not done through this API call but instead is done by deleting SSL from the app.

```php
$ssl    = $sp->ssl_auto('APPID');
```

##Notes

ServerPilot site: [https://serverpilot.io/](https://serverpilot.io)

ServerPilot's API doc: [https://github.com/ServerPilot/API](https://github.com/ServerPilot/API)

This project's Packagist link: [https://packagist.org/packages/daverogers/serverpilot-php](https://packagist.org/packages/daverogers/serverpilot-php)

Getting started with Composer: [https://getcomposer.org/doc/00-intro.md](https://getcomposer.org/doc/00-intro.md)

If this isn't your style, check out James West's PHP lib here: [https://github.com/jameswestnz/ServerPilot-API-PHP-Wrapper](https://github.com/jameswestnz/ServerPilot-API-PHP-Wrapper)
