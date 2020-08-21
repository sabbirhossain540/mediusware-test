<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;
use Bulkly\User;
use Bulkly\Plan;
use Bulkly\SocialPostGroups;
use Bulkly\SocialAccounts;
use Bulkly\BufferPosting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DB;
use App\Http\Controllers\Input;

class PagesController extends Controller
{
    //
    public function settings()
    {
        if (!Auth::guard('web')->check()) {
            return redirect('/login');
        }
        $user = User::find(Auth::id());
        //dump('Server is updating. please browse after couple of minute');
        //dump($user->subscriptions);

        if ($user->hasStripeId()) {
            $invoices = $user->invoicesIncludingPending();
            $cardData = $user->asStripeCustomer()->sources->data;

            if (!isset($cardData[0]))
                $cards = null;
            else
                $cards = [$cardData[0]];
        } else {
            $invoices = null;
            $cards = null;
        }

        // Get plan name subscription by user id
        $stripePlan = DB::table('subscriptions')->select('stripe_plan')->where('user_id', $user->id)->orderBy('id', 'DESC')->first();
        // Get ppm mean total post can be sent/total quota
        if (isset($stripePlan->stripe_plan)) {
            $totalQuota = DB::table('plans')->select('ppm')->where('slug', $stripePlan->stripe_plan)->first();
        }
        $totalQuota = isset($totalQuota->ppm) ? $totalQuota->ppm : 0;

        // Get remaining posts
        $user_current_pph = 0;
        $user_pph = 0;

        $timestamp = date('Y-m');
        $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

        $current_date_timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
        $current_date_day = date('d', strtotime($current_date_timestamp));

        $subscription_start_timestamp = date('Y-m-d');

        if ($user->plansubs() != null) {
            $subscription_start_timestamp = $user->plansubs()['subscription']->current_period_start;
            $subscription_day = date('d', strtotime($subscription_start_timestamp));

            if ($current_date_day > $subscription_day) {
                $time = strtotime(date('m') . '/' . date('d', strtotime($current_date_timestamp)) . '/' . date('Y'));
                $timestamp = date('Y-m-d H:i:s', $time);
            } else if ($current_date_day < $subscription_day) {
                $subscription_day = date('d', strtotime($subscription_start_timestamp));
                $time = strtotime(date('m') . '/' . date('d', strtotime($subscription_start_timestamp)) . '/' . date('Y'));
                $subs_day_month = date('m', $time);

                // Subscription date previous month
                $timestamp = date('Y-m-d', mktime(0, 0, 0, date($subs_day_month) - 1, $subscription_day, date('Y')));
            }

            $user_current_pph = BufferPosting::where('user_id', $user->id)->where('created_at', '>', $timestamp)->count();

            $user_current_pph = BufferPosting::where('user_id', $user->id)->whereMonth('sent_at', '=', date("m"))->get()->count();
        }

        $remainingPosts = $totalQuota - $user_current_pph;

        return view('pages.settings')
            ->with('user', $user)
            ->with('totalposts', $totalQuota)
            ->with('remaininposts', $remainingPosts)
            ->with('invoices', $invoices)
            ->with('cards', $cards)
            ->with('cards_extra', 1)
            ->with('plans_m', Plan::where('type', 'Month')->orderBy('price')->get())
            ->with('plans_y', Plan::where('type', 'Year')->orderBy('price')->get());;
    }

    public function confirmation()
    {
        $user = User::find(Auth::id());
        if ($user && $user->varifide == 0) {
            return view('auth.confirmation')->with('method', 'get');
        } else {
            return redirect(route('home'));
        }
    }


    public function rebrandlyDomain(Request $request)
    {
        print_r($request->all());

        $user = \Bulkly\User::find(\Auth::id());
        $user->rebrandly_domain = $request->rebrandly_domain;
        $user->save();

    }


    public function confirmationPost()
    {
        $user = User::find(Auth::id());
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['verification_token'] = str_random(25);
        $user->verification_token = $data['verification_token'];
        $user->save();

        
        $mail = Mail::send('mails.confirmation',  $data, function($message) use ($data){
            $message->to($data['email']);            
            $message->subject('Registration Confirmation');
        });
        


        return view('auth.confirmation')->with('method', 'post');
    }

    public function confirmationToken($verification_token, Client $client)
    {
        $user = User::where('verification_token', $verification_token)->first();
        if (!is_null($user)) {
            $user->varifide = 1;
            $user->verification_token = '';
            $user->save();
            return redirect(route('login'))->with('status', 'Your account is now activated. Login below to get started.');
        }
        return redirect(route('login'))->with('status', 'Something went wrong.');
    }

    public function socialAccounts(Request $request)
    {
        $user = User::find(Auth::id());
        $profiles = SocialAccounts::where('user_id', Auth::id())->get();
        return view('pages.social-accounts')->with('profiles', $profiles)->with('user', $user);
    }

    public function analytics()
    {
        $user = User::find(Auth::id());
        return view('pages.analytics')->with('user', $user);
    }

    public function calendar()
    {
        $user = User::find(Auth::id());
        return view('pages.calendar')->with('user', $user);
    }

    public function support()
    {
        $user = User::find(Auth::id());
        return view('pages.support')->with('user', $user);
    }

    public function start()
    {
        $user = User::find(Auth::id());
        return view('pages.start')->with('user', $user);
    }

    public function friday()
    {
        return view('auth.friday');
    }


}