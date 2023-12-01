# Migration samples
This folder includes some examples to show how you can interact with our API and the "secuconnect PHP SDK".
The samples ares similar to the samples of the old "secucard SDK", so that you are able to migrate easily.

## Contents

How to
1. get an access token: [init.php](init.php)
2. create a project: [create_project.php](create_project.php)
3. get a list of available payment methods: [get_payment_methods.php](get_payment_methods.php)
4. create payment transactions:
   1. with credit card: [creditcard.php](creditcard.php)
   2. with direct debit: [debit.php](debit.php)
   3. with direct debit (with stakeholder share): [debit_with_basket.php](debit_with_basket.php)
   4. with direct debit (with known bank account data): [debit_with_payment_container.php](debit_with_payment_container.php)
   5. with prepayment: [prepay.php](prepay.php)
   6. with Sofort: [sofort.php](sofort.php)
5. cancel an order: [cancel_order.php](cancel_order.php)
