const value = +document
    .querySelector('.product-checkout-sum')
    .innerHTML.split(' ')[3];
console.log(value);

paypal
    .Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [
                    {
                        amount: {
                            value: value,
                        },
                    },
                ],
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                const collectionDay =
                    document.querySelector('#collection-day').innerHTML;
                const collectionTime =
                    document.querySelector('#collection-time').innerHTML;
                const discountCoupon =
                    document.querySelector('#discount-coupon').innerHTML || '';

                window.location.replace(
                    `./orderProducts.php?collection_day=${collectionDay}&collection_time=${collectionTime}&discount_coupon=${discountCoupon}`
                );
            });
        },
    })
    .render('#paypal-payment-button');
