<?php 
return [
    'cronjobs' => [
        'last-run-time' => '0000-00-00 00:00:00',
        'time-to-process' => [
            'start'     => '09:00',
            'end'       => '18:00',
            'tasks'     => [
                'invoice-create',
                'invoice-cancellation',
                'invoice-reminder',
                'invoice-overdue',
                'invoice-auto-payment',
                'order-suspend',
                'order-cancel',
                'order-terminate',
                'domain-pending-transfer',
                'cancellation-requests',
                'ticket-close',
                'low-balance-remind',
            ],
        ],
        'tasks'         => [
            'periodic-outgoings'             => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'minute',
            ],
            'reminding'                      => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'minute',
            ],
            'auto-currency-rates'            => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
            ],
            'auto-intl-sms-prices'           => [
                'status'        => false,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
            ],
            'auto-define-domain-prices'      => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 12,
                'period'        => 'hour',
            ],
            'auto-backup-db'                 => [
                'status'        => false,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
                'settings'      => [
                    'email'        => '',
                    'ftp-host'     => '',
                    'ftp-port'     => 0,
                    'ftp-username' => '',
                    'ftp-password' => '',
                    'ftp-target'   => '',
                ],
            ],
            'cancellation-requests'          => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
            ],
            'invoice-deletion'               => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 4,
                'period'        => 'hour',
                'settings'      => [
                    'day' => 0,
                ],
            ],
            'invoice-create'                 => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 5,
                'period'        => 'minute',
                'settings'      => [
                    'month'          => 7,
                    'than-one-month' => '15',
                ],
            ],
            'invoice-cancellation'           => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 3,
                'period'        => 'hour',
                'settings'      => [
                    'day' => 0,
                ],
            ],
            'invoice-reminder'               => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
                'settings'      => [
                    'first'  => '10',
                    'second' => '5',
                    'third'  => 1,
                ],
            ],
            'invoice-overdue'                => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
                'settings'      => [
                    'first'  => 1,
                    'second' => 2,
                    'third'  => 3,
                ],
            ],
            'invoice-auto-payment'           => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
            ],
            'order-suspend'                  => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '4',
                    'hour' => '0',
                ],
            ],
            'order-cancel'                   => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 2,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '15',
                    'hour' => '6',
                ],
            ],
            'order-terminate'                => [
                'status'        => false,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 3,
                'period'        => 'hour',
                'settings'      => [
                    'day' => 30,
                    'hour' => '6',
                ],
            ],
            'pending-order-deletion'         => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 4,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '3',
                ],
            ],
            'pending-order-cancellation'         => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 4,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '0',
                ],
            ],
            'domain-pending-transfer'        => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 15,
                'period'        => 'minute',
            ],
            'domain-transfer-unlocked-check' => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 3,
                'period'        => 'hour',
            ],
            'checking-order'                 => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'minute',
            ],
            'ticket-close'                   => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
                'settings'      => [
                    'day' => 2,
                ],
            ],
            'ticket-lock'                    => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
                'settings'      => [
                    'day' => 3,
                ],
            ],
            'clear-logs'                     => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 3,
                'period'        => 'day',
                'settings'      => [
                    'day' => '60',
                ],
            ],
            'clear-notifications'            => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
                'settings'      => [
                    'day' => '2',
                ],
            ],
            'unverified-accounts-deletion'   => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 8,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '0',
                ],
            ],
            'non-order-accounts-deletion'    => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 5,
                'period'        => 'hour',
                'settings'      => [
                    'day' => '0',
                ],
            ],
            'low-balance-remind'             => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
                'settings'      => [
                    'day' => 3,
                ],
            ],
            'clear-storage-logs'             => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 3,
                'period'        => 'day',
                'settings'      => [
                    'day' => 3,
                ],
            ],
            'scheduled-operations'           => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
            ],
            'monthly'                        => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'month',
            ],
            'daily'                          => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'day',
            ],
            'hourly'                         => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'hour',
            ],
            'per-minute'                     => [
                'status'        => true,
                'last-run-time' => '0000-00-00 00:00:00',
                'next-run-time' => '0000-00-00 00:00:00',
                'time'          => 1,
                'period'        => 'minute',
            ],
        ],
    ],
];