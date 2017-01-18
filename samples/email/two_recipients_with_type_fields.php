<?php
/**
 * Example send document to RightSignature to sign by two recipients with type fields
 * See all fields type
 * @link https://rightsignature.com/apidocs/documentation_intro#/text_tags
 * set token, base_url and recipients in config.php
 */

use right_signature\RightSignature;
use right_signature\api\Recipients;

require_once('../autoload.php');

// load config
$config = include('../config.php');

// set relation url to file
$url_to_file = '/samples/resources/pdf/two_recipient_with_type_fields.pdf';

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
    'Two recipients with type fields',
    $config['base_url'] . $url_to_file,
    $recipients,
    $config['base_url'] . '/callback.php'
);

// show output
header('Content-Type: application/xml');
echo $output;