<?php

namespace Omnipay\AuthorizeNet;

use Omnipay\Common\AbstractGateway;
use Omnipay\AuthorizeNet\Message\AuthorizeRequest;


/**
 * OGone Class
 * http://payment-services.ingenico.com/ogone/support/guides/integration%20guides/e-commerce
 */
class AcceptjsGateway extends AIMGateway
{
    public function getName()
    {
        return 'Authorize.Net - Accept.js';
    }

    public function getDefaultParameters()
    {
        $parameters = parent::getDefaultParameters();
        $parameters = array_merge($parameters, array(
            'clientKey' => '',
            'liveEndpoint' => 'https://secure2.authorize.net/gateway/transact.dll',
            'developerEndpoint' => 'https://test.authorize.net/gateway/transact.dll',
        ));
        return $parameters;
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AcceptjsAuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AcceptjsCompleteAuthorizeRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AcceptjsCaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AcceptjsPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->completeAuthorize($parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\AuthorizeNet\Message\AcceptjsVoidRequest', $parameters);
    }
}
