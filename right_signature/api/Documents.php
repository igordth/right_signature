<?php
/**
 * Documents api
 */

namespace right_signature\api;

use right_signature\RightSignature;

class Documents extends RightSignature
{
    /**
     * List Documents
     * Returns a paginated listing of documents that the authenticated user is involved in.
     *
     * @param integer $page
     * @param integer $per_page
     *
     * @return string | \SimpleXMLElement
     */
    public function getList($page = 1, $per_page = 10)
    {
        $data = $this->request("/api/documents.xml?page={$page}&per_page={$per_page}");
        return $this->prepareResult($data);
    }

    /**
     * Document Details
     * Returns details and status of a particular document or batch of documents.
     * Extra data such as <form-fields> (data entered by the signers) are only available once the <status> is 'signed'.
     *
     * @param string $guid the document number in RightSignature field guid in getList return
     *
     * @return string | \SimpleXMLElement
     */
    public function getDetails($guid)
    {
        $data = $this->request("/api/documents/{$guid}.xml");
        return $this->prepareResult($data);
    }

    /**
     * Trash Document
     * Moves a Document to the "Trash". The Document will no longer be available for signature.
     * Credentials must be those of the document sender.
     *
     * @param string $guid the document number in RightSignature field guid in getList return
     *
     * @return string | \SimpleXMLElement
     */
    public function trash($guid)
    {
        $data = $this->request("/api/documents/{$guid}/trash.xml", true);
        return $this->prepareResult($data);
    }

    /**
     * Send Document
     * This method is for sending a once-off document that has not been setup as a Template.
     * @param $title string a node specifying the subject of the document.
     * @param $url string file path
     * @param $recipients Recipients
     * @param $tags array A node specifying tags to attach to the document.
     * Tags can be specified as simple tags (name only) or tuples (name/value) pairs. See example below.
     * https://rightsignature.com/apidocs/documentation_intro#/text_tags
     * [
     *    [
     *      'name' => 'sent_from_api',
     *    ],
     *    [
     *      'name' => 'mutual_nda',
     *    ],
     *    [
     *      'name' => 'user_id',
     *      'value' => '123456',
     *    ],
     * ]
     * @param $options array the other parameters of the document
     * [
     *      'action' => 'redirect',
     *      'use_text_tags' => 'false',
     *      'expires_in' => '5 days',
     * ]
     *
     * @return string | \SimpleXMLElement
     */
    public function send($title, $url, Recipients $recipients, array $options = [], array $tags = null)
    {
        $xml_arr = [
            'document' => [
                'action' => isset($options['action']) ? $options['action'] : 'send',
                'callback_location' => Yii::$app->urlManagerFrontEnd->createUrl('/callback/documents'),
                'subject' => $title,
                'expires_in' => isset($options['expires_in']) ? $options['expires_in'] : '5 days',
                'use_text_tags' => isset($options['use_text_tags']) ? $options['use_text_tags'] : 'true',
                'document_data' => [
                    'type' => 'url',
                    'value' => $url,
                ],
                'recipients' => $recipients->getRecipients(),
            ],
        ];
        if (!empty($tags)) $xml_arr['document']['tags'] = $tags;
        $xml = $this->buildXmlFromArray($xml_arr);
        //header("Content-Type: text/xml");echo $xml->saveXML(); exit;
        //if (count($recipients->getRecipients()) > 1) {header("Content-Type: text/xml");echo $xml->saveXML(); exit;}
        $data = $this->request("/api/documents/", true, $xml->saveXML());
        $res = (array) simplexml_load_string($data);
        if (!isset($res['guid'])) {
            throw new Exception($res['message']);
        }
        return $this->prepareResult($data);
    }

    /**
     * Get Signer Links
     * Use a document GUID, which is returned from the Prefill/Send Template call, to make a call to Signer Links.
     * An embedded signing token is returned for each signer whose email was set to “noemail@rightsignature.com” in the Prefill/Send Template call.
     *
     * @param string $guid the document number in RightSignature field guid in getList return
     *
     * @return string | \SimpleXMLElement
     */
    public function getSignerLinks($guid)
    {
        $data = $this->request("/api/documents/{$guid}/signer_links.xml");
        return $this->prepareResult($data);
    }

    /**
     * Send Reminder Emails
     * Sends a reminder email to pending signers. This call can be made a maximum of 3 times per document.
     * Credentials must be those of the document sender.
     *
     * @param string $guid the document number in RightSignature field guid in getList return
     *
     * @return string | \SimpleXMLElement
     */
    public function sendReminders($guid)
    {
        $data = $this->request("/api/documents/{$guid}/send_reminders.xml", true);
        return $this->prepareResult($data);
    }

    public function getSignLink($guid, $recipient = null)
    {
        $this->setResultType(RightSignature::RESULT_TYPE_SIMPLE_XML);
        $xml = $this->objectToArray($this->getSignerLinks($guid));
        $tmp = current(current($xml));
        if (isset($tmp['signer-token'])) {
            $token = $tmp['signer-token'];
        }
        elseif ($recipient) {
            foreach ($tmp as $item) {
                $item = (array) $item;
                if ($item['role'] == $recipient) {
                    $token = $item['signer-token'];
                    break;
                }
            }
            if (empty($token)) {
                throw new Exception("Recipient {$recipient} not found");
            }
        }
        else {
            throw new Exception("Please set the recipient");
        }
        return "https://rightsignature.com/signatures/embedded?rt={$token}";
    }
}