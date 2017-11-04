<?php

namespace Omnipay\AuthorizeNet\Message;

/**
 * Authorize.Net Acceptjs Purchase Request
 */
class AcceptjsPurchaseRequest extends AcceptjsAuthorizeRequest
{
    protected $action = 'authCaptureTransaction';
}
