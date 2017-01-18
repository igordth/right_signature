<?php
/**
 * Example send document to RightSignature to sign by two recipients
 * set token, base_url and recipients in config.php
 */

use right_signature\RightSignature;
use right_signature\api\Recipients;

// load config
$config = include('../config.php');

// set relation url to file
$url_to_file = '/samples/resources/pdf/two_recipients.pdf';

$rightSignature = new RightSignature($config['token']);
$documentsApi = $rightSignature->loadDocumentsApi();

// create recipients
$recipients = new Recipients();
foreach ($config['two_recipients']['recipients'] as $v) {
    $recipients->add($v['name'], $v['email'], Recipients::ROLE_SIGNER, true);
}
// set sender (owner of token) role to copy
$recipients->setSenderRole(Recipients::ROLE_CC);

// set result type to xml
$documentsApi->setResultType(RightSignature::RESULT_TYPE_XML);

// send document to sign
$output = $documentsApi->send(
    'Two recipients',
    $config['base_url'] . $url_to_file,
    $recipients,
    $config['base_url'] . '/callback.php'
);

// show output
header('Content-Type: application/xml');
echo $output;