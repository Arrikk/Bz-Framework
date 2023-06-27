<?php

namespace App\Controllers\Stripe\Webhook;

use App\Controllers\Stripe\Webhook\Service;
use Core\Controller;
use Core\Env;
use Core\Http\Res;
use Core\Pipes\Pipes;

class Webhook extends Controller
{
  private $sig_header;
  function before()
  {
    \Stripe\Stripe::setApiKey(Env::STRIPE_KEY());
    $this->sig_header = ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null);
  }

  public function _webhook(Pipes $payload)
  {
    # Not required if other way,,, 
    # Remove validation error from request
    $event = (object) $payload;
    unset($event->pipe_validation_error);
    // Parse the message body and check the signature

    $webhookSecret = Env::STRIPE_WEBHOOK_SECRET();
    // Res::json($event);
    if (!$webhookSecret) {
      try {
        $event = \Stripe\Webhook::constructEvent(
          (object) $payload,
          $this->sig_header,
          $webhookSecret
        );
      } catch (\Exception $e) {
        return Res::status(403)::error($e->getMessage());
      }
    } else {
      $event = $payload;
    }

    $event = (object) $event;
    $type = $event->type;
    $object = $event->data->object;

    switch ($type) {
      case 'checkout.session.completed':
        break;
      case 'payment_intent.succeeded':
        Service::updateCustomerSubscription($object->customer, $object);
        break;
      case 'subscription.payment_succeeded':
        Service::updateCustomerSubscription($object->customer, $object);
        break;
      case 'customer.subscription.updated':
        Service::updateCustomerSubscription($object->customer, $object);
        break;
      case 'customer.subscription.resumed':
        Service::updateCustomerSubscription($object->customer, $object);
        break;
      case 'customer.subscription.deleted':
      case 'subscription_schedule.aborted':
      case 'subscription_schedule.canceled':
        Service::updateCustomerSubscription($object->customer, $object, false);
        break;
      case 'invoice.paid':
        Service::sendInvoice($object->hosted_invoice_url, $object->customer_email, $object->customer);
        break;
      case 'invoice.payment_failed':
        Service::sendInvoice($object->hosted_invoice_url, $object->customer_email, $object->customer);
        break;
        // ... handle other event types
      default:
        // Unhandled event type
    }
  }
}
