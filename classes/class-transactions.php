<?php
namespace AA_ConfPayments;

use WP_Error;

/**
 * Class Transactions
 * @package AA_ConfPayments
 * @namespace AA_ConfPayments
 */
class Transactions
{

    /**
     * Send transaction
     * @param $data
     * @param $apikey
     * @return array|mixed|object|WP_Error
     */
    public function send_transcation_info( $data, $apikey )
    {
        $post = array(
            'ticketdata' => base64_encode( serialize( $data ) )
        );
        $url = AA_API_BASE . '/wp-json/aasitetickets/v1/conferencetransaction/' . $apikey;
        $ssl = true;

        if (strpos(AA_API_BASE, '.local') > 0) {
            // This is needed for testing with my local server. Apple refuses to update their openssl  ¯\_(ツ)_/¯
            $ssl = false;
        }

        $curl = new Curl($url, array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_SSL_VERIFYPEER => $ssl,
            CURLOPT_SSL_VERIFYHOST => $ssl,
        ));
        $transaction = json_decode($curl);;

        if ($transaction->status != 'SUCCESS') {
            return new WP_Error('transaction', 'There was a problem with this transaction. Please contact us to complete your order');
        }

        return $transaction;
    }

    /**
     * Save transaction
     * @return bool
     */
    public function register_transaction()
    {
        return true;
    }
}



