<?php
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard AG and are explicitly not part
 * of the Wirecard AG range of products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License Version 3 (GPLv3) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Wirecard AG does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard AG does not guarantee their full
 * functionality neither does Wirecard AG assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Wirecard AG does not guarantee the full functionality
 * for customized shop systems or installed plugins of other vendors of plugins within the same
 * shop system.
 *
 * Customers are responsible for testing the plugin's functionality before starting productive
 * operation.
 *
 * By installing the plugin into the shop system the customer agrees to these terms of use.
 * Please do not use the plugin if you do not agree to these terms of use!
 */

namespace Wirecard\ElasticEngine\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class CreditCardDataAssignObserver extends AbstractDataAssignObserver
{
    const TOKEN_ID = 'token_id';
    const VAULT_ENABLER = 'is_active_payment_token_enabler';
    const RECURRING = 'recurring_payment';

    /**
     * @param Observer $observer
     * @return void|null
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        /*if (array_key_exists('jsresponse', $_POST)) {
            foreach ($_POST as $key => $value) {
                $paymentInfo->setAdditionalInformation($key, $value);
            }
        }*/
        if (array_key_exists('jsresponse', $additionalData)) {
            foreach ($additionalData['jsresponse'] as $key => $value) {
                $paymentInfo->setAdditionalInformation($key, $value);
            }
        }

        if (array_key_exists(self::TOKEN_ID, $additionalData)) {
            $paymentInfo->setAdditionalInformation(
                self::TOKEN_ID,
                $additionalData[self::TOKEN_ID]
            );
        }

        if (array_key_exists(self::VAULT_ENABLER, $additionalData)) {
            $paymentInfo->setAdditionalInformation(
                self::VAULT_ENABLER,
                $additionalData[self::VAULT_ENABLER]
            );
        }

        if (array_key_exists(self::RECURRING, $additionalData)) {
            $paymentInfo->setAdditionalInformation(
                self::RECURRING,
                $additionalData[self::RECURRING]
            );
        }
    }
}
