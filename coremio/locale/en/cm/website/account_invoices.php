<?php 
return [
    'list-meta'                => [
        'title'       => 'My Invoices',
        'keywords'    => NULL,
        'description' => NULL,
        'robots'      => 'NOFOLLOW,NOINDEX',
    ],
    'detail-meta'              => '{num} Invoice Detail',
    'page-title'               => 'My Invoices',
    'page-bulk-payment'        => 'Bulk Pay Invoices',
    'page-title2'              => [
        'content'   => 'Invoice {num}',
        'variables' => '{num}',
    ],
    'bulk-payment-meta'        => [
        'title'       => 'Bulk Invoice Payment',
        'keywords'    => NULL,
        'description' => NULL,
        'robots'      => 'Nofollow,Noindex',
    ],
    'breadcrumb-invoices'      => 'My Invoices',
    'breadcrumb-bulk-payment'  => 'Bulk Payment',
    'user-id'                  => 'Client ID',
    'invoice'                  => 'Invoice',
    'invoice-owner'            => 'Invoice Owner',
    'invoice-num'              => 'Invoice',
    'creation-date'            => 'Creation Date',
    'due-date'                 => 'Last Payment Date',
    'invoice-amount'           => 'Amount',
    'invoice-status'           => 'Status',
    'invoice-operation'        => 'Manage',
    'status-waiting'           => 'Pending Approval',
    'status-refund'            => 'Refunded',
    'status-cancelled'         => 'Cancelled',
    'status-paid'              => 'Paid',
    'status-unpaid'            => 'Unpaid',
    'customer-information'     => 'Client Information',
    'phone'                    => 'Phone',
    'uinfo-identity'           => 'T.C.K.N.',
    'uinfo-company_tax_office' => 'Tax Office',
    'uinfo-company_tax_number' => 'Tax Number',
    'paid-date'                => 'Paid Date',
    'payment-method'           => 'Payment Method',
    'description'              => 'Description',
    'amount'                   => 'Amount',
    'subtotal'                 => 'Subtotal',
    'total-discount-amount'    => 'Total Discount',
    'discounted-total'         => 'Discounted Total Amount',
    'sendbta'                  => 'Sending Invoice To Address',
    'sendbta-amount'           => 'Send With Shipping ({amount})',
    'pmethod_commission'       => [
        'content'   => 'Payment commission with {method}',
        'variables' => '{method}',
    ],
    'tax-amount'               => [
        'content'   => '{rates}TAX %{rate}',
        'variables' => '{rate}',
    ],
    'total-amount'             => 'Amount Payable',
    'share'                    => 'Share Invoice',
    'share-invoice'            => 'Share Invoice',
    'share-invoice-info'       => "<h5><strong>Share the Bill</strong></h5>\n<br>This invoice only belongs to the owner of the invoice with the above information. No one can access or display this invoice unless the invoice owner wants it. The owner of the invoice can send this invoice to a friend and make the payment through his or her friend. The logon process is not required. But some information is hidden for security reasons.<br><br>To forward the invoice to a friend, copy the link below.",
    'print'                    => 'Print',
    'download-pdf'             => 'Download',
    'text1'                    => 'Thank you for your payment',
    'text2'                    => 'You can download the image of your invoice in a short time.',
    'download-invoice'         => 'Official Invoice Download',
    'download-pdf-invoice'     => 'Download',
    'go-back'                  => 'Go Back to Client Area',
    'pay-the-bill'             => 'Make a Payment',
    'sign-in'                  => 'Login',
    'censored-info'            => 'Some information is hidden for security reasons.',
    'payment-turn-back'        => 'Back to Payment Options',
    'continue-button'          => 'Continue',
    'share-url'                => 'Invoice Shared URL',
    'bulk-payment-text1'       => 'Description',
    'bulk-payment-text2'       => 'Amount',
    'bulk-payment-text3'       => 'Subtotal',
    'bulk-payment-text4'       => '(%{rate}) TAX',
    'bulk-payment-text5'       => 'Amount Payable',
    'bulk-payment-text6'       => 'Make a Payment',
    'error1'                   => 'Please select payment method.',
    'error2'                   => 'The payment operation failed.',
    'error3'                   => 'You don\'t have enough funds in your account.',
    'error4'                   => '<strong>{module} Subscription Enabled</strong><br>If you want to pay the invoice now, first cancel the subscription on the service details.',
    'errorx'                   => 'Error!',
    'successx'                 => 'Successful',
    'success'                  => 'The payment process has been completed successfully.',
    'refunded'                 => 'REFUNDED',
];