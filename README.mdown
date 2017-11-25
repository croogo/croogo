# Croogo

**Croogo** is a free, open source, content management system for PHP, released under [MIT License](http://github.com/croogo/croogo/blob/master/LICENSE.txt).

It is powered by [CakePHP](http://cakephp.org) MVC framework.

[![Build Status](https://secure.travis-ci.org/croogo/croogo.png)](http://travis-ci.org/croogo/croogo)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/croogo/croogo/badges/quality-score.png?s=af82d83fa37e1817f3ff21ad4bf2a320b49b2124)](https://scrutinizer-ci.com/g/croogo/croogo/)
[![Code Coverage](https://scrutinizer-ci.com/g/croogo/croogo/badges/coverage.png?s=261fc6b969dc3e45e05f2f6d1b4728da6d434c2a)](https://scrutinizer-ci.com/g/croogo/croogo/)

## Requirements
  * Apache with `mod_rewrite`
  * PHP 5.5 or higher
  * MySQL 5.7 or higher

## Installation

#### Installation using composer

The preferred way to install Croogo is by using [composer](http://getcomposer.org).

    composer create-project croogo/app myapp
    cd myapp
    composer install

If you want hack on croogo, you can use the `--prefer-source` flag, ie:

    `composer create-project --prefer-source croogo/app myapp`

#### Web based installer

  * Extract the archive. Upload the content to your server.
  * Create a new MySQL database (`utf8_unicode_ci` collation)
  * Visit http://your-site.com/ from your browser and follow the instructions.

#### Manual installation

  * Extract the archive. Upload the content to your server.
  * Locate your `config` directory, and rename the following files:
    * `config/app.default.php ` to `config/app.php`, and edit the details.
  * You can access your admin panel at http://your-site.com/admin. The installer
    should display a page for you to create the administrative user.

## Links

  * **Official website**: [http://croogo.org](http://croogo.org)
  * **Blog**: [http://blog.croogo.org](http://blog.croogo.org)
  * **Downloads**: [http://downloads.croogo.org](http://downloads.croogo.org)
  * **Issue Tracker**: [http://github.com/croogo/croogo/issues](http://github.com/croogo/croogo/issues)
  * **Documentation**: [http://docs.croogo.org](http://docs.croogo.org)
  * **Google Group**: [http://groups.google.com/group/croogo](http://groups.google.com/group/croogo)
  * **IRC Channel**: [#croogo](irc://irc.freenode.net/croogo) on irc.freenode.net

  * **Gitter**: [![Join the chat at https://gitter.im/croogo/croogo](https://badges.gitter.im/croogo/croogo.svg)](https://gitter.im/croogo/croogo?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
