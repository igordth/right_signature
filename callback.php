<?php
/**
 * Callback
 *
 * @link https://rightsignature.com/apidocs/documentation_intro#/callbacks
 */

$body = file_get_contents('php://input');

$xml = simplexml_load_string($body);
// document guid - identify in Right Signature
$guid = $xml->guid;
// document status
$status = $xml->status;

switch ($status) {
    case 'signed':
        // todo signed
        break;
    case 'created':
        //todo created
        break;
    case 'viewed':
        // todo viewed
        break;
    case 'recipient_signed':
        // todo recipient signed
        break;
    default:
        //todo wtf
        break;
}
echo 'success';