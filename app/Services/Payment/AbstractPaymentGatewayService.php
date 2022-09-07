<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Support\Carbon;

abstract class AbstractPaymentGatewayService
{

    protected $limit;
    protected $headers = [];
    protected \GuzzleHttp\Client $client;

    function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * send callback to payment gateway
     */
    abstract function sendCallback(Payment $payment);

    /**
     * generate sign
     */
    abstract function makeSign($payload): string;

    /**
     * check daily payment limit
     *
     * @param Payment $payment
     * @param int $newAmount
     * @param string $completedStatus
     * @param int $gatewayId
     * @return bool
     */
    protected function checkLimit(Payment $payment, int $newAmount, string $completedStatus, int $gatewayId): bool
    {
        $sumAmount = Payment::where([
            ['user_id', $payment->user_id],
            ['gateway_id', $gatewayId],
            ['status', $completedStatus],
            ['created_at', '>=', Carbon::yesterday()->format('Y-m-d H:i:s')]
        ])->get('amount')->sum('amount');

        return $sumAmount + $newAmount <= $this->limit;
    }

    /**
     * Check if payment status was changed on progress
     *
     * @param Payment $payment
     * @param string $status
     * @return bool
     */
    protected function checkStatus(Payment $payment, string $status): bool
    {
        return $payment->status == $status;
    }
}
