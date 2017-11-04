<?php

namespace Omnipay\AuthorizeNet\Message;

use Omnipay\AuthorizeNet\Model\CardReference;
use Omnipay\AuthorizeNet\Model\TransactionReference;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Authorize.Net Acceptjs Abstract Request
 */
abstract class AcceptjsAbstractRequest extends AIMAbstractRequest
{

}
