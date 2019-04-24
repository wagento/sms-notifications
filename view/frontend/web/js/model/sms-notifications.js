/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

define(['ko'], function (ko) {
    let isSubscribed = ko.observable(false),
        isSubscribing = ko.observable(false),
        selectedSmsTypes = ko.observableArray();

    isSubscribed.extend({ notify: 'always' });

    return {
        isSubscribed: isSubscribed,
        isSubscribing: isSubscribing,
        selectedSmsTypes: selectedSmsTypes
    };
});
