<?php
namespace Commerce\Gateways\Omnipay;


class Acceptjs_GatewayAdapter extends \Commerce\Gateways\OffsiteGatewayAdapter
{
    public function handle()
    {
        // This is the omnipay class name compatible with `Omnipay::create`.
        // See https://github.com/thephpleague/omnipay-common/blob/master/src/Omnipay.php#L100-L103
        return "AuthorizeNet_Acceptjs";
    }

    public function populateRequest($request, \Craft\BaseModel $paymentForm)
    {
        parent::populateRequest($request, $paymentForm);
        $request->setOpaqueDataDescriptor(\Craft\craft()->request->getPost('opaqueDataDescriptor'));
        $request->setOpaqueDataValue(\Craft\craft()->request->getPost('opaqueDataValue'));
    }

    // not enabling this because it relies on all that javascript
    // to set things up, and I don't want to send cc data to our
    // server by mistake.
    public function cpPaymentsEnabled()
    {
        return false;
    }
}
