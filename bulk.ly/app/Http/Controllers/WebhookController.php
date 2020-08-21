<?php

namespace Bulkly\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class WebhookController extends CashierController
{
    /**
     * Handle a Stripe webhook.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handleCustomerSubscriptionCreated($payload)
    {
        // Handle The Event
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ( $user ) {

            $subscription = $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->first();

            $subscription->current_period_start = \Carbon\Carbon::createFromTimestamp($payload['data']['object']['current_period_start']);
            $subscription->current_period_end =  \Carbon\Carbon::createFromTimestamp($payload['data']['object']['current_period_end']);
            $subscription->status =  $payload['data']['object']['status'];
            $subscription->save();

            return $subscription;
        }
    }


    /**
     * customer.subscription.updated
     *
     * Handle a Stripe webhook.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handleCustomerSubscriptionUpdated($payload)
    {
        // Handle The Event
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ( $user ) {
            $subscription = $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->first();
            $subscription->current_period_start = \Carbon\Carbon::createFromTimestamp($payload['data']['object']['current_period_start']);
            $subscription->current_period_end =  \Carbon\Carbon::createFromTimestamp($payload['data']['object']['current_period_end']);
            $subscription->status =  $payload['data']['object']['status'];
            $subscription->name =  $payload['data']['object']['plan']['name'];


            try{
                $client = new Client;
                $result = $client->request('POST', 'https://api2.autopilothq.com/v1/contact', [
                    'headers' => [
                        'autopilotapikey' => env('AUTOP'),
                        'Content-Type'     => 'application/json'
                    ],
                    'json' => [
                        'contact' => [
                            'Email' => $user->email,
                            'custom' => [
                                'string--Subscription--Status' => $payload['data']['object']['status'],
                                'string--Subscription--Plan' => $payload['data']['object']['plan']['name'],
                                ],
                            '_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
                        ]
                    ]
                ]);
                
            } catch (RequestException $e) {
                
            } catch (ClientException $e) {
                
            }


            $subscription->save();
            return $subscription;
        }

    }
    

}