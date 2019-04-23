<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Util;

/**
 * Template Processor Interface
 *
 * @package Wagento\SMSNotifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 */
interface TemplateProcessorInterface
{
    /**
     * Replaces variables in a template with their real values
     *
     * @param string $template
     * @param string[] $data Key-value pairs to replace in template (key is variable, value is replacement)
     * @return string
     */
    public function process(string $template, array $data, string $listSeparator = ', '): string;
}
