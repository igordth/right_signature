<?php
/**
 * Configuration file for samples
 */

return [
    'token' => '________set_your_token_________',
    'base_url' => 'http://localhost:80',
    'one_recipient' => [
        'recipients' => [
            [
                'name' => 'Sample Recipient',
                'email' => 'sample_recipient@email.com',
            ],
        ],
    ],
    'two_recipients' => [
        'recipients' => [
            [
                'name' => 'Sample Recipient 1',
                'email' => 'sample_recipient1@email.com',
            ],
            [
                'name' => 'Sample Recipient 2',
                'email' => 'sample_recipient2@email.com',
            ],
        ],
    ],
];