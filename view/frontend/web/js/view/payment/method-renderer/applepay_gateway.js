define(
        [
            'jquery',
            'Magento_Checkout/js/view/payment/default'
        ],
        function ($, Component) {
            'use strict';

            return Component.extend({
                defaults: {
                    template: 'Apexx_Applepay/payment/form',
                    transactionResult: ''
                   
                },
                getCode: function() {
                    return 'applepay_gateway';
                },
                getData: function () {

                    var data = {
                        'method': this.getCode(),
                        'additional_data': {
                            'cryptogram': $('input[data-apexx="cryptogram"]').val(),
                            'encrypted_data': $('input[data-apexx="expiry_month"]').val(),
                            'expiry_year': $('input[data-apexx="expiry_year"]').val(),
                            'dpan': $('input[data-apexx="dpan"]').val(),
                        }
                    };
                    data['additional_data'] = _.extend(data['additional_data'], this.additionalData);
                    return data;
                }
            });
        }
);

