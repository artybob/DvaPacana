<?php

namespace App\Services\Payment;

use App\Models\Payment;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PaymentGatewayTwoService extends AbstractPaymentGatewayService
{

    protected $app_id = '816';
    protected $app_key = 'rTaasVHeteGbhwBx';
    protected $callback_url = 'callback_url';

    public function __construct()
    {
        parent::__construct();
        $this->limit = '1000';
    }

    /**
     * @param Payment $payment
     * @return void
     */
    public function sendCallback(Payment $payment)
    {
        if (!$this->checkStatus($payment, 'inprogress')) {
            return;
        }
        if (!$this->checkLimit($payment, $payment->amount, 'completed', 2)) {
            return;
        }

        try {
            $payload = [
                'project' => 816,
                'invoice' => 73,
                'status' => 'completed',
                'amount' => $payment->amount,
                'amount_paid' => $payment->amount,
                'rand' => Str::random(8),
            ];

            $this->headers = [
                'Authorization' => $this->makeSign($payload),
                'Content-Type' => 'multipart/form-data'
            ];

            $response = $this->client->post($this->callback_url, [
                'query' => $payload,
                'headers' => $this->headers,
            ]);

        } catch (GuzzleException $e) {
            //TODO: make return $e->getMessage();
            return;
        }

        //TODO: make return $response->getBody();
        return;
    }

    /**
     * @param $payload
     * @return string
     */
    public function makeSign($payload): string
    {
        $sign = implode('..', $payload);
        $sign .= $this->app_key;

        return md5($sign);
    }

}
