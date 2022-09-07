<?php

namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayOneService;
use App\Services\Payment\PaymentGatewayTwoService;

class SendGatewayCallback
{
    /**
     * Handle the event.
     * What gateway pay going?
     *
     * @return void
     */
    public function handle(PaymentStatusChanged $event)
    {
        $payment = $event->payment;
        $service = null;

        switch ($payment->gateway_id) {
            case 1: {
                $service = new PaymentGatewayOneService();
            }
            case 2: {
                $service = new PaymentGatewayTwoService();
            }

            $service?->sendCallback($payment);
        }
    }
}
