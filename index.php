<?php
/**
 * Testing right signature api
 */

use right_signature\RightSignature;

$token = '________set_your_token_________';
$rightSignature = new RightSignature($token);
$documentsApi = $rightSignature->loadDocumentsApi();