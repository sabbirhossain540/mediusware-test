<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Auth;

use Bulkly\User;

use Bulkly\SocialAccounts;

use Bulkly\BufferPosting;

use GuzzleHttp\Exception\RequestException;

use GuzzleHttp\Exception\ClientException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // get campaign Cookie
        $campaign_cookie = \Cookie::get('campaign');
        if (!$campaign_cookie) {
            $this->middleware('auth');
            $this->middleware('confirmed');
            //$this->middleware('billing');
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $campaign_cookie = \Cookie::get('campaign');
        $campaign_email = \Cookie::get('campaign_email');
        if (!Auth::check() && $campaign_cookie && $campaign_email) {
            $campaign_user = User::where('email', $campaign_email)->first();
            if (!$campaign_user && $request->code != null) {
                try {
                    $client = new Client();
                    $response = $client->request('POST', 'https://api.bufferapp.com/1/oauth2/token.json', [
                        'form_params' => [
                            'client_id' => env('BUFFER_CLIENT_ID'),
                            'client_secret' => env('BUFFER_CLIENT_SECRET'),
                            'redirect_uri' => env('BUFFER_REDIRECT'),
                            'code' => $request->code,
                            'grant_type' => 'authorization_code',
                        ]
                    ]);
                    $buffer_token = json_decode($response->getBody());
                    $bufferuser = file_get_contents('https://api.bufferapp.com/1/user.json?access_token=' . $buffer_token->access_token);
                    $bufferuser = json_decode($bufferuser);
                    $campaign_user = User::where('buffer_id', $bufferuser->id)->first();
                    if ($campaign_user) {
                        \Auth::login($campaign_user);
                    } else {
                        $user_meta = array(
                            'temp_user' => true,
                            'temp_subs' => false,
                        );

                        $campaign_user = new User;
                        $campaign_user->name = ' ';
                        $campaign_user->first_name = ' ';
                        $campaign_user->last_name = ' ';
                        $campaign_user->email = $campaign_email;
                        $campaign_user->password = bcrypt($campaign_email);
                        $campaign_user->varifide = 1;
                        $campaign_user->user_meta = serialize($user_meta);
                        $campaign_user->save();
                        // delete campaign Cookie 
                        \Auth::login($campaign_user);
                    }
                } catch (Exception $e) {
                    dump($e->getMessage());
                }
            } else {
                \Auth::login($campaign_user);
            }

        }


        /*if(!Auth::check()){
            // get campaign Cookie
                $campaign_cookie = \Cookie::get('campaign');
                $campaign_email = \Cookie::get('campaign_email');
                if($campaign_cookie && $campaign_email){
                    $campaign_user = User::where('email', $campaign_email)->first();
                    if(!$campaign_user){
        
                        $user_meta = array(
                            'temp_user' => true,
                            'temp_subs' => false,
                        );
        
                        $campaign_user = new User;
                        $campaign_user->name = ' ';
                        $campaign_user->first_name = ' ';
                        $campaign_user->last_name = ' ';
                        $campaign_user->email = $campaign_email;
                        $campaign_user->password = bcrypt($campaign_email);
                        $campaign_user->varifide = 1;
                        $campaign_user->user_meta = serialize($user_meta);
                        $campaign_user->save();
                        // delete campaign Cookie 
                        \Auth::login($campaign_user);
                    } else {
                        \Auth::login($campaign_user);
                    }
                }
        }*/


        $user = null;
        if (Auth::check()) {
            // get auth user
            $user = User::find(Auth::id());
            $user_meta = unserialize($user->user_meta);
            /*if ($user_meta['temp_subs'] === true) {

            } else {

            }*/


//        if($user->id == 2874){
            //dd(date_default_timezone_get());
//        }

            if ($user->buffer_id == null || (isset($request->code) && $request->code != null)) {
                if (isset($request->code)) {
                    $client = new Client();
                    $response = $client->request('POST', 'https://api.bufferapp.com/1/oauth2/token.json', [
                        'form_params' => [
                            'client_id' => env('BUFFER_CLIENT_ID'),
                            'client_secret' => env('BUFFER_CLIENT_SECRET'),
                            'redirect_uri' => env('BUFFER_REDIRECT'),
                            'code' => $request->code,
                            'grant_type' => 'authorization_code',
                        ]
                    ]);
                    $buffer_token = json_decode($response->getBody());
                    $profiles = file_get_contents('https://api.bufferapp.com/1/profiles.json?access_token=' . $buffer_token->access_token);
                    $profiles = json_decode($profiles);

                    $bufferInfo = file_get_contents('https://api.bufferapp.com/1/user.json?access_token=' . $buffer_token->access_token);
                    $bufferInfo = json_decode($bufferInfo);


                    $user->buffer_id = $bufferInfo->id;
                    $user->timezone = $bufferInfo->timezone;
                    $user->buffer_token = $buffer_token->access_token;
                    $user->save();

                    if ($user->id == '2874') {
                        //dd($profiles);    
                    }

                    $delNotSocIds = [];
                    foreach ($profiles as $key => $profile) {
                        $social_account = SocialAccounts::where('account_id', $profile->id)->where('user_id', $user->id)->first();
                        if (!$social_account) {
                            $sa = new SocialAccounts();
                            $sa->user_id = $user->id;
                            $sa->buffer_id = $bufferInfo->id;
                            $sa->buffer_token = $buffer_token->access_token;
                            $sa->account_id = $profile->id;
                            $sa->type = $profile->service;
                            $sa->name = $profile->formatted_username;

                            try {
                                $___fileName = $user->id . uniqid() . '.jpeg';
                                if (@GetImageSize($profile->avatar_https)) {
                                    $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                                    file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                                } else {
                                    $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                                }

                            } catch (Exception $e) {
                                $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                            }

                            $sa->avatar = $avatar_https;
                            $sa->post_sent = serialize(array());
                            $sa->save();
                            $delNotSocIds[] = $sa->id;
                        } else {

                            try {
                                $___fileName = $user->id . uniqid() . '.jpeg';
                                if (@GetImageSize($profile->avatar_https)) {
                                    $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                                    file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                                } else {
                                    $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                                }

                            } catch (Exception $e) {
                                $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                            }

                            $social_ac = SocialAccounts::find($social_account->id);
                            $social_ac->buffer_token = $buffer_token->access_token;
                            $social_ac->avatar = $avatar_https;
                            $social_ac->save();
                            $delNotSocIds[] = $social_ac->id;
                        }
                        if (count($profiles) > 0) {
                            $social_account = SocialAccounts::whereNotIn('id', $delNotSocIds)->where('user_id', $user->id)->delete();
                        }
                    }

                }
            }
        }


        if ($user != null && $user->buffer_token) {
            $request->session()->put('buffer_token', $user->buffer_token);
        }

        if (isset($bufferuser)) {
            if (!empty($request->code)) {

                $user->buffer_id = $bufferuser->id;
                $user->timezone = $bufferuser->timezone;
                $user->buffer_token = $buffer_token->access_token;
                $user->save();

                $request->session()->put('buffer_token', $buffer_token->access_token);
                $profiles = file_get_contents('https://api.bufferapp.com/1/profiles.json?access_token=' . $request->session()->get('buffer_token'));
                $profiles = json_decode($profiles);
                $newaccountids = array();

                foreach ($profiles as $key => $profile) {
                    $social_account = SocialAccounts::where('account_id', $profile->id)->where('user_id', $user->id)->first();
                    if (!$social_account) {
                        $sa = new SocialAccounts;
                        $sa->user_id = $user->id;
                        $sa->buffer_id = $bufferuser->id;
                        $sa->buffer_token = $buffer_token->access_token;
                        $sa->account_id = $profile->id;
                        $sa->type = $profile->service;
                        $sa->name = $profile->formatted_username;

                        try {
                            $___fileName = $user->id . uniqid() . '.png';
                            $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                            if (@GetImageSize($profile->avatar_https)) {

                                file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                            } else {
                                $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                            }
                        } catch (Exception $e) {
                            $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                        }

                        $sa->avatar = $avatar_https;
                        $sa->post_sent = serialize(array());
                        $sa->save();
                    } else {

                        try {
                            $___fileName = $user->id . uniqid() . '.png';
                            $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                            if (@GetImageSize($profile->avatar_https)) {
                                file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                            } else {
                                $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                            }

                        } catch (Exception $e) {
                            $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                        }

                        $social_ac = SocialAccounts::find($social_account->id);
                        $social_ac->buffer_token = $buffer_token->access_token;
                        $social_ac->avatar = $avatar_https;
                        $social_ac->save();
                    }
                    array_push($newaccountids, $profile->id);
                }

                $oldaccount = SocialAccounts::where('user_id', $user->id)->get();
                foreach ($oldaccount as $key => $oldaccount) {
                    if (in_array($oldaccount->account_id, $newaccountids)) {
                    } else {
                        $oldaccount->delete();
                    }
                }

                $SocialPostGroups = \Bulkly\SocialPostGroups::where('user_id', \Auth::id())->get();

                if (count($SocialPostGroups) == 0) {
                    $user = \Bulkly\User::find(\Auth::id());
                    foreach ($user->socialaccounts as $key => $socialaccount) {
                        $client = new Client();
                        try {
                            $result = $client->request('GET', 'https://api.bufferapp.com/1/profiles/' . $socialaccount->account_id . '/updates/sent.json?count=100&access_token=' . $socialaccount->buffer_token);
                            $json = $result->getBody();
                        } catch (ClientException $e) {
                            $json = $e->getResponse()->getBody();
                        } catch (RequestException $e) {
                            $json = $e->getResponse()->getBody();
                        }
                        $posts = (isset(json_decode($json)->updates) ? json_decode($json)->updates : false);
                        if ($posts) {
                            $group_name = 'Buffer Import – ' . ucwords(str_replace('google', 'google+', $socialaccount->type)) . ' – ' . $socialaccount->name;
                            $group = new \Bulkly\SocialPostGroups;
                            $group->name = $group_name;
                            $group->user_id = $user->id;
                            $group->type = 'upload';
                            $group->status = 0;
                            $group->target_acounts = serialize(array($socialaccount->id));
                            $group->save();
                            foreach ($posts as $key => $bpost) {
                                if ($key < 100) {
                                    $post = new \Bulkly\SocialPosts;
                                    $post->group_id = $group->id;
                                    $post->text = $bpost->text;
                                    $post->link = isset($bpost->media->link) ? $bpost->media->link : null;
                                    $post->image = isset($bpost->media->picture) ? $bpost->media->picture : null;
                                    $post->save();
                                }
                            }
                        }
                    }
                }

                $__email = explode('@', \Auth::user()->email);
                if (isset($__email[1]) && $__email[1] == 'bulk.ly') {
                    return redirect(route('start'));
                } else {
                    return redirect(route('home'));
                }


                $SocialPostGroupsNew = \Bulkly\SocialPostGroups::where('user_id', \Auth::id())->first();
                if ($SocialPostGroupsNew) {
                    //dd(\Auth::user());
//                    return redirect(route('content-pending', $SocialPostGroupsNew->id));
                    return redirect(route('start'));
                } else {
                    dump('No Post Found');
                    return redirect(route('start'));
                }


            }
        }

        if (!Auth::check()) {
            return redirect(route('login'));
        }

        /*if ($request->code) {
            return redirect(route('home'));
        }*/

        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $frequency = array();
        /*foreach ($months as $key => $month) {
            $bufferposting = BufferPosting::where('user_id', Auth::id())->whereMonth('sent_at', '=', $month)->get()->count();
            array_push($frequency, $bufferposting);
        }*/
        $activities = BufferPosting::where('user_id', Auth::id())->limit(5)->orderBy('sent_at', 'desc')->get();
        $services = \DB::table('buffer_postings')->select(\DB::raw('count(*) as count, account_service'))->groupBy('account_service')->where('user_id', Auth::id())->get();
        return view('home')->with('user', $user)->with('services', $services)->with('frequency', $frequency)->with('activities', $activities);


    }

    public function bufferChange(Request $request, $buffer_id)
    {
        // get auth user
        $user = User::find(Auth::id());
        $bufferAccountModel = new BufferAccounts();
        $check = $bufferAccountModel->where('user_id', Auth::id())
            ->where('buffer_id', $buffer_id)
            ->where('is_active', 1)
            ->get()->first();
        if ($check != null) {
            if (!$request->session()->get('buffer_token')) {
                $request->session()->put('buffer_token', $check->buffer_token);
            }
        } else {
            $request->session()->remove('buffer_token');
        }
        $buffer_token = $check->buffer_token;
        $bufferuser = file_get_contents('https://api.bufferapp.com/1/user.json?access_token=' . $buffer_token);
        $bufferuser = json_decode($bufferuser);

        $profiles = file_get_contents('https://api.bufferapp.com/1/profiles.json?access_token=' . $buffer_token);
        $profiles = json_decode($profiles);
        $newaccountids = array();

        foreach ($profiles as $key => $profile) {
            $social_account = SocialAccounts::where('account_id', $profile->id)->where('user_id', $user->id)->first();
            if (!$social_account) {
                $sa = new SocialAccounts;
                $sa->user_id = $user->id;
                $sa->buffer_id = $bufferuser->id;
                $sa->buffer_token = $buffer_token;
                $sa->account_id = $profile->id;
                $sa->type = $profile->service;
                $sa->name = $profile->formatted_username;

                try {
                    $___fileName = $user->id . uniqid() . '.png';
                    $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                    if (@GetImageSize($profile->avatar_https)) {
                        file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                    } else {
                        $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                    }
                } catch (Exception $e) {
                    $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                }

                $sa->avatar = $avatar_https;
                $sa->post_sent = serialize(array());
                $sa->save();
            } else {

                try {
                    $___fileName = $user->id . uniqid() . '.png';
                    $avatar_https = 'https://app.bulk.ly/public/avatar/' . $___fileName;
                    if (@GetImageSize($profile->avatar_https)) {
                        file_put_contents("/home/bulk/public_html/app/public/avatar/" . $___fileName, fopen($profile->avatar_https, 'r'));
                    } else {
                        $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                    }
                } catch (Exception $e) {
                    $avatar_https = 'https://app.bulk.ly/images/avatar.png';
                }

                $social_ac = SocialAccounts::find($social_account->id);
                $social_ac->buffer_token = $buffer_token;
                $social_ac->avatar = $avatar_https;
                $social_ac->save();
            }
            array_push($newaccountids, $profile->id);
        }


        return redirect(route('home'));

    }

}
