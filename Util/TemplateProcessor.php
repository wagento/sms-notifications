<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
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

use Zend\Filter\FilterInterface;

/**
 * Template Processor
 *
 * @package Wagento\SMSNotifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Magento2.PHP.FinalImplementation.FoundFinal -- This class is not meant to be extended. Inherit from
 * its interface instead.
 */
final class TemplateProcessor implements TemplateProcessorInterface
{
    /**
     * @var \Zend\Filter\FilterInterface
     */
    private $converter;

    public function __construct(FilterInterface $converter)
    {
        $this->converter = $converter;
    }

    /**
     * Replaces variables in a template with their real values
     *
     * @param string $template
     * @param string[] $data Key-value pairs to replace in template (key is variable, value is replacement)
     * @return string
     */
    public function process(string $template, array $data, string $listSeparator = ', '): string
    {
        foreach ($data as $variable => $value) {
            if (is_array($value)) {
                $value = implode($listSeparator, $value);
            }

            if (is_object($value)) {
                $objectValues = $this->processObjectValue($template, $variable, $value);
                $template = $this->process($template, $objectValues, $listSeparator);
                continue;
            }

            $template = str_replace('{{' . $variable . '}}', $value, $template);
        }

        return $template;
    }

    private function processObjectValue(string $template, string $variable, $object): array
    {
        $values = [];

        preg_match_all('/{{(' . $variable . '\.(\w+))}}/', $template, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (array_key_exists($match[2], get_class_vars(get_class($object)))) {
                $values[$match[1]] = $object->{$match[2]};
                continue;
            }

            $method = 'get' . $this->converter->filter($match[2]);

            if (method_exists($object, $method)) {
                $values[$match[1]] = $object->{$method}();
            }
        }

        return $values;
    }
}
