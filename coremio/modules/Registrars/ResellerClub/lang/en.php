<?php 
return [
    'name'              => 'ResellerClub',
    'description'       => 'With ResellerClub.com, one of the popular domain name registrars, all domain name transactions can be made instantaneously through the domain API. To do this, define your ResellerClub.com client account information in the following fields.',
    'import-tld-button' => 'Import',
    'fields'            => [
        'auth-userid'   => 'User ID',
        'api-key'       => 'API Key',
        'test-mode'     => 'Test Mode',
        'WHiddenAmount' => 'WhoIS Protection Fee',
        'adp'           => 'Update pricing automatically',
        'cost-currency' => 'Your Resellerclub Currency',
        'import-tld'    => 'Import Extensions',
    ],
    'desc'              => [
        'auth-userid'   => 'It can be found in "Control Panel / Settings / Personal Information / Primary Profile" section',
        'api-key'       => 'It can be found in "Control Panel / Settings / API" section',
        'customer-id'   => 'Resellerclub is customer account ID information, created in your Resellership account. All transactions are carried out via the customer\'s account.',
        'WHiddenAmount' => '<br>Ask for a fee for whois protection service.',
        'test-mode'     => 'Activate to process in test mode.',
        'adp'           => 'Automatically pulls pricing daily and the price is set at the profit rate',
        'cost-currency' => 'Your ResellerClub Currency',
        'import-tld-1'  => 'Automatically import all extensions',
        'import-tld-2'  => 'All domain extensions and costs registered on the API will be imported collectively',
    ],
    'tab-detail'        => 'API Information',
    'tab-import'        => 'Import',
    'test-button'       => 'Test Connection',
    'import-note'       => 'You can easily transfer the domain names that are already registered in provider\'s system. The imported domain names are created as an addon, domain names that are currently registered in system are marked green.',
    'import-button'     => 'Import',
    'save-button'       => 'Save settings',
    'error1'            => 'API information is not available',
    'error2'            => 'Domain and extension information are not present',
    'error3'            => 'An error occurred while retrieving the contact ID',
    'error4'            => 'Failed to get status information',
    'error5'            => 'The transfer information could not be retrieved',
    'error6'            => 'Please enter the API information.',
    'error7'            => 'The import operation failed',
    'error8'            => 'An error has occurred',
    'success1'          => 'Settings saved successfully',
    'success2'          => 'The connection test succeeded',
    'success3'          => 'Import completed successfully',
    'success4'          => 'Extensions were successfully imported',
];