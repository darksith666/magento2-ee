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

namespace Wirecard\ElasticEngine\Gateway\Request;

use Magento\Checkout\Model\Session;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Wirecard\PaymentSdk\Exception\MandatoryFieldMissingException;
use Wirecard\PaymentSdk\Transaction\PoiPiaTransaction;
use Wirecard\PaymentSdk\Transaction\Transaction;

/**
 * Class PoiPiaTransactionFactory
 * @package Wirecard\ElasticEngine\Gateway\Request
 */
class PoiPiaTransactionFactory extends TransactionFactory
{
    /**
     * @var PoiPiaTransaction
     */
    protected $transaction;

    /**
     * PoiPiaTransactionFactory constructor.
     * @param UrlInterface $urlBuilder
     * @param ResolverInterface $resolver
     * @param StoreManagerInterface $storeManager
     * @param Transaction $transaction
     * @param BasketFactory $basketFactory
     * @param AccountHolderFactory $accountHolderFactory
     * @param ConfigInterface $methodConfig
     * @param Session $session
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ResolverInterface $resolver,
        StoreManagerInterface $storeManager,
        Transaction $transaction,
        BasketFactory $basketFactory,
        AccountHolderFactory $accountHolderFactory,
        ConfigInterface $methodConfig,
        Session $session,
        LoggerInterface $logger
    ) {
        parent::__construct($urlBuilder, $resolver, $transaction, $methodConfig, $storeManager, $accountHolderFactory, $basketFactory, $session, $logger);
    }

    /**
     * @param array $commandSubject
     * @return Transaction
     * @throws \InvalidArgumentException
     * @throws MandatoryFieldMissingException
     */
    public function create($commandSubject)
    {
        parent::create($commandSubject);

        /** @var PaymentDataObjectInterface $payment */
        $payment = $commandSubject[self::PAYMENT];
        $order = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();

        $this->transaction->setAccountHolder($this->accountHolderFactory->create($billingAddress));

        return $this->transaction;
    }

    public function void($commandSubject)
    {
        parent::void($commandSubject);

        return $this->transaction;
    }
}
