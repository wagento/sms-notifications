<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\ViewModel;

use Wagento\SMSNotifications\Model\Source\SmsType as SmsTypeSource;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * SMS Types View Model
 *
 * @package Wagento\SMSNotifications\ViewModel
 * @author Joseph Leedy <joseph@wagento.com>
 */
final class SmsTypes implements ArgumentInterface
{
    /**
     * @var \Wagento\SMSNotifications\Model\Source\SmsType
     */
    private $smsTypeSource;

    public function __construct(SmsTypeSource $smsTypeSource)
    {
        $this->smsTypeSource = $smsTypeSource;
    }

    public function getSmsTypes(string $field = ''): array
    {
        $smsTypes = $this->smsTypeSource->toArray();

        if (trim($field) !== '') {
            $smsTypes = array_column($smsTypes, $field);
        }

        return $smsTypes;
    }

    public function getGroupedSmsTypes(): array
    {
        $groupedSmsTypes = [];
        $i = 0;

        foreach ($this->smsTypeSource->toArray() as $smsType) {
            $key = array_search($smsType['group'], array_column($groupedSmsTypes, 'groupName'));

            if ($key === false) {
                $key = $i++;
                $groupedSmsTypes[$key] = [
                    'groupName' => $smsType['group'],
                    'title' => ucwords(str_replace('_', ' ', $smsType['group'])),
                    'smsTypes' => []
                ];
            }

            $groupedSmsTypes[$key]['smsTypes'][] = [
                'code' => $smsType['code'],
                'description' => $smsType['description']
            ];
        }

        return $groupedSmsTypes;
    }
}
