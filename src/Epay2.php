<?php

namespace Relesssar\Epay2;

class Epay2
{
    protected $isTest = 0;
    protected $host = '';
    protected $terminalID = '';
    protected $clientID = '';
    protected $clientSecret = '';
    protected $token = '';


    public function __construct($IsTest = 0, $Host,$TerminalID,$ClientID,$ClientSecret)
    {
        $this->isTest    = $IsTest;
        $this->host    = $Host;
        $this->terminalID    = $TerminalID;
        $this->clientID    = $ClientID;
        $this->clientSecret    = $ClientSecret;
    }

    protected function request_token($host, array $post=null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        $error = curl_errno($ch);
        if ($error) {
            echo 'Error:' . curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode == 200) {
            $parse_result = json_decode($result, true);
            if ($parse_result['access_token']) {
                return $parse_result['access_token'];
            } else {
                echo $result;
                return null;
            }
        } else {
            return 'error 200'.$result;
        }
        curl_close($ch);
    }


    protected function request($host, $token, $amount=null) {

        $ch = curl_init();

        if (is_int($amount) and $amount>0) {
            $host = $host . '?amount=' . (integer)$amount;
        }
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$token,
        ]);

        $result = curl_exec($ch);
        $error = curl_errno($ch);
        if ($error) {
            return 'Error:' . curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode == 200) {
            return $result;
        } else {
            return $result;
        }
        curl_close($ch);
    }

    public function charge($transaction_id,$amount = null) {
        $token = $this->getToken_regular();

        $url = '/operation/'.$transaction_id.'/charge';

        if ($this->isTest == 1) {
            $host = 'https://testepay.homebank.kz/api';
        } else {
            $host = 'https://epay-api.homebank.kz';
        }
        $host = $host . $url;

        $result = $this->request($host,$token,$amount);

        return $result;
    }

    // full refund
    public function refund($transaction_id,$amount = null) {
        $token = $this->getToken_regular();

        $url = '/operation/'.$transaction_id.'/refund';

        if ($this->isTest == 1) {
            $host = 'https://testepay.homebank.kz/api';
        } else {
            $host = 'https://epay-api.homebank.kz';
        }
        $host = $host . $url;

        $result = $this->request($host,$token,$amount);

        return $result;
    }

    public function getToken_pay($request) {
        $url = '/oauth2/token';
        if ($this->isTest == 1) {
            $host = 'https://testoauth.homebank.kz/epay2';
        } else {
            $host = 'https://epay-oauth.homebank.kz';
        }
        $host = $host . $url;

        $post = array(
            'grant_type' => 'client_credentials',
            'scope' => 'webapi usermanagement email_send verification statement statistics payment',
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'invoiceID' => $request['invoiceID'] ? $request['invoiceID'] : '',
            'amount' => $request['amount'] ? $request['amount'] : '',
            'currency' => $request['currency'] ? $request['currency'] : '',
            'terminal' => $this->terminalID,
            'postLink' => $request['postlink'] ? $request['postlink'] : '',
            'failurePostLink' => $request['faillink'] ? $request['faillink'] : '',
        );

        $result = $this->request_token($host,$post);
        return $result;
    }

    public function getToken_regular() {
        $url = '/oauth2/token';
        if ($this->isTest == 1) {
            $host = 'https://testoauth.homebank.kz/epay2';
        } else {
            $host = 'https://epay-oauth.homebank.kz';
        }
        $host = $host . $url;

        $post = array(
            'grant_type' => 'client_credentials',
            'scope' => 'webapi usermanagement email_send verification statement statistics payment',
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
        );

        $result = $this->request_token($host,$post);
        return $result;
    }

}