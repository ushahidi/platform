---
description: >-
  In September 2018, we updated the Platform api, removed Kohana (mostly) and
  started using Lumen. This affects some of the setup and commands used in the
  api.
---

# Upgrading from V3.x.x to V4.x.x

## Migration guide

### PHP

{% hint style="info" %}
Please ensure that you are using a supported version of PHP for the version of platform that you are running.
{% endhint %}

* **v2** supports up to PHP 5.4
* **v3** supports PHP 5.6 and 7.0
* **v4.0.0** supports PHP 7.0 to 7.2
* **v4.1.0+** supports PHP 7.1 to 7.3 \(inclusive\). This change was made to ensure we support versions of PHP that are getting security fixes at the very least. See PHP maintainance schedules [here](https://www.php.net/supported-versions.php).

### Database config changes

The database configuration vars have been renamed.

| Old var | New var |
| :--- | :--- |
| `DB_NAME` | `DB_DATABASE` |
| `DB_USER` | `DB_USERNAME` |
| `DB_PASS` | `DB_PASSWORD` |
| `DB_TYPE=MySQLi` | `DB_CONNECTION=mysql` |

### New Configuration keys

| Var | Comments |
| :--- | :--- |
| `CACHE_DRIVER` | Supported options are `array`, `redis` and `memcached`. Read more about Lumen's cache configuration and options here [https://lumen.laravel.com/docs/5.4/cache](https://lumen.laravel.com/docs/5.4/cache) |

### Artisan

`bin/ushahidi` will be deprecated in future versions. You should use `artisan` instead.

### Command name changes

CLI commands have been renamed. If you had cronjobs set up to run dataproviders, etc you will need to update those

| Old command | New command |
| :--- | :--- |
| `bin/ushahidi dataprovider incoming` | `artisan datasource:incoming` |
| `bin/ushahidi savedsearch` | `artisan savedsearch:sync` |
| `bin/ushahidi notification queue` | `artisan notification:queue` |
| `bin/ushahidi dataprovider outgoing` | `artisan datasource:outgoing` |
| `bin/ushahidi dataprovider webhook send` | `artisan webhook:send` |
| `bin/ushahidi user create` | `artisan user:create` |
| `bin/ushahidi user delete` | `artisan user:delete` |
| `bin/ushahidi config get` | `artisan config:get` |
| `bin/ushahidi config set` | `artisan config:set` |
| `bin/ushahidi export` | `artisan export` |
| `bin/ushahidi import` | `artisan import` |

### Filesystem changes

Uploaded files have moved from `application/media/upload` to `storage/app`. You should move any existing files to the new location.

If you used a CDN for file storage, you should configure the `FILESYSTEM_DRIVER` variable in your `.env` file. Then review `config/filesystems.php` to find the other config parameters, the old `CDN_`... params have be renamed.

### Old configuration files

Old configuration files in `application/config` are now obsolete. These are not either located in `config/`, or configured through environment vars \(ie. `.env`\)

### Platform Client

To use this version of the platform API, you will need to update your version of the platform-client to use the same release version.

