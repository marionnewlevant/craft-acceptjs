# Authorize.Net / Accept.js Craft Commerce Gateway

Craft Commerce gateway for using Accept.js with Authorize.net. [Documented here](https://developer.authorize.net/api/reference/features/acceptjs.html)

## Installation and Configuration

Download the zip file and install in the usual way. Enable in the control panel under Settings > Plugins.

Choose the 'Authorize.Net - Accept.js' Gateway when creating a new payment method. The Live Endpoint should be
`https://api.authorize.net/xml/v1/request.api` and the Developer Endpoint should be `https://apitest.authorize.net/xml/v1/request.api`

## How to Use

This is an External, or Off-site gateway. The credit card details are passed to the Accept.js javascript library, which returns a token, that is passed to Craft Commerce in place of the credit card details.

Here is an example payment form:

    <div id="errors">{# javascript will stick any errors here #}</div>
    <form method="POST" id="paymentForm">
      {{ getCsrfInput() }}
      <input type="hidden" name="action" value="commerce/payments/pay"/>
      <input type="hidden" name="redirect" value="/customer/order?number={number}"/>
      <input type="hidden" name="cancelUrl" value="/shop/billing"/>

      <input type="hidden" name="opaqueDataDescriptor" />
      <input type="hidden" name="opaqueDataValue" />

      {# name data #}
      <input type="text" name="firstName"/>
      <input type="text" name="lastName"/>

      {# credit card data. starts off disabled, so if there is no javascript we won't submit the cc number by mistake #}
      <input type="text" id="cardNumberID" disabled />
      <input type="text" id="monthID" disabled />
      <input type="text" id="yearID" disabled />
      <input type="text" id="cardCodeID" disabled />
      <button type="submit">Pay</button>
    </form>

With this form you will need to include Accept.js. Either this one for testing:

    {% includeJsFile 'https://jstest.authorize.net/v1/Accept.js' %}

or this one for the live site:

    {% includeJsFile 'https://js.authorize.net/v1/Accept.js' %}

And you will need this javascript (kind of a mish-mash of jQuery and plain javascript, sorry):

    window.csrfTokenName = "{{ craft.config.csrfTokenName|e('js') }}";
    window.csrfTokenValue = "{{ craft.request.csrfToken|e('js') }}";

    $(function() { // jquery on ready

      var $gForm = $('#paymentForm'); // our payment form

      // enable the cc fields (initially disabled to reduce possibility of submitting them to server)
      $gForm.find('input:disabled').prop('disabled', false);

      $gForm.on('submit', function(e) {
        e.preventDefault();
        sendPaymentDataToAnet();
      });

      function sendPaymentDataToAnet() {
        var secureData = {};
        var authData = {};
        var cardData = {};

        // Extract the card details.
        cardData.cardNumber = document.getElementById("cardNumberID").value;
        cardData.month = document.getElementById("monthID").value;
        cardData.year = document.getElementById("yearID").value;
        cardData.cardCode = document.getElementById("cardCodeID").value;
        // zip and full name are optional, but since we have them, might as well send them.
        cardData.zip = "{{ cart.billingAddress.zipCode ?? '' }}";
        cardData.fullName = $gForm.find('[name="firstName"]').val() + ' ' + $gForm.find('[name="lastName"]').val();

        // The Authorize.Net Client Key is used in place of the traditional Transaction Key. The Transaction Key
        // is a shared secret and must never be exposed. The Client Key is a public key suitable for use where
        // someone outside the merchant might see it.
        authData.clientKey = "{{ cart.paymentMethod.settings.clientKey ?? ''}}";
        authData.apiLoginID = "{{ cart.paymentMethod.settings.apiLoginId ?? ''}}";

        secureData.cardData = cardData;
        secureData.authData = authData;

        // Pass the card number and expiration date to Accept.js for submission to Authorize.Net.
        Accept.dispatchData(secureData, anetResponseHandler);
      }

      // Process the response from Authorize.Net to retrieve the two elements of the payment nonce.
      // If the data looks correct, call the transaction processing function.
      function anetResponseHandler(response) {
        if (response.messages.resultCode === 'Error') {
          for (var i = 0; i < response.messages.message.length; i++) {
            console.log(response.messages.message[i].code + ":" + response.messages.message[i].text);
            $('#errors').append('<p class="error" role="alert">'+response.messages.message[i].code + ': ' + response.messages.message[i].text+'</p>');
          }
        } else {
          sendPaymentToCraft(response.opaqueData);
        }
      }

      function sendPaymentToCraft (responseOpaqueData) {
        // set the new values in the form
        $gForm.find('[name="opaqueDataDescriptor"]').val(responseOpaqueData.dataDescriptor);
        $gForm.find('[name="opaqueDataValue"]').val(responseOpaqueData.dataValue);

        // empty out the cc data
        $('#cardNumberID').val('');
        $('#monthID').val('');
        $('#yearID').val('');
        $('#cardCodeID').val('');

        // and submit (not via jquery)
        $gForm[0].submit();
      }
    });

Brought to you by [Marion Newlevant](http://marion.newlevant.com)
