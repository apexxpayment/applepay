<?php

/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */

namespace Apexx\Applepay\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;

/**
 * Class CaptureDataBuilder
 * @package Apexx\Applepay\Gateway\Request
 */
class CaptureDataBuilder implements BuilderInterface {

  /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ApplePayHelper
     */
    protected  $applepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * CaptureDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param ApplePayHelper $applepayHelper
     * @param ApexxBaseHelper $apexxBaseHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        ApplePayHelper $applepayHelper,
        ApexxBaseHelper $apexxBaseHelper
    )
    {
        $this->subjectReader = $subjectReader;
        $this->applepayHelper = $applepayHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
    }

    /**
     * Create capture request
     *
     * @param array $buildSubject
     * @return array
     */
  public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();
        $amount = $buildSubject['amount']*100;
        $delivery = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        if($payment->getLastTransId())
        {
            $finalRequest = [
                "transactionId" => $payment->getParentTransactionId()
                    ?: $payment->getLastTransId(),
                "amount" => $amount,
                "capture_reference" => "Capture".$order->getOrderIncrementId()
            ];
        } else {
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
        }

        return $finalRequest ;
    }
}
