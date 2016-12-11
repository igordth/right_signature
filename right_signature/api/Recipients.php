<?php
/**
 * Recipients involved (including the sender)
 * The recipient node for the sender should include is_sender set to true and role set to either "signer" or "cc".
 * Each non-sender recipient node should specify a name, email, and role, where role is either "signer" or "cc".
 * To enable signer sequencing within the document, simply specify a signer_sequence_number for each of the recipients specified as "signer" roles.
 * This is optional and if omitted, will default to a non-signer-sequenced scenario.
 * To prevent the editing of a recipients name and email when using a RedirectToken, the recipient can be locked by setting the locked parameter to true.
 * Sample $recipients
 * * [
 *    [
 *      'name' => 'RightSignature',
 *      'email' => 'support@rightsignature.com',
 *      'role' => 'cc',
 *    ],
 *    [
 *      'name' => 'John Bellingham',
 *      'email' => 'john@rightsignature.com',
 *      'role' => 'signer',
 *      'locked' => 'true',
 *    ],
 *    [
 *      'is_sender' => 'true',
 *      'role' => 'signer',
 *    ],
 * ]
 */

namespace right_signature\api;


class Recipients
{
    const ROLE_CC = 'cc';
    const ROLE_SIGNER = 'signer';
    private $recipients = [];

    public function add($name, $email, $role, $locked = false)
    {
        $this->recipients[] = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'locked' => $locked ? 'true' : 'false',
        ];
    }

    public function setSenderRole($role = 'signer')
    {
        $this->recipients[] = [
            'is_sender' => 'true',
            'role' => $role,
        ];
    }

    public function getRecipients()
    {
        if (empty($this->recipients)) throw new \Exception("add at least one recipient");
        return $this->recipients;
    }
}