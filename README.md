# LINK Mobility SMS Notifications
by [Wagento][1]

LINK Mobility SMS Notifications integrates Magento 2 with the LINK Mobility
mobile messaging service to send transactional text notifications. Customers can
be notified when their order is successfully placed as well as when it is
invoiced, shipped, canceled, refunded, held, or released.

Detailed instructions for installation, configuration and usage can be found in
the [User Guide][3].

## Requirements

* PHP 7.1.3+ or 7.2.0+
* Magento Open Source/Commerce 2.2.27+ or 2.3.0+
* A [LINK Mobility][2] account

## Installation

### Composer (recommended)

We highly recommend purchasing the extension from the [Magento Marketplace][4],
where you can receive the latest version for free. Once purchased, you can use
the following command to install it from a terminal or command prompt:

    cd /path/to/your/site && composer require wagento/module-linkmobility-sms-notifications

**Note:** The Marketplace version is compatible with both Magento 2.3 and 2.2.
For 2.2, PHP 7.1 is required, while 7.2 is recommended for 2.3.

### Manual

This extension can be downloaded from [GitHub][5] and installed into the
`app/code` directory of your Magento installation with these commands:

    cd /path/to/your/site/app/code
    mkdir Wagento
    cd Wagento
    git clone git@github.com:wagento/linkmobility-sms-notifications.git LinkMobilitySMSNotifications

For Magento 2.2, you will need to append this line to `registration.php`:

    require 'compat.inc';

**Warning**: `compat.inc` contains aliases for interfaces and classes that exist
in 2.3 but are not available in 2.2. If any other installed extensions require
these interfaces and/or classes and provide their own work-arounds, you may
experience compatibility issues and/or degraded site performance. If this
occurs, please [open a support ticket][10] and let us know the vendor and
extension name so that we can work with them on a solution.

## Updating

### Composer

If you've installed the extension from the Magento Marketplace using Composer,
run this command from your terminal or command prompt to update it:

    cd /path/to/your/site && composer update wagento/module-linkmobility-sms-notifications

### Manual

If you've installed the extension from GitHub manually, run these commands from
your terminal or command prompt to update it:

    cd /path/to/your/site/app/code/Wagento/LinkMobilitySMSNotifications
    git pull

## Post-Install or Post-Update

To complete the installation or update process, please run these commands:

    cd /path/to/your/site
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy

## Configuration

The settings can configured in the Admin panel at
`Stores > Settings > Configuration > General > SMS Notifications`. For detailed
descriptions of the available options, please refer to the [User Guide][3].

## Support

If you experience any issues or errors while using the extension, please open a
ticket by sending an e-mail to [support@wagento.com][10]. Be sure to include
your domain, PHP version, Magento version, a detailed description of the problem
including steps to reproduce it, and any other relevant information. We do
our best to respond to all legitimate inquires within 48 business hours.

## License

The source code contained in this extension is licensed under [version 3.0 of
the Open Software License (OSL-3.0)][6]. A full copy of the license can be found
in the [LICENSE.txt][7] file.

## History

A full history of the extension can be found in the [CHANGELOG.md][8] file.

## Contributing

We welcome any and all feedback, suggestions and improvements submitted via
issues and pull requests on [GitHub][5]. For guidelines, please see the
[CONTRIBUTING.md][9] document. 

## Credits

This extension was developed by Joseph Leedy and Yair García Torres of
[Wagento][1] in co-operation with LINK Mobility.

[1]: https://wagento.com
[2]: https://www.linkmobility.com
[3]: https://docs.wagento.com/linkmobility/sms-notifications/UserGuide.pdf
[4]: https://marketplace.magento.com/wagento-module-linkmobility-sms-notifications.html
[5]: https://github.com/wagento/linkmobility-sms-notifications
[6]: https://opensource.org/licenses/OSL-3.0.php
[7]: ./LICENSE.txt
[8]: ./CHANGELOG.md
[9]: ./CONTRIBUTING.md
[10]: mailto:support@wagento.com