<?php
/**
 * Example send document to RightSignature to sign by one recipient
 * set token, base_url and recipient in config.php
 */

use right_signature\RightSignature;
use right_signature\api\Recipients;

require_once('../autoload.php');

// load config
$config = include('../config.php');

// set relation url to file
$url_to_file = '/samples/resources/pdf/one_recipient.pdf';

$rightSignature = new RightSignature($config['token']);
$documentsApi = $rightSignature->loadDocumentsApi();

// create recipients
$recipients = new Recipients();
foreach ($config['one_recipient']['recipients'] as $v) {
    $recipients->add($v['name'], 'noemail@rightsignature.com', Recipients::ROLE_SIGNER, true);
}
// set sender (owner of token) role to copy
$recipients->setSenderRole(Recipients::ROLE_CC);

// set result type to simple xml object
$documentsApi->setResultType(RightSignature::RESULT_TYPE_SIMPLE_XML);

// send document to sign
$output = (array) $documentsApi->send(
    'One recipient',
    $config['base_url'] . $url_to_file,
    $recipients,
    $config['base_url'] . '/callback.php'
);

$guid = $output['guid'];

$link = $documentsApi->getSignLink($guid, 'signer_A');
?>

<html>
    <head>
        <title>Example send document to RightSignature to sign by one recipient</title>
    </head>
    <body>
        <h3>Sign document by recipient A:</h3>
        <iframe width="706px" scrolling="no" height="600px" frameborder="0" id="signing-frame" src="<?= $link ?>&width=600&height=600"></iframe>
    </body>
</html>
