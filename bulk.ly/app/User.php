<?php

namespace Bulkly;



use Illuminate\Notifications\Notifiable;

use Laravel\Cashier\Billable;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    use Billable;

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password', 'varifide', 'bfriday', 'ip','token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function groups()
    {
        return $this->hasMany('Bulkly\SocialPostGroups', 'user_id');
    }

    public function socialaccounts()
    {
        return $this->hasMany('Bulkly\SocialAccounts', 'user_id');
    }

    public function plan()
    {
        if($this->stripe_id) :
            if( $this->subscriptions[0]) :
                if($this->subscriptions[0]->stripe_plan) :
                    $plan = \Bulkly\Plan::where('slug', $this->subscriptions[0]->stripe_plan)->first();
                return  $plan;
                endif;
           endif;
       endif;
    }

    public function plansubs()
    {
        $plans = \Bulkly\Plan::all();
        $subscriptionPlan = array();

        foreach ($plans as $key => $plan) {
            if($this->onPlan($plan->slug) === true){
                array_push($subscriptionPlan, array(
                    'plan' => $plans->where('slug', $plan->slug)->first(),
                    'subscription' => $this->subscriptions->sortByDesc(function ($value) { return $value->created_at->getTimestamp(); })->first(),
                    )
                );
            } 
        }

        if(count($subscriptionPlan) > 0 ){
           return $subscriptionPlan[0]; 
        }

        

    }



    

}


