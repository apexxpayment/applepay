define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'applepay_gateway',
                component: 'Apexx_Applepay/js/view/payment/method-renderer/applepay_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
