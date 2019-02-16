<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package LinkMobility\SMSNotifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */
declare(strict_types=1);

namespace LinkMobility\SMSNotifications\Util;

use Zend\Filter\FilterInterface;

/**
 * Template Processor
 *
 * @package LinkMobility\SMSNotifications\Util
 * @author Joseph Leedy <joseph@wagento.com>
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
