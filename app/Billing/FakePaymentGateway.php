<?php

namespace App\Billing;

class FakePaymentGateway
{
    public function getValidTestToken()
    {
        return "valid-token";
    }
}