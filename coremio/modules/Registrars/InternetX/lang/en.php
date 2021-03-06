<?php 
return [
    'name'              => 'InternetX',
    'description'       => '',
    'fields'            => [
        'serverHost'        => 'Server',
        'serverUsername'    => 'Username',
        'serverPassword'    => 'Password',
        'serverContext'     => 'Context',
        'nameServers'       => 'Default Nameservers',
        'domainMX'          => 'MX Record',
        'domainIP'          => 'IP Address',
        'adminContact'      => 'Admin Contact',
        'test-mode'     => 'Sandbox / Test Mode',
        'WHiddenAmount' => 'Whois Protection Fee',
    ],
    'desc'              => [
        'serverHost'        => 'Provide your API url. Eg. https://gateway.autodns.com',
        'serverUsername'    => '',
        'serverPassword'    => '',
        'serverContext'     => 'Default: 4',
        'nameServers'       => 'Provide your Default Nameservers (for Zone) Eg. ns1.demo.autodns2.de',
        'domainMX'          => 'MX record (mailserver). Enter the complete domain host name of the mailserver.',
        'domainIP'          => 'IP address of the zone (A Record).',
        'adminContact'      => 'Specify that the owner will be OwnerC and AdminC and reseller will be Billing/TechC',

        'test-mode'     => 'Activate to process in test mode.',
    ],
    'tab-detail'        => 'API Information',
    'tab-import'        => 'Import',
    'test-button'       => 'Test Connection',
    'import-note'       => 'You can easily transfer the domain names found on the service provider to your existing clients. When you do this, the domain name will be defined as an order for your client. The domain names already registered in the system are coloured green.',
    'import-button'     => 'Import',
    'save-button'       => 'Save settings',
    'error1'            => 'The API information is not available.',
    'error2'            => 'Domain and extension information did not come.',
    'error3'            => 'An error occurred while retrieving the contact ID.',
    'error4'            => 'Failed to get status information.',
    'error5'            => 'The transfer information could not be retrieved.',
    'error6'            => 'Please enter the API information.',
    'error7'            => 'The import operation failed',
    'error8'            => 'An error has occurred.',
    'success1'          => 'Settings saved successfully.',
    'success2'          => 'The connection test succeeded.',
    'success3'          => 'Import completed successfully.',
];
