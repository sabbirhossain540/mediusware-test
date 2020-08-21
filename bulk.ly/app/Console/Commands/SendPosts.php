<?php

namespace Bulkly\Console\Commands;

use Illuminate\Console\Command;
use Bulkly\SocialPostGroups;
use Bulkly\SocialPosts;
use Bulkly\SocialAccounts;
use Bulkly\RssAutoPost;
use Bulkly\BufferPosting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;

date_default_timezone_set('UTC');

class SendPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendpost';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Post to buffer';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function haveanyschedulepost($posts){
        foreach ($posts as $post) {
            if ($post->status == '0'){
                return 'have';
            }
        }
        return 'nothave';
    }


    public function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function interval($interval, $frequency)
    {

        if ($interval == 'hourly') {
            $hour = 1;
        }
        if ($interval == 'daily') {
            $hour = 24;
        }
        if ($interval == 'weekly') {
            $hour = 7 * 24;
        }
        if ($interval == 'monthly') {
            $hour = 30 * 24;
        }
        $rawinterval = $hour * 60 * 60;
        $intervals = round($rawinterval / $frequency);

        return $intervals;

    }


    public function handle()
    {
        $this->sendPostTest();

        $activeGroups = SocialPostGroups::where('status', 1)->where('type', '!=', 'rss-automation')->where('next_schedule_time', '<', date('Y-m-d H:i:s', time()))->get();
        // $activeGroups = SocialPostGroups::where('status', 1)->where('id', '3511')->get();
        //dd($activeGroups);
        //print_r(count($activeGroups).PHP_EOL);

        // foreach ($activeGroups as $key => $group) {
        //     print_r($group->id.PHP_EOL);
        // }
        //print_r('||||||||||||||||||||'.PHP_EOL);
        foreach ($activeGroups as $key => $group) {
            //print_r("ACTIVE GROUP " . $group->id . "USER id" . $group->user->id);
            // if($group->id == '4962'){
            //     print_r($group->id.PHP_EOL);
            //     print_r('------------------'.PHP_EOL);
            // }

            $today = date('Y-m-d');
            $nextTime = time() + $group->interval_seconds;
            $nextDay = date('Y-m-d', $nextTime);
            if($today != $nextDay && $group->interval == 'daily'){
                $next = date('Y-m-d 00:00:00', $nextTime);
                $group->next_schedule_time = $next;
                $group->save();
            } else {
                $group->next_schedule_time = date('Y-m-d H:i:s', time() + $group->interval_seconds);
                $group->save();
            }

            $timestamp = date('Y-m');
            $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
            $current_date_timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
            $current_date_day = date('d', strtotime($current_date_timestamp));

            $subscription_start_timestamp = date('Y-m-d');

            if($group->user->plansubs() != null){
                $timestamp = $group->user->plansubs()['subscription']->current_period_start;

                $subscription_start_timestamp = $group->user->plansubs()['subscription']->current_period_start;
                $subscription_day = date('d', strtotime($subscription_start_timestamp));

                if ( $current_date_day > $subscription_day ) {
                    $time = strtotime(date('m') . '/' . date('d', strtotime($current_date_timestamp)) . '/' . date('Y') );
                    $timestamp = date('Y-m-d H:i:s', $time);
                }
                else if ( $current_date_day < $subscription_day ) {
                    $subscription_day = date('d', strtotime($subscription_start_timestamp));
                    $time = strtotime(date('m') . '/' . date('d', strtotime($subscription_start_timestamp)) . '/' . date('Y') );
                    $subs_day_month = date('m', $time);

                    // Subscription date previous month
                    $timestamp = date('Y-m-d', mktime(0, 0, 0, date($subs_day_month)-1, $subscription_day, date('Y')));
                }
            }

            //==============================================
            // user can send posts maximum in a month
            //==============================================
            if($group->user->plansubs() != null){
                $user_pph = $group->user->plansubs()['plan']->ppm;
            } else {
                $user_pph = '5000';
            }
            //==============================================
            // user sent post on current period
            //==============================================
            //$user_current_pph = BufferPosting::where('user_id', $group->user->id)->where('created_at', '>', $timestamp)->count();
            $user_current_pph = BufferPosting::where('user_id', $group->user->id)->whereMonth('sent_at', '=', date("m"))->get()->count();

            if($group->id == 1299){
                print_r($user_current_pph);
                print_r('-');
                print_r($user_pph);
            }

            if($user_current_pph < $user_pph) {
                //==============================================
                // user reached the limit of sending post
                //==============================================
                $posts = $group->posts;
                $haveanyschedulepost = $this->haveanyschedulepost($posts);
                if($group->recycle=='1' && $haveanyschedulepost=='nothave'){
                    $start_time = strtotime($group->start_time);
                    $repeat_wait = $group->repeat_wait ? $group->repeat_wait : 0;
                    $nextstart = $start_time + $group->repeat_wait*24*60*60;
                    $nextstart = date('Y-m-d H:i:s',  $nextstart);

                    $group->next_schedule_time = $nextstart;
                    $group->save();

                    $interval = $group->interval;
                    $frequency = $group->frequency;
                    if ($interval == 'hourly') {
                        $hour = 1;
                    }
                    if ($interval == 'daily') {
                        $hour = 24;
                    }
                    if ($interval == 'weekly') {
                        $hour = 7 * 24;
                    }
                    if ($interval == 'monthly') {
                        $hour = 31 * 24;
                    }
                    $rawinterval = $hour * 60 * 60;
                    $intervals = $rawinterval / $frequency;
                    if($group->shuffle == 1){
                        $posts  = $posts->shuffle();
                    }
                    foreach ($posts as $key => $post) {
                        $post = SocialPosts::find($post->id);
                        $post->status = 0;
                        $post->sent_at = null;
                        $post->save();
                    }
                    /*if($group->type=='rss-automation'){
                        $haverssautoposts = array();
                        foreach ($posts as $key => $post) {
                            $rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
                            $haveanyrssautoposts = $this->haveanyschedulepost($rssautoposts);
                            //$haveanyschedulepost = 'have';
                            array_push($haverssautoposts, $haveanyrssautoposts);
                        }
                        if (in_array('have', $haverssautoposts)) {
                        }
                        else {
                            foreach ($posts as $key => $post) {
                                $rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
                                foreach ($rssautoposts as $key => $rssautopost) {
                                    $rssautopost = RssAutoPost::find($rssautopost->id);
                                    $rssautopost->status = 0;
                                    $rssautopost->save();
                                }
                            }
                        }
                    }*/
                }
                if($group->recycle=='0' && $haveanyschedulepost=='nothave'){
                    $updategroup = SocialPostGroups::find($group->id);
                    if($updategroup->type=='rss-automation'){
                        $haverssautoposts = array();
                        foreach ($posts as $key => $post) {
                            $rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
                            $haveanyrssautoposts = $this->haveanyschedulepost($rssautoposts);
                            //$haveanyschedulepost = 'have';
                            array_push($haverssautoposts, $haveanyrssautoposts);
                        }
                        if (in_array('have', $haverssautoposts)) {
                        } else {
                            $updategroup->status = 2;
                        }
                    } else {
                        $updategroup->status = 2;
                    }
                    $updategroup->save();
                }
            }
            else{
                $group->status = 2;
                $group->save();
            }


            if($user_current_pph < $user_pph) {

                //==============================================
                // user can send post
                //==============================================
                if($group->shuffle == 1){
                    $post = SocialPosts::where('status', 0)->where('group_id', $group->id)->inRandomOrder()->first();
                } else {
                    $post = SocialPosts::where('status', 0)->where('group_id', $group->id)->first();
                }

                if($post){
                    $targetaccounts = unserialize($post->group->target_acounts);
                    $checksentpost = array();

                    $post->status = '100';
                    $post->save();

                    if(!empty($targetaccounts)){
                        foreach ($targetaccounts as $key => $targetaccount) {
                            $account = SocialAccounts::find($targetaccount);
                            if(isset($account->status) && $account->status=='1'){

                                if($group->enable_slot == 1){
                                    $___totalSlot = 100;
                                    $url = 'https://api.bufferapp.com/1/profiles/'.$account->account_id.'/updates/pending.json?count=2&access_token='.$account->buffer_token;
                                    $pendingUpdates = file_get_contents($url);
                                    $pendingUpdates = json_decode($pendingUpdates, true);
                                    $___usedSlot = $pendingUpdates['total'];
                                    $___usedSlot = $___usedSlot > 100 ? 100 : $___usedSlot;
                                    $__emptySlot = $___totalSlot - $___usedSlot;
                                    if($group->slot_amount > $__emptySlot){
                                        $post->reason = 'Buffer queue is full to create new post request';
                                        $post->status = '0';
                                        $post->save();
                                        return true;
                                    }
                                }



                                if(isset($account->post_sent)){
                                    if(isset(unserialize($account->post_sent)['count'])){
                                        $old_post_sent_count = unserialize($account->post_sent)['count'];
                                    } else {
                                        $old_post_sent_count = 0;
                                    }
                                } else {
                                    $old_post_sent_count = 0;
                                }

                                $hash = [];
                                if(isset($post->group->type) && ($post->group->type == 'curation' || $post->group->type == 'upload')){
                                    $hash = unserialize($post->group->hash);
                                }
                                $hash_s = '';
                                if(isset($account->type) && $account->type =='facebook'){
                                    $hash_s = isset($hash['fb']) ? $hash['fb'] : '';
                                }
                                if(isset($account->type) && $account->type =='google'){
                                    $hash_s = isset($hash['g']) ? $hash['g'] : '';
                                }
                                if(isset($account->type) && $account->type =='linkedin'){
                                    $hash_s = isset($hash['in']) ? $hash['in'] : '';
                                }
                                if(isset($account->type) && $account->type =='twitter'){
                                    $hash_s = isset($hash['tw']) ? $hash['tw'] : '';
                                }
                                if(isset($account->type) && $account->type =='instagram'){
                                    $hash_s = isset($hash['ins']) ? $hash['ins'] : '';
                                }


                                $hash_s = str_replace(array('{', '}'), array('', ''), $hash_s);
                                $hash_s = explode('|', $hash_s);
                                $rand = mt_rand(0, count($hash_s)-1);
                                $hash_s = $hash_s[$rand];

                                $utm_campaignarr = 	explode('utm_campaign', $post->link);

                                if($account->type=='facebook'){
                                    $utm_source = 'facebook.com';
                                }
                                if($account->type=='google'){
                                    $utm_source = 'plus.google.com';
                                }
                                if($account->type=='linkedin'){
                                    $utm_source = 'linkedin.com';
                                }
                                if($account->type=='twitter'){
                                    $utm_source = 'twitter.com';
                                }
                                if($account->type=='instagram'){
                                    $utm_source = 'instagram.com';
                                }

                                if(isset($utm_campaignarr[1])){
                                    $utm ='';
                                } else {
                                    if(!empty(unserialize($post->group->utm))){
                                        $utm_campaign = !empty(unserialize($post->group->utm)['utm_campaign']) ? unserialize($post->group->utm)['utm_campaign'] : 'Bulkly';
                                        $utm_source = !empty(unserialize($post->group->utm)['utm_source']) ? unserialize($post->group->utm)['utm_source'] : $utm_source;
                                        $utm_medium = !empty(unserialize($post->group->utm)['utm_medium']) ? unserialize($post->group->utm)['utm_medium'] : 'social';
                                        $utm_content = !empty(unserialize($post->group->utm)['utm_content']) ? unserialize($post->group->utm)['utm_content'] : 'Bulkly'.$post->group->id;

                                        $utm = '?utm_campaign='.$utm_campaign.'&utm_source='.$utm_source.'&utm_medium='.$utm_medium.'&utm_content='.$utm_content;
                                    } else {
                                        $utm = '?utm_campaign=Bulkly&utm_source='.$utm_source.'&utm_medium=social&utm_content=Bulkly'.$post->group->id;
                                    }
                                }
                                if($post->group->add_image == 1){
                                    if($post->image == ''){
                                        $html = $this->file_get_contents_curl($post->link);
                                        $doc = new \DOMDocument();
                                        @$doc->loadHTML($html);
                                        $metas = $doc->getElementsByTagName('meta');
                                        $ogImage = '';
                                        for ($i = 0; $i < $metas->length; $i++)
                                        {
                                            $meta = $metas->item($i);
                                            if($meta->getAttribute('property') == 'og:image'){
                                                $ogImage = $meta->getAttribute('content');
                                            }
                                        }
                                        $post->image= $ogImage;
                                    }
                                }
                                if($post->link){
                                    $link_urm = $post->link.''.$utm;

                                    $specialpra = explode('v=', $post->link);

                                    if(isset($specialpra[1])){
                                        $link_urm = $post->link;
                                    }
                                } else {
                                    $link_urm = null;
                                }
                                if($link_urm){
                                    $final_link = $link_urm;
                                    $client = new Client();
                                    if($post->group->user->rebrandly_key){
                                        try {
                                            $rebrandly_domain = $post->group->user->rebrandly_domain;

                                            if($rebrandly_domain) {
                                                $rebrandly_domain = $rebrandly_domain;
                                            } else {
                                                $rebrandly_domain = '';
                                            }


                                            $client = new Client([
                                                'headers' => [
                                                    'Content-Type' => 'application/json',
                                                    'Authorization' => 'Bearer ' . $post->group->user->rebrandly_key
                                                ]
                                            ]);
                                            $result = $client->post('https://api.rebrandly.com/v1/links',
                                                ['body' => json_encode(
                                                    [
                                                        'destination' => $final_link,
                                                        'domain' => [
                                                            'id' => $rebrandly_domain,
                                                        ]
                                                    ]
                                                )]
                                            );
                                        } catch (ClientException $e) {
                                            $result = null;
                                        } catch (RequestException $e) {
                                            $result = null;
                                        }

                                        if($result){
                                            $final_link = 'http://'.json_decode($result->getBody())->shortUrl;
                                        }	else {
                                            $final_link = null;
                                        }
                                    } else {
                                        $final_link = $link_urm;
                                    }

                                } else {
                                    $final_link = null;

                                }
                                if(!$final_link){
                                    $final_link  = $link_urm;
                                }
                                if($post->text || $post->image || $final_link){
                                    $client = new Client();
                                    /*if($post->text != ''){
                                        $post->text = $this->breakMe($post->text);
                                    }*/
                                    $form_params = [
                                        'access_token' => $account->buffer_token,
                                        'profile_ids' => array($account->account_id),
                                        'text' => htmlspecialchars_decode($post->text).' '.$final_link.' '.$hash_s
                                    ];
                                    if($account->type == 'instagram'){
                                        $form_params['text'] = htmlspecialchars_decode($post->text).' '.$final_link.' '.PHP_EOL.PHP_EOL.$hash_s;
                                    }
                                    if(isset($post->image) && $post->image != ''){
                                        $form_params['media']['photo'] = $post->image;
                                    } else if(isset($final_link) && $final_link != ''){
                                        $form_params['media']['link'] = $final_link;
                                    }
                                    //print_r($form_params);

                                    try {
                                        if($form_params['text'] != ''){
                                            $form_params['text'] = str_replace('amp;','',$form_params['text']);
                                        }
                                        if($group->top_buffer_queue == 1){
                                            $form_params['top'] = true;
                                        }
                                        $result = $client->request('POST', 'https://api.bufferapp.com/1/updates/create.json', [
                                            'form_params' => $form_params,
                                        ]);

                                        $json = $result->getBody();

                                    } catch (ClientException $e) {
                                        $json = $e->getResponse()->getBody()->getContents();
                                        $json = json_decode($json, true);
                                        $post->status = '10';
                                        $post->reason = $json['message'].' ('.$account->type.')';
                                        $post->save();

                                        /*if($group->id == '4962'){
                                            print_r('Error ==> '.$json);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);
                                            print_r('ClientException ==> '.$post->id);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);
                                        }*/

                                    } catch (RequestException $e) {
                                        $json = $e->getResponse()->getBody()->getContents();
                                        $json = json_decode($json, true);
                                        $post->status = '10';
                                        $post->reason = $json['message'].' ('.$account->type.')';
                                        $post->save();

                                        /* if($group->id == '4962'){
                                             print_r('Error ==> '.$json);
                                             print_r(PHP_EOL.'============================'.PHP_EOL);
                                             print_r('RequestException ==> '.$post->id);
                                             print_r(PHP_EOL.'============================'.PHP_EOL);
                                         }*/
                                    }
                                    if(isset($result)){
                                        $output = json_decode($result->getBody());
                                        if(isset($output->updates)){
                                            if($output->updates[0]->id){
                                                $checksentpost[] = 'ok';
                                                // array_push($checksentpost, 'ok');
                                                $bufferposting = new BufferPosting;
                                                $bufferposting->user_id = $account->user->id;
                                                $bufferposting->group_id = $group->id;
                                                $bufferposting->post_id = $post->id;
                                                $bufferposting->account_id = $account->id;
                                                $bufferposting->account_service = $account->type;
                                                $bufferposting->buffer_post_id = $output->updates[0]->id;
                                                $bufferposting->post_text = $post->text;
                                                $bufferposting->sent_at = date('Y-m-d H:i:s', time());
                                                $bufferposting->save();
                                            }
                                        }
                                        $new_post_sent = array(
                                            'count' => 1 + $old_post_sent_count,
                                            'last_sent_at' => date('Y-m-d H:i:s', time())
                                        );
                                        $account->post_sent = serialize($new_post_sent);
                                        $account->save();

                                        $today = date('Y-m-d');
                                        $nextTime = time() + $group->interval_seconds;
                                        $nextDay = date('Y-m-d', $nextTime);
                                        if($today != $nextDay && $group->interval == 'daily'){
                                            $next = date('Y-m-d 00:00:00', $nextTime);
                                            $group->next_schedule_time = $next;
                                            $group->save();
                                        } else {
                                            $group->next_schedule_time = date('Y-m-d H:i:s', time() + $group->interval_seconds);
                                            $group->save();
                                        }
                                    }
                                }

                            }
                        }
                    }
                    if(count($checksentpost) > 0){
                        $postUpdateSent = SocialPosts::find($post->id);
                        $postUpdateSent->sent_at = date('Y-m-d H:i:s', time());
                        $postUpdateSent->reason = '';
                        $postUpdateSent->status = 1;
                        $postUpdateSent->save();
                    }
                }
            }
        }

        //print_r(PHP_EOL.'Finish'.PHP_EOL);
        //print_r('======================='.PHP_EOL);
    }

    public function breakMe($str)
    {
        $rv = '';
        if ($str == '' || $str == null) {
            return '';
        }
        $arr = explode(' ', $str);
        if (count($arr) > 0) {
            $hash = 0;
            foreach ($arr as $a) {
                $check = strpos($a, '#');
                if ($check !== false) {
                    if ($hash == 1) {
                        $rv .= $a . ' ';
                    } else {
                        $hash = 1;
                        $rv .= PHP_EOL . $a . ' ';
                    }
                } else {
                    if ($hash == 0) {
                        $rv .= $a . ' ';
                    } else {
                        $hash = 0;
                        $rv .= PHP_EOL . $a . ' ';
                    }
                }
            }
        } else {
            return '';
        }
        return $rv;
    }

    public function sendPostTest(){
        /*print_r(PHP_EOL.'============================'.PHP_EOL);
        print_r(PHP_EOL.'RSS Auto'.PHP_EOL);
        print_r(PHP_EOL.'============================'.PHP_EOL);*/

        $activeGroups = SocialPostGroups::where('status', 1)->where('type', 'rss-automation')->where('next_schedule_time', '<', date('Y-m-d H:i:s', time()))->get();
        //$activeGroups = SocialPostGroups::where('status', 1)->where('id', '3529')->get();
        //dd(Carbon::now(), date('Y-m-d H:i:s', time()), $activeGroups);
        //print_r($activeGroups);
        foreach ($activeGroups as $key => $group) {

            //if($group->id == '4927'){
            //print_r('Group ID ==> '.$group->id);
            //print_r(PHP_EOL.'============================'.PHP_EOL);
            //}

            $today = date('Y-m-d');
            $nextTime = time() + $group->interval_seconds;
            $nextDay = date('Y-m-d', $nextTime);
            if($today != $nextDay && $group->interval == 'daily'){
                $next = date('Y-m-d 00:00:00', $nextTime);
                $group->next_schedule_time = $next;
                $group->save();
            } else {
                $group->next_schedule_time = date('Y-m-d H:i:s', time() + $group->interval_seconds);
                $group->save();
            }

            if($group->user == null){
                continue;
            }

            $timestamp = date('Y-m');
            $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

            if ( $group->user->plansubs() != null ) {
                $timestamp = $group->user->plansubs()['subscription']->current_period_start;
            }
            //dd($timestamp);
            //==============================================
            // user can send posts maximum in a month
            //==============================================
            if($group->user->plansubs() != null){
                $user_pph = $group->user->plansubs()['plan']->ppm;
            } else {
                $user_pph = '5000';
            }
            //==============================================
            // user sent post on current period
            //==============================================
            //$user_current_pph = BufferPosting::where('user_id', $group->user->id)->where('created_at', '>', $timestamp)->count();
            $user_current_pph = BufferPosting::where('user_id', $group->user->id)->whereMonth('sent_at', '=', date("m"))->get()->count();


            if($user_current_pph < $user_pph) {
                //==============================================
                // user reached the limit of sending post
                //==============================================
                $posts = $group->posts;
                $haveanyschedulepost = $this->haveanyschedulepost($posts);
                if($group->recycle=='1' && $haveanyschedulepost=='nothave'){
                    $start_time = strtotime($group->start_time);
                    $repeat_wait = $group->repeat_wait ? $group->repeat_wait : 0;
                    $nextstart = $start_time + $group->repeat_wait*24*60*60;
                    $nextstart = date('Y-m-d H:i:s',  $nextstart);

                    $group->next_schedule_time = $nextstart;
                    $group->save();

                    $interval = $group->interval;
                    $frequency = $group->frequency;
                    if ($interval == 'hourly') {
                        $hour = 1;
                    }
                    if ($interval == 'daily') {
                        $hour = 24;
                    }
                    if ($interval == 'weekly') {
                        $hour = 7 * 24;
                    }
                    if ($interval == 'monthly') {
                        $hour = 31 * 24;
                    }
                    $rawinterval = $hour * 60 * 60;
                    $intervals = $rawinterval / $frequency;
                    if($group->shuffle == 1){
                        $posts  = $posts->shuffle();
                    }
                    foreach ($posts as $key => $post) {
                        $post = SocialPosts::find($post->id);
                        $post->status = 0;
                        $post->sent_at = null;
                        $post->save();
                    }
                    if($group->type=='rss-automation'){
                        foreach ($posts as $key => $post) {
                            $rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
                            foreach ($rssautoposts as $key => $rssautopost) {
                                $rssautopost = RssAutoPost::find($rssautopost->id);
                                $rssautopost->status = 0;
                                $rssautopost->save();
                            }
                        }
                    }
                }
                if($group->recycle=='0' && $haveanyschedulepost=='nothave'){
                    $updategroup = SocialPostGroups::find($group->id);
                    if($updategroup->type=='rss-automation'){
                        $haverssautoposts = array();
                        foreach ($posts as $key => $post) {
                            $rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
                            $haveanyrssautoposts = $this->haveanyschedulepost($rssautoposts);
                            //$haveanyschedulepost = 'have';
                            array_push($haverssautoposts, $haveanyrssautoposts);
                        }
                        if (in_array('have', $haverssautoposts)) {
                        } else {
                            $updategroup->status = 2;
                        }
                    } else {
                        $updategroup->status = 2;
                    }
                    $updategroup->save();
                }
            }
            else{
                $group->status = 2;
                $group->save();
            }

            if($user_current_pph < $user_pph) {

                //==============================================
                // user can send post
                //==============================================

                if($group->type == 'rss-automation'){
                    $now = date('Y-m-d H:i:s');
                    if($group->skip_post_older > 0) {
                        $from = date('Y-m-d H:i:s', strtotime('-'.$group->skip_post_older.' day', strtotime($now)));
                    }
                    else {
                        $from = '1970-01-01 00:00:00';
                    }
                    if($group->skip_post_newer > 0) {
                        $to = date('Y-m-d H:i:s', strtotime('-'.$group->skip_post_newer.' day', strtotime($now)));
                    }
                    else {
                        $to = date('Y-m-d H:i:s');
                    }

                    $item = [];
                    if($group->shuffle == 1){
                        $_exist = 0;
                        if ($group->keyword != '' && $group->skip_keyword > 0) {
                            if($group->last_skip_post != null){
                                $skip = $group->skip_keyword;
                                $last_pub = $group->last_skip_post;
                                $seconds = strtotime(date('Y-m-d H:i:s', time())) - strtotime($last_pub);
                                $hourdiff = $seconds / 60 / 60;
                                $hourdiff = $hourdiff / 24;
                                if ($hourdiff < $skip) {
                                    $_exist = 1;
                                    //$grpModel = new SocialPostGroups();
                                    //$grpModel->where('id', $group->id)->update(['last_skip_post' => Carbon::now()]);
                                }
                            }

                        }
                        //print_r('$_exist ='.$_exist);
                        $rssPostModel = new RssAutoPost();
                        if($rssPostModel->where('pub_date','>=', $from)->where('pub_date','<=', $to)->where('status', '0')->count() > 0){

                            if ($_exist == 1) {
                                $_keyword = $group->keyword;
                                $_keyword = explode(',', $_keyword);
                                $rssPostRand = $rssPostModel
                                    ->select('rss_auto_posts.*')
                                    ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                    ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                    ->where(function($query) use($_keyword){
                                        foreach($_keyword as $word){
                                            $query->where('rss_auto_posts.text', 'NOT LIKE', '%'.$word.'%');
                                        }
                                    })
                                    ->where('rss_auto_posts.pub_date','>=', $from)
                                    ->where('rss_auto_posts.pub_date','<=', $to)
                                    ->where('rss_auto_posts.status', '0')
                                    ->where('social_post_groups.id', $group->id)
                                    // ->get()->random(1);
                                    ->inRandomOrder()->first();
                            } else {
                                $rssPostRand = $rssPostModel
                                    ->select('rss_auto_posts.*')
                                    ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                    ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                    ->where('rss_auto_posts.pub_date','>=', $from)
                                    ->where('rss_auto_posts.pub_date','<=', $to)
                                    ->where('rss_auto_posts.status', '0')
                                    ->where('social_post_groups.id', $group->id)
                                    // ->get()->random(1);
                                    ->inRandomOrder()->first();
                            }

                            if($rssPostRand != null){
                                $item = $rssPostRand;
                            }
                            /*foreach ($rssPostRand as $rnd){
                                $item = $rnd;
                            }*/
                        } else {

                            if ($_exist == 1) {
                                $_keyword = $group->keyword;
                                $_keyword = explode(',', $_keyword);
                                $rssPostRand = $rssPostModel
                                    ->select('rss_auto_posts.*')
                                    ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                    ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                    ->where(function($query) use($_keyword){
                                        foreach($_keyword as $word){
                                            $query->where('rss_auto_posts.text', 'NOT LIKE', '%'.$word.'%');
                                        }
                                    })
                                    ->where('rss_auto_posts.pub_date','>=', $from)
                                    ->where('social_post_groups.id', $group->id)
                                    ->where('rss_auto_posts.pub_date','<=', $to)
                                    // ->get()->random(1);
                                    ->inRandomOrder()->first();
                            } else {
                                $rssPostRand = $rssPostModel
                                    ->select('rss_auto_posts.*')
                                    ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                    ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                    ->where('rss_auto_posts.pub_date','>=', $from)
                                    ->where('social_post_groups.id', $group->id)
                                    ->where('rss_auto_posts.pub_date','<=', $to)
                                    // ->get()->random(1);
                                    ->inRandomOrder()->first();
                            }
                            if($rssPostRand != null){
                                $item = $rssPostRand;
                            }
                            /*foreach ($rssPostRand as $rnd){
                                $item = $rnd;
                            }*/

                        }
                    }
                    else {
                        $_exist = 0;
                        if ($group->keyword != '' && $group->skip_keyword > 0) {
                            if($group->last_skip_post != null){
                                $skip = $group->skip_keyword;
                                $last_pub = $group->last_skip_post;
                                $seconds = strtotime(date('Y-m-d H:i:s', time())) - strtotime($last_pub);
                                $hourdiff = $seconds / 60 / 60;
                                $hourdiff = $hourdiff / 24;
                                if ($hourdiff < $skip) {
                                    $_exist = 1;
                                    //$grpModel = new SocialPostGroups();
                                    //$grpModel->where('id', $group->id)->update(['last_skip_post' => Carbon::now()]);
                                }
                            }
                        }
                        if ($_exist == 1) {
                            $_keyword = $group->keyword;
                            $_keyword = explode(',', $_keyword);
                            $rssPostModel = new RssAutoPost();
                            $item = $rssPostModel->select('rss_auto_posts.*')
                                ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                ->where('rss_auto_posts.pub_date','>=', $from)
                                ->where(function($query) use($_keyword){
                                    foreach($_keyword as $word){
                                        $query->where('rss_auto_posts.text', 'NOT LIKE', '%'.$word.'%');
                                    }
                                })
                                ->where('rss_auto_posts.pub_date','<=', $to)
                                ->where('rss_auto_posts.status', '0')
                                ->where('social_post_groups.id', $group->id)
                                ->first();

                        } else {
                            $rssPostModel = new RssAutoPost();
                            $item = $rssPostModel->select('rss_auto_posts.*')
                                ->leftJoin('social_posts', 'social_posts.id','=','rss_auto_posts.post_id')
                                ->leftJoin('social_post_groups', 'social_post_groups.id','=','social_posts.group_id')
                                ->where('rss_auto_posts.pub_date','>=', $from)
                                ->where('rss_auto_posts.pub_date','<=', $to)
                                ->where('rss_auto_posts.status', '0')
                                ->where('social_post_groups.id', $group->id)
                                ->first();
                        }
                    }

                    if($item){
                        $item->status = '10';
                        $item->save();
                        $postUpdateSent = SocialPosts::find($item->post->id);
                        $postUpdateSent->status = '10';
                        $postUpdateSent->save();

                        $_kCheck = 0;
                        $_keywordSing = $group->keyword;
                        $_keywordSing = explode(',', $_keywordSing);
                        if (count($_keywordSing) > 0) {
                            foreach($_keywordSing as $k){
                                $_workCheck = preg_match("/".strtolower($k)."/", strtolower($item->text));
                                if ($_workCheck > 0) {
                                    $_kCheck = 1;
                                }
                            }
                        }
                        if($_kCheck == 1) {
                            $grpModel = new SocialPostGroups();
                            $grpModel->where('id', $group->id)->update(['last_skip_post' => Carbon::now()]);
                        }



                        if(isset($item->text)){
//                            print_r('Rss Group post item text '. $item->text);
                        } else {
//                            print_r('Rss Group post item text no text');
                        }
                        $item->text = isset($item->text) ? htmlspecialchars($item->text) : '';
                        $targetaccounts = unserialize($group->target_acounts);

                        $checksentpost = array();
                        if(!empty($targetaccounts)){
                            foreach ($targetaccounts as $key => $targetaccount) {
                                $account = SocialAccounts::find($targetaccount);
                                if($account->status=='1'){

                                    if($group->enable_slot == 1){
                                        $___totalSlot = 100;
                                        $url = 'https://api.bufferapp.com/1/profiles/'.$account->account_id.'/updates/pending.json?count=2&access_token='.$account->buffer_token;
                                        $pendingUpdates = file_get_contents($url);
                                        $pendingUpdates = json_decode($pendingUpdates, true);
                                        $___usedSlot = $pendingUpdates['total'];
                                        $___usedSlot = $___usedSlot > 100 ? 100 : $___usedSlot;
                                        $__emptySlot = $___totalSlot - $___usedSlot;
                                        if($group->slot_amount > $__emptySlot){
                                            $item->status = '0';
                                            $item->save();
                                            $postUpdateSent->reason = 'Buffer queue is full to create new post request';
                                            $postUpdateSent->status = '0';
                                            $postUpdateSent->save();
                                            return true;
                                        }
                                    }


                                    if(isset(unserialize($account->post_sent)['count'])){
                                        $old_post_sent_count = unserialize($account->post_sent)['count'];
                                    } else {
                                        $old_post_sent_count = 0;
                                    }
                                    if($group->type == 'rss-automation'){
                                        $hash = unserialize($item->post->hash);
                                    }
                                    if($account->type =='facebook'){
                                        $hash_s = isset($hash['fb']) ? $hash['fb'] : '';
                                    }
                                    if($account->type =='google'){
                                        $hash_s = isset($hash['g']) ? $hash['g'] : '';
                                    }
                                    if($account->type =='linkedin'){
                                        $hash_s = isset($hash['in']) ? $hash['in'] : '';
                                    }
                                    if($account->type =='twitter'){
                                        $hash_s = isset($hash['tw']) ? $hash['tw'] : '';
                                    }
                                    if($account->type =='instagram'){
                                        $hash_s = isset($hash['ins']) ? $hash['ins'] : '';
                                    }

                                    $hash_s = str_replace(array('{', '}'), array('', ''), $hash_s);
                                    $hash_s = explode('|', $hash_s);
                                    $rand = mt_rand(0, count($hash_s)-1);
                                    $hash_s = $hash_s[$rand];

                                    $utm_campaignarr = 	explode('utm_campaign', $item->link);



                                    if($account->type=='facebook'){
                                        $utm_source = 'facebook.com';
                                    }
                                    if($account->type=='google'){
                                        $utm_source = 'plus.google.com';
                                    }
                                    if($account->type=='linkedin'){
                                        $utm_source = 'linkedin.com';
                                    }
                                    if($account->type=='twitter'){
                                        $utm_source = 'twitter.com';
                                    }
                                    if($account->type=='instagram'){
                                        $utm_source = 'instagram.com';
                                    }

                                    if(isset($utm_campaignarr[1])){
                                        $utm ='';
                                    }
                                    else {
                                        if(!empty(unserialize($group->utm))){

                                            $utm_campaign = !empty(unserialize($group->utm)['utm_campaign']) ? unserialize($group->utm)['utm_campaign'] : 'Bulkly';
                                            $utm_source = !empty(unserialize($group->utm)['utm_source']) ? unserialize($group->utm)['utm_source'] : $utm_source;
                                            $utm_medium = !empty(unserialize($group->utm)['utm_medium']) ? unserialize($group->utm)['utm_medium'] : 'social';
                                            $utm_content = !empty(unserialize($group->utm)['utm_content']) ? unserialize($group->utm)['utm_content'] : 'Bulkly'.$group->id;

                                            $utm = '?utm_campaign='.$utm_campaign.'&utm_source='.$utm_source.'&utm_medium='.$utm_medium.'&utm_content='.$utm_content;
                                        } else {
                                            $utm = '?utm_campaign=Bulkly&utm_source='.$utm_source.'&utm_medium=social&utm_content=Bulkly'.$group->id;
                                        }
                                    }

                                    if($group->add_image == 1){
                                        if($item->image == ''){
                                            $html = $this->file_get_contents_curl($item->link);
                                            $doc = new \DOMDocument();
                                            @$doc->loadHTML($html);
                                            $metas = $doc->getElementsByTagName('meta');
                                            $ogImage = '';
                                            for ($i = 0; $i < $metas->length; $i++)
                                            {
                                                $meta = $metas->item($i);
                                                if($meta->getAttribute('property') == 'og:image'){
                                                    $ogImage = $meta->getAttribute('content');
                                                }
                                            }
                                            $item->image= $ogImage;
                                        }
                                    }


                                    if($item->link){
                                        $link_urm = $item->link.''.$utm;
                                        $specialpra = explode('v=', $item->link);
                                        if(isset($specialpra[1])){
                                            $link_urm = $item->link;
                                        }
                                    } else {
                                        $link_urm = null;
                                    }

                                    if($link_urm){
                                        $final_link = $link_urm;
                                        if($group->user->rebrandly_key){
                                            try {
                                                $rebrandly_domain = $group->user->rebrandly_domain;
                                                if($rebrandly_domain) {
                                                } else {
                                                    $rebrandly_domain = '';
                                                }
                                                $client = new Client([
                                                    'headers' => [
                                                        'Content-Type' => 'application/json',
                                                        'Authorization' => 'Bearer ' . $group->user->rebrandly_key
                                                    ]
                                                ]);
                                                $result = $client->post('https://api.rebrandly.com/v1/links',[
                                                    'body' => json_encode(
                                                        [
                                                            'destination' => $final_link,
                                                            'domain' => [
                                                                'id' => $rebrandly_domain,
                                                            ]
                                                        ]
                                                    )]);

                                            } catch (ClientException $e) {
                                                $result = null;
                                            } catch (RequestException $e) {
                                                $result = null;
                                            }

                                            if($result){
                                                $final_link = 'http://'.json_decode($result->getBody())->shortUrl;
                                            }	else {
                                                $final_link = null;
                                            }
                                        } else {
                                            $final_link = $link_urm;
                                        }

                                    } else {
                                        $final_link = null;

                                    }



                                    if(!$final_link){
                                        $final_link  = $link_urm;
                                    }

                                    //dd($group->toArray(), $exist, $item);
                                    if($item->text || $item->image || $final_link){
                                        $client = new Client();
                                        /*if($item->text != ''){
                                            $item->text = $this->breakMe($item->text);
                                        }*/
                                        $form_params = [
                                            'access_token' => $account->buffer_token,
                                            'profile_ids' => array($account->account_id),
                                            'text' => htmlspecialchars_decode($item->text).' '.$final_link.' '.$hash_s
                                        ];
                                        if($account->type == 'instagram'){
                                            $form_params['text'] = htmlspecialchars_decode($item->text).' '.$final_link.' '.PHP_EOL.PHP_EOL.$hash_s;
                                        }
                                        if(isset($item->image) && $item->image != ''){
                                            $form_params['media']['photo'] = $item->image;
                                        } else if(isset($final_link) && $final_link != ''){
                                            $form_params['media']['link'] = $final_link;
                                        }

                                        //print_r($form_params);
                                        try {
                                            if($form_params['text'] != ''){
                                                $form_params['text'] = str_replace('amp;','',$form_params['text']);
                                            }
                                            if($group->top_buffer_queue == 1){
                                                $form_params['top'] = true;
                                            }
                                            $result = $client->request('POST', 'https://api.bufferapp.com/1/updates/create.json', [
                                                'form_params' => $form_params,
                                            ]);
                                            $json = $result->getBody();

                                            /*print_r(PHP_EOL.'============================'.PHP_EOL);
                                            print_r(PHP_EOL.'Sent'.PHP_EOL);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);*/


                                        } catch (ClientException $e) {
                                            $json = $e->getResponse()->getBody();
                                            $item->status = '10';
                                            $item->save();

                                            /*print_r('Error ==> '.$json);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);
                                            print_r('ClientException ==> '.$item->id);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);*/

                                        } catch (RequestException $e) {
                                            $json = $e->getResponse()->getBody();
                                            $item->status = '10';
                                            $item->save();

                                            /*print_r('Error ==> '.$json);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);
                                            print_r('RequestException ==> '.$item->id);
                                            print_r(PHP_EOL.'============================'.PHP_EOL);*/
                                        }
                                        if(isset($result)){
                                            $item->last_post = Carbon::now();
                                            $item->save();
                                            $output = json_decode($result->getBody());
                                            if(isset($output->updates)){
                                                if($output->updates[0]->id){
                                                    $checksentpost[] = 'ok';
                                                    $bufferposting = new BufferPosting;
                                                    $bufferposting->user_id = $account->user->id;
                                                    $bufferposting->group_id = $group->id;
                                                    $bufferposting->post_id = $item->post->id;
                                                    $bufferposting->account_id = $account->id;
                                                    $bufferposting->account_service = $account->type;
                                                    $bufferposting->buffer_post_id = $output->updates[0]->id;
                                                    $bufferposting->post_text = $item->text;
                                                    $bufferposting->sent_at = date('Y-m-d H:i:s', time());
                                                    $bufferposting->save();
                                                }
                                            }
                                            $new_post_sent = array(
                                                'count' => 1 + $old_post_sent_count,
                                                'last_sent_at' => date('Y-m-d H:i:s', time())
                                            );
                                            $account->post_sent = serialize($new_post_sent);
                                            $account->save();

                                            /*$today = date('Y-m-d');
                                            $nextTime = time() + $group->interval_seconds;
                                            $nextDay = date('Y-m-d', $nextTime);
                                            if($today != $nextDay && $group->interval == 'daily'){
                                                $next = date('Y-m-d 00:00:00', $nextTime);
                                                $group->next_schedule_time = $next;
                                                $group->save();
                                            } else {
                                                $group->next_schedule_time = date('Y-m-d H:i:s', time() + $group->interval_seconds);
                                                $group->save();
                                            }*/
                                        }
                                    }
                                }
                            }
                        }

                        if(count($checksentpost) > 0){
                            $item->status = 1;
                            $item->save();

                            $postUpdateSent = SocialPosts::find($item->post->id);
                            $interval = $postUpdateSent->group->interval;
                            $frequency = $postUpdateSent->group->frequency;
                            if ($interval == 'hourly') {
                                $hour = 1;
                            }
                            if ($interval == 'daily') {
                                $hour = 24;
                            }
                            if ($interval == 'weekly') {
                                $hour = 7 * 24;
                            }
                            if ($interval == 'monthly') {
                                $hour = 31 * 24;
                            }
                            $rawinterval = $hour * 60 * 60;
                            $intervals = $rawinterval / $frequency;

                            $actu_lainr = $intervals * $postUpdateSent->group->posts->count();
                            $schedule_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($postUpdateSent->schedule_at)). ' + '.$actu_lainr.' second'));
                            $postUpdateSent->sent_at = date('Y-m-d H:i:s', time());
                            $postUpdateSent->schedule_at =  $schedule_at;
                            $postUpdateSent->status =  1;
                            $postUpdateSent->save();
                        }
                    }

                }
            }


            //print_r('Finish THIS ==> ');
            //print_r(PHP_EOL.'============================'.PHP_EOL);
        }

        return true;

    }
}