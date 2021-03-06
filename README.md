<p align="center">
    <sub><img src="https://github.githubassets.com/images/icons/emoji/unicode/26a0.png" width="22"></sub> <strong>This package is abandoned and no longer maintained</strong> <sub><img src="https://github.githubassets.com/images/icons/emoji/unicode/26a0.png" width="22"></sub><br />
    We suggests using the original <a href="https://github.com/dg/bypass-finals">dg/bypass-finals</a> package instead<br />
    <em>since v1.2.0 it includes all changes from this fork</em>
</p>

Bypass Finals
=============

[![Version][badge_packagist_version]][link_packagist]
[![Version][badge_php_version]][link_packagist]
[![Build Status][badge_build_status]][link_build_status]
[![Downloads count][badge_downloads_count]][link_packagist]
[![License][badge_license]][link_license]

Introduction
------------

Removes final keywords from source code on-the-fly and allows mocking of final methods and classes.
It can be used together with any test tool such as PHPUnit, Mockery or [Nette Tester](https://tester.nette.org).


Installation
------------

The recommended way to install is through Composer:

```bash
composer require --dev avto-dev/bypass-finals "^1.2"
```

> Installed `composer` is required ([how to install composer][getcomposer]).

> You need to fix the major version of package.

It requires PHP version 5.6 and supports PHP up to 7.3.


Usage
-----

Simply call this:

```php
DG\BypassFinals::enable();
```

You need to enable it before the classes you want to remove the final are loaded. So call it as soon as possible, preferably right after `vendor/autoload.php` in loaded.

You can choose to only bypass finals in specific files:

```php
DG\BypassFinals::setWhitelist([
    'relative/path/to/file.php'
]);

DG\BypassFinals::enable();
```

This gives you finer control and can solve issues with certain frameworks and libraries.

If you like it, **[please make a donation now](https://nette.org/make-donation?to=bypass-finals)**. Thank you!

## Changes log

[![Release date][badge_release_date]][link_releases]
[![Commits since latest release][badge_commits_since_release]][link_commits]

Changes log can be [found here][link_changes_log].

## Support

[![Issues][badge_issues]][link_issues]
[![Issues][badge_pulls]][link_pulls]

If you will find any package errors, please, [make an issue][link_create_issue] in current repository.

[badge_packagist_version]:https://img.shields.io/packagist/v/avto-dev/bypass-finals.svg?maxAge=180
[badge_php_version]:https://img.shields.io/packagist/php-v/avto-dev/bypass-finals.svg?longCache=true
[badge_build_status]:https://travis-ci.org/avto-dev/bypass-finals.svg?branch=master
[badge_downloads_count]:https://img.shields.io/packagist/dt/avto-dev/bypass-finals.svg?maxAge=180
[badge_license]:https://img.shields.io/packagist/l/avto-dev/bypass-finals.svg?longCache=true
[badge_release_date]:https://img.shields.io/github/release-date/avto-dev/bypass-finals.svg?style=flat-square&maxAge=180
[badge_commits_since_release]:https://img.shields.io/github/commits-since/avto-dev/bypass-finals/latest.svg?style=flat-square&maxAge=180
[badge_issues]:https://img.shields.io/github/issues/avto-dev/bypass-finals.svg?style=flat-square&maxAge=180
[badge_pulls]:https://img.shields.io/github/issues-pr/avto-dev/bypass-finals.svg?style=flat-square&maxAge=180
[link_releases]:https://github.com/avto-dev/bypass-finals/releases
[link_packagist]:https://packagist.org/packages/avto-dev/bypass-finals
[link_build_status]:https://travis-ci.org/avto-dev/bypass-finals
[link_changes_log]:https://github.com/avto-dev/bypass-finals/blob/master/CHANGELOG.md
[link_issues]:https://github.com/avto-dev/bypass-finals/issues
[link_create_issue]:https://github.com/avto-dev/bypass-finals/issues/new/choose
[link_commits]:https://github.com/avto-dev/bypass-finals/commits
[link_pulls]:https://github.com/avto-dev/bypass-finals/pulls
[link_license]:https://github.com/avto-dev/bypass-finals/blob/master/LICENSE
[getcomposer]:https://getcomposer.org/download/
