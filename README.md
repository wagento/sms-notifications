# Wagento SMS Notifications powered by LINK Mobility

Wagento SMS Notifications integrates Magento 2 with the LINK Mobility
mobile messaging service to send transactional text notifications. Customers can
be notified when their order is successfully placed as well as when it is
invoiced, shipped, canceled, refunded, held or released.

Detailed instructions for installation, configuration and usage can be found in
the [User Guide].

## Requirements

* PHP 7.1.3+ or 7.2.0+
* Magento Open Source/Commerce 2.2.7+ or 2.3.0+
* A [LINK Mobility] account

## Installation

### Composer (recommended)

We highly recommend purchasing the extension from the [Magento Marketplace],
where you can receive the latest version for free. Once purchased, you can use
the following commands to install it from a terminal or command prompt:

    $ cd /path/to/your/site
    $ composer require wagento/module-sms-notifications

**Note:** The Marketplace version is compatible with both Magento 2.3 and 2.2.
For 2.2, PHP 7.1 is required, while 7.2 is recommended for 2.3.

### Manual

This extension can be downloaded from [GitHub] and installed into the
`app/code` directory of your Magento installation with these commands:

    $ cd /path/to/your/site/app/code
    $ mkdir Wagento
    $ cd Wagento
    $ git clone git@github.com:wagento/sms-notifications.git SMSNotifications

For Magento 2.2, you will need to append this line to `registration.php`:

    require 'compat.inc';

**Warning**: `compat.inc` contains aliases for interfaces and classes that exist
in 2.3 but are not available in 2.2. If any other installed extensions require
these interfaces and/or classes and provide their own work-arounds, you may
experience compatibility issues and/or degraded site performance. If this
occurs, please [open a support ticket][Support] and let us know the vendor and
extension name so that we can work with them on a solution.

### Post-Install

After installing the extension for the first time, please run these commands to
enable it:

    $ cd /path/to/your/site
    $ php bin/magento module:enable Wagento_SMSNotifications

Once you have enabled the extension, please follow the instructions in the
[Post-Install, Post-Update or Post-Uninstall][post]
section to complete the installation process.

## Updating

### Composer

If you've installed the extension from the Magento Marketplace using Composer,
run these commands from your terminal or command prompt to update it:

    $ cd /path/to/your/site
    $ composer update wagento/module-sms-notifications

### Manual

If you've installed the extension from GitHub manually, run these commands from
your terminal or command prompt to update it:

    $ cd /path/to/your/site/app/code/Wagento/SMSNotifications
    $ git pull

## Uninstalling

### Composer

If you've installed the extension from the Magento Marketplace using Composer,
run these commands from your terminal or command prompt to remove its data and
package:

    $ cd /path/to/your/site
    $ php bin/magento module:uninstall -r Wagento_SMSNotifications

### Manual

If you've installed the extension manually, run these commands from your
terminal or command prompt to remove its data:

    $ cd /path/to/your/site/app/code
    $ rm -rf Wagento/SMSNotifications
    $ mysql -u your_user -p your_database <<'SQL'
    DROP TABLE `directory_telephone_prefix`;
    DROP TABLE `sms_notification_subscription`;
    DELETE FROM `eav_attribute` WHERE `attribute_code` LIKE 'sms_mobile%';
    DELETE FROM `core_config_data` WHERE `path` LIKE 'sms_notifications/%';
    DELETE FROM `setup_module` WHERE `module` = 'Wagento_SMSNotifications';
    SQL

## Post-Install, Post-Update or Post-Uninstall

To complete the installation, update or uninstall process, please run these
commands:

    $ cd /path/to/your/site
    $ php bin/magento setup:upgrade
    $ php bin/magento setup:di:compile
    $ php bin/magento setup:static-content:deploy

## Configuration

The settings can configured in the Admin panel at
`Stores > Settings > Configuration > General > SMS Notifications`. For detailed
descriptions of the available options, please refer to the [User Guide].

## Support

If you experience any issues or errors while using the extension, please open a
ticket by sending an e-mail to [support@wagento.com][Support]. Be sure to
include your domain, PHP version, Magento version, a detailed description of the
problem including steps to reproduce it and any other relevant information. We
do our best to respond to all legitimate inquires within 48 business hours.

## License

The source code contained in this extension is licensed under [version 3.0 of
the Open Software License (OSL-3.0)][OSL]. A full copy of the license can be
found in the [LICENSE.txt] file.

## History

A full history of the extension can be found in the [CHANGELOG.md] file.

## Contributing

We welcome any and all feedback, suggestions and improvements submitted via
issues and pull requests on [GitHub]. For guidelines, please see the
[CONTRIBUTING.md] document. 

## Credits

This extension was developed by Joseph Leedy and Yair García Torres of [Wagento]
in co-operation with LINK Mobility.

[Wagento]: https://wagento.com
[LINK Mobility]: https://www.linkmobility.com
[User Guide]: https://docs.wagento.com/extensions/sms-notifications/UserGuide.pdf
[Magento Marketplace]: https://marketplace.magento.com/wagento-module-sms-notifications.html
[GitHub]: https://github.com/wagento/sms-notifications
[OSL]: https://opensource.org/licenses/OSL-3.0.php
[LICENSE.txt]: ./LICENSE.txt
[CHANGELOG.md]: ./CHANGELOG.md
[CONTRIBUTING.md]: ./CONTRIBUTING.md
[Support]: mailto:support@wagento.com?subject=[SMS%20Notifications]%20
[post]: #post-install-post-update-or-post-uninstall