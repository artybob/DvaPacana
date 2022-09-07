<?php

namespace App\Services\Payment;

use App\Models\Payment;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Hash;

class PaymentGatewayOneService extends AbstractPaymentGatewayService
{
    protected $merchant_id = '6';
    protected $merchant_key = 'KaTf5tZYHx4v7pgZ';
    protected $callback_url = 'callback_url';

    public function __construct()
    {
        parent::__construct();
        $this->headers = ['Content-Type' => 'application/json'];
        $this->limit = '100';
    }

    /**
     * @param $payment
     * @return void
     */
    public function sendCallback($payment)
    {
        if (!$this->checkStatus($payment, 'pending')) {
            return;
        }

        if (!$this->checkLimit($payment, $payment->amount, 'paid', 1)) {
            return;
        }

        try {
            $payload = [
                'merchant_id' => $this->merchant_id,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'amount_paid' => $payment->amount,
                'timestamp' => $payment->updated_at,
            ];

            $sign = $this->makeSign($payload);
            $payload['sign'] = $sign;

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
        unset($payload['sign']);

        $sign = implode(':', $payload);
        $sign .= $this->merchant_key;
        return Hash::make($sign);
    }
}
