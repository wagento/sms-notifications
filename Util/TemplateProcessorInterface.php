<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace Linkmobility\Notifications\Util;

/**
 * Template Processor Interface
 *
 * @package Linkmobility\Notifications\Util
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
