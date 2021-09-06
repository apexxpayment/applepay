<?php

/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */

namespace Apexx\Applepay\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Payment\Gateway\Helper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AuthorizationRequest
 * @package Apexx\Applepay\Gateway\Request
 */
class AuthorizationRequest implements BuilderInterface {

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ApplePayHelper
     */
    protected $applepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * AuthorizationRequest constructor.
     * @param ConfigInterface $config
     * @param ApplePayHelper $applepayHelper
     * @param SubjectReader $subjectReader
     * @param ApexxBaseHelper $apexxBaseHelper
     */
    public function __construct(
        ConfigInterface $config,
        ApplePayHelper $applepayHelper,
        SubjectReader $subjectReader,
        ApexxBaseHelper $apexxBaseHelper
    ) {
        $this->config = $config;
        $this->applepayHelper = $applepayHelper;
        $this->subjectReader = $subjectReader;
        $this->apexxBaseHelper = $apexxBaseHelper;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject) {
    	
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        
        /** @var PaymentDataObjectInterface $payment */
        $delivery = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $amount = $buildSubject['amount']*100;

        $formFields = [];

        $requestData = [
            //"organisation" => $this->apexxBaseHelper->getOrganizationId(),
            //"account" => $this->apexxBaseHelper->getAccountId(),
            "account" => '79da6583ac01482f9061bbe1136b90c9',
            "amount" => $amount,
            "capture_now" => $this->applepayHelper->getApplepayPaymentAction(),
            "customer_ip" => $order->getRemoteIp(),
            "dynamic_descriptor" => $this->applepayHelper->getDynamicDescriptor(),
            "merchant_reference" => $this->apexxBaseHelper->getStoreCode().$order->getOrderIncrementId(),
            "recurring_type" => $this->applepayHelper->getRecurringType(),
            "user_agent" => $this->apexxBaseHelper->getUserAgent(),
            "webhook_transaction_update" => $this->applepayHelper->getWebhookUrl(),
            "currency" => $this->apexxBaseHelper->getStoreBaseCurrency(),
            "shopper_interaction" => $this->applepayHelper->getShopperInteraction(),
            "three_ds" => [
                "three_ds_required" => $this->applepayHelper->getThreeDsRequired(),
            ],
            "billing_address" => [
                "first_name" => $billing->getFirstname(),
                "last_name" => $billing->getLastname(),
                "email" => $billing->getEmail(),
                "address" => $billing->getStreetLine1() . '' . $billing->getStreetLine2(),
                "city" => $billing->getCity(),
                "state" => $billing->getRegionCode(),
                "postal_code" => $billing->getPostcode(),
                "country" => $billing->getCountryId()
            ],
            "customer" => [
                "customer_id" => $order->getCustomerId(),
                "last_name" => $delivery->getLastname(),
                "postal_code" => $delivery->getPostcode(),
                "account_number" => $order->getCustomerId(),
                "date_of_birth" => $this->applepayHelper->getCustomerDateOfBirth()
            ]
        ];

        $formFields['card']['applepay']['cryptogram'] =  $payment->getAdditionalInformation("cryptogram");
        $formFields['card']['applepay']['expiry_month'] = $payment->getAdditionalInformation("encrypted_data");
        $formFields['card']['applepay']['expiry_year'] = $payment->getAdditionalInformation("expiry_year");
        $formFields['card']['applepay']['dpan'] = $payment->getAdditionalInformation("dpan");

        $finalRequest = array_merge($requestData, $formFields);

        return $finalRequest;
    }

}
