<?php

namespace right_signature;

class RightSignature
{
    const BASE_URL = 'https://rightsignature.com';
    const RESULT_TYPE_XML = 'XML';
    const RESULT_TYPE_SIMPLE_XML = 'SIMPLE XML';
    private $result_type = self::RESULT_TYPE_SIMPLE_XML;
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function setResultType($type)
    {
        $this->result_type = $type;
    }

    public function getResultType()
    {
        return $this->result_type;
    }

    protected function prepareResult($result)
    {
        if ($this->result_type == self::RESULT_TYPE_XML) {
            return $result;
        }
        elseif ($this->result_type == self::RESULT_TYPE_SIMPLE_XML) {
            return simplexml_load_string($result);
        }
        else throw new \Exception('Wrong type result');
    }

    public function request($path, $is_post = false, $body = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::BASE_URL . $path);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: text/xml",
            "charset=utf-8",
            "api-token: " .  $this->token,
        ]);

        if ($body) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }
        elseif ($is_post) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        }

        if ($data = curl_exec($curl)) {
            curl_close($curl);
            return $data;
        }
        else {
            curl_close($curl);
            throw new \Exception('#' . curl_errno($curl) . "\n" . curl_error($curl));
        }
    }

    protected function buildXmlFromArray(array $array, \DOMElement &$base_element = null, $base_key = null)
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $element = new \DOMElement($key, $value);
                if (empty($base_element)) $xml->appendChild($element);
                else $base_element->appendChild($element);
            }
            else {
                if (is_numeric($key)) {
                    $key = substr($base_key, 0, strlen($base_key)-1);
                }
                $element = new \DOMElement($key);
                if (empty($base_element)) $xml->appendChild($element);
                else $base_element->appendChild($element);
                $this->buildXmlFromArray($value, $element, $key);
            }
        }
        return $xml;
    }

    protected function objectToArray($xml_object)
    {
        $out = [];
        foreach ( (array) $xml_object as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->objectToArray ( $node ) : $node;

        return $out;
    }
}