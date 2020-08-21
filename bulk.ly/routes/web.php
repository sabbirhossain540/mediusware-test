<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Bulkly\Billable;
use Bulkly\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Bulkly\SocialPostGroups;
use Bulkly\SocialPosts;
use Bulkly\SocialAccounts;
use Bulkly\RssAutoPost;
use Bulkly\BufferPosting;
use GuzzleHttp\Exception\ClientException;


Route::get('/user/invoice/{invoice}', function (Request $request, $invoiceId) {
    return $request->user()->downloadInvoice($invoiceId, [
        'vendor'  => 'Bulk.ly',
        'product' => 'Subscription Payment',
    ]);
});

Route::get('/group-test/{id}', function(Request $request, $id){

    $group = SocialPostGroups::find($id);

    if($group->type){

        $posts = SocialPosts::where('group_id', $group->id)->whereIn('status', array('1','0'))->get();


        $rsspostsarray = array();
        foreach ($posts as $key => $post) {

            $item = RssAutoPost::where('post_id', $post->id)->where('status', 0)->first();

            if($item){
                array_push($rsspostsarray , $item);
            }


        }
        dump($rsspostsarray[0]);






    }






});






Auth::routes();
Route::post('stripe/webhook', '\Bulkly\Http\Controllers\WebhookController@handleWebhook');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/buffer/change/{buffer_id}', 'HomeController@bufferChange')->name('bufferChange');
Route::resource('subscriptions', 'SubscriptionController');
Route::get('/settings', 'PagesController@settings')->name('settings');;
Route::get('/social-accounts', 'PagesController@socialAccounts')->name('social-accounts');
Route::get('/users/confirmation/', 'PagesController@confirmation');
Route::get('/users/confirmation/{token}', 'PagesController@confirmationToken')->name('confirmation');
Route::post('/users/confirmation/', 'PagesController@confirmationPost');
Route::get('/content-upload/', 'ContentUploadController@index')->name('content-upload');
Route::get('/content-upload/pending/{id}', 'ContentUploadController@pendingGroup')->name('content-pending');
Route::get('/content-upload/active/{id}', 'ContentUploadController@activeGroup')->name('content-active');
Route::get('/content-upload/completed/{id}', 'ContentUploadController@completedGroup')->name('content-completed');
Route::get('/content-curation/', 'ContentCurationController@index')->name('content-curation');
Route::get('/content-curation/pending/{id}', 'ContentCurationController@pendingGroup')->name('content-curation-pending');
Route::get('/content-curation/active/{id}', 'ContentCurationController@activeGroup')->name('content-curation-active');
Route::get('/content-curation/completed/{id}', 'ContentCurationController@completedGroup')->name('content-curation-completed');
Route::get('/rss-automation/', 'RssAutomationController@index')->name('rss-automation');
Route::get('/rss-automation/pending/{id}', 'RssAutomationController@pendingGroup')->name('rss-automation-pending');
Route::get('/rss-automation/active/{id}', 'RssAutomationController@activeGroup')->name('rss-automation-active');
Route::get('/rss-automation/completed/{id}', 'RssAutomationController@completedGroup')->name('rss-automation-completed');

Route::get('/analytics/', 'PagesController@analytics')->name('analytics');
Route::get('/calendar/', 'PagesController@calendar')->name('calendar');
Route::get('/support/', 'PagesController@support')->name('support');
Route::get('/start/', 'PagesController@start')->name('start');


Route::post('/settings/timezone/', 'PagesController@history')->name('saveTimezone');

//Route::get('/friday', 'PagesController@friday')->name('friday');

Route::post('/rebrandly-domain', 'PagesController@rebrandlyDomain')->name('rebrandly.domain');



Route::post('/update-user/', 'AjaxController@updateUser');
Route::post('/upgrade-plan/', 'AjaxController@upgradePlan');
Route::post('/csv-to-content-upload/', 'AjaxController@CsvToContentUpload');
Route::post('/add-content-online/', 'AjaxController@AddContentOnline');
Route::post('/add-content-online-ingroup/', 'AjaxController@AddContentOnlineIngroup');
Route::post('/csv-to-curation-upload/', 'AjaxController@CsvToCurationUpload');
Route::post('/add-curation-online/', 'AjaxController@AddCurationOnline');
Route::post('/add-curation-online-ingroup/', 'AjaxController@AddCurationOnlineIngroup');
Route::post('/csv-to-rss-automation-upload/', 'AjaxController@CsvToRssAutomationUpload');
Route::post('/add-rss-automation-online/', 'AjaxController@AddRssAutomationOnline');
Route::post('/add-rss-automation-online-ingroup/', 'AjaxController@AddRssAutomationOnlineIngroup');
Route::post('/target-social-accounts/', 'AjaxController@TargetSocialAccounts');
Route::post('/schedule-update/', 'AjaxController@ScheduleUpdate');
Route::post('/change-group-status/', 'AjaxController@ChangeGroupStatus');
Route::post('/recycle-group-update/', 'AjaxController@RecycleGroupUpdate');
Route::post('/shuffle-group-update/', 'AjaxController@ShuffleGroupUpdate');

Route::post('/add_image-group-update/', 'AjaxController@AddImageGroupUpdate');
Route::post('/home-posting-frequency/', 'AjaxController@homePostingFrequency')->name('homePostingFrequency');


Route::post('/hashtags-update/', 'AjaxController@HashtagsUpdate');
Route::post('/group-delete/', 'AjaxController@GroupDelete');
Route::post('/group-delete/selected', 'AjaxController@GroupDeleteIds');
Route::post('/group-name-update/', 'AjaxController@GroupNameUpdate');
Route::post('/delete-post-by-rsslink/', 'AjaxController@DeletePostByRsslink');
Route::post('/post-update/', 'AjaxController@PostUpdate');
Route::post('/post-delete/', 'AjaxController@PostDelete');
Route::post('/curation-refresh/', 'AjaxController@CurationRefresh');
Route::post('/drag-drop/', 'AjaxController@DragDrop');
Route::post('/social-account-id-group/', 'AjaxController@SocialAccountIdGroup');
Route::post('/account-active-inactive/', 'AjaxController@AccountActiveInactive');
Route::post('/re-activate/', 'AjaxController@ReActivateGroup');
Route::post('/timezone-form/', 'AjaxController@TimezoneForm');
Route::post('/send-idea/', 'AjaxController@SendIdea');
Route::post('/posts-sort/', 'AjaxController@PostsSort');
Route::get('/send-post/', 'SendPostController@index');

Route::post('/export-content', 'AjaxController@ExportContent');
Route::post('/csv-to-reupload', 'AjaxController@CsvToReupload');



Route::get('/import-from-buffer', function(){
    $user = \Bulkly\User::find(\Auth::id());
    foreach ($user->socialaccounts as $key => $socialaccount) {
        $client = new Client();
        try {
            $result = $client->request('GET', 'https://api.bufferapp.com/1/profiles/'.$socialaccount->account_id.'/updates/sent.json?count=100&access_token='.$socialaccount->buffer_token);
            $json = $result->getBody();
        } catch (ClientException $e) {
            $json = $e->getResponse()->getBody();
        } catch (RequestException $e) {
            $json = $e->getResponse()->getBody();
        }
        $posts = (isset(json_decode($json)->updates) ? json_decode($json)->updates : false);
        if($posts){
            $group_name = 'Buffer Import – '.ucwords(str_replace('google', 'google+', $socialaccount->type)).' – '.$socialaccount->name;
            $group = new SocialPostGroups;
            $group->name = $group_name;
            $group->user_id = $user->id;
            $group->type = 'upload';
            $group->status = 0;
            $group->target_acounts = serialize(array($socialaccount->id));
            $group->save();
            foreach ($posts as $key => $bpost) {
                if($key < 100){
                    $post = new SocialPosts;
                    $post->group_id = $group->id;
                    $post->text = $bpost->text;
                    $post->link = isset($bpost->media->link) ? $bpost->media->link : null;
                    $post->image = isset($bpost->media->picture) ? $bpost->media->picture : null;
                    $post->save();
                }
            }
        }
    }
});

Route::post('/update-card-info', function(Request $request){
    $input = $request->input();
    dd($input);
});
Route::post('/close-account/', function(Request $request){


    $input = $request->input();
    if(isset($input['type'])){
        $user = User::find($request->user_id);
        $id = $user->id;
        //\Cookie::queue('campaign_email', 'asdfghjk', 0);
        //dd(\Cookie::get('campaign_email'));
        \Cookie::queue(\Cookie::forget('campaign_email'));
        \Cookie::queue('campaign_email', uniqid().'@bulk.ly', 7*24*60);
        \Illuminate\Support\Facades\Auth::guard('web')->logout();

        $subsModel = new \Bulkly\Subscriptions();
        $subsModel->where('user_id', $id)->delete();

        $groupModel = new SocialPostGroups();
        $groupModel->where('user_id', $id)->delete();

        $bufferModel = new BufferPosting();
        $bufferModel->where('user_id', $id)->delete();

        $socialModel = new SocialAccounts();
        $socialModel->where('user_id', $id)->delete();

        $userModel = new User();
        $a = $userModel->where('id', $id)->delete();


        return redirect('login');
    }
    else {
        $user = User::find($request->user_id);
        $check = $user->subscription($request->user_plan)->cancelNow();

        try{
            $client = new Client;
            $result = $client->request('POST', 'https://api2.autopilothq.com/v1/contact', [
                'headers' => [
                    'autopilotapikey' => env('AUTOP'),
                    'Content-Type'     => 'application/json'
                ],
                'json' => [
                    'contact' => [
                        'Email' =>$user->email,
                        'custom' => [
                            'string--Cancelled--Account' => 'true',
                            'string--Subscription--Status' => 'close',
                        ],
                        '_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
                    ]
                ]
            ]);
        }
        catch (Exception $e) {
        }

        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        return redirect('login');
    }
});

Route::get('/admin', 'SuController@index')->name('admin');



Route::get('/admin/manage-user', 'SuController@manageUser')->name('admin/manage-user');
Route::get('/admin/manage-user/create', 'SuController@manageUserAdd')->name('admin/manage-user/create');
Route::post('/admin/manage-user/create', 'SuController@manageUserAddPost');
Route::get('/admin/manage-user/edit/{id}', 'SuController@manageUserEdit')->name('admin/manage-user/create/edit');
Route::post('/admin/manage-user/edit/{id}', 'SuController@manageUserEditPost');
Route::get('/admin/membership-plan', 'SuController@membershipPlan')->name('admin/membership-plan');
Route::get('/admin/membership-plan/add', 'SuController@membershipPlanAdd')->name('admin/membership-plan/add');
Route::post('/admin/membership-plan/add', 'SuController@membershipPlanAddPost');
Route::get('/admin/membership-plan/edit/{id}', 'SuController@membershipPlanEdit');
Route::post('/admin/membership-plan/edit/{id}', 'SuController@membershipPlanEditPost');
Route::get('/admin/membership-plan/delete/{id}', 'SuController@membershipPlanDelete');



Route::get('/admin/free-sign-up','SuController@freeSignUp')->name('admin/free-sign-up');
Route::get('/admin/free-sign-up/create','SuController@createFreeSignUp');
Route::get('/admin/free-sign-up/delete/{id}','SuController@deleteFreeSignUp');
Route::get('/admin/free-sign-up/renew/{id}','SuController@renewFreeAccount');




Route::post('/wait-date', function(Request $request){
    $group = \Bulkly\SocialPostGroups::find($request->group_id);
    $group->repeat_wait = $request->repeat_wait;
    $group->save();
    $start_time = strtotime($group->start_time);
    if( time() > $start_time ){
        $group->next_schedule_time= date('Y-m-d H:i:s', time());
    } else {
        $group->next_schedule_time= date('Y-m-d H:i:s', $start_time);
    }
    $group->save();
});

Route::get('/test', 'SendPostController@index');
Route::post('/post-sent-now/', 'AjaxController@sendPostNow');
Route::post('/rebrandly_key', function(Request $request){
    $user = User::find(\Auth::id());
    $user->rebrandly_key = isset($request->rebrandly_key) ? $request->rebrandly_key : null;
    $user->save();
});
Route::get('/repare-all', function(){
    $activeGroups_Che = SocialPostGroups::all();
    foreach ($activeGroups_Che as $key => $group) {
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
            $hour = 30 * 24;
        }
        $rawinterval = $hour * 60 * 60;
        $intervals = round($rawinterval / $frequency);
        $group->interval_seconds = $intervals;
        $group->next_schedule_time = date('Y-m-d H:i:s', time());
        $group->save();
    }
});

Route::post('/update-temp-user', function(Request  $request){
    if($request->email){
        $user = User::where('email', $request->email)->first();
        //dd(\Cookie::get('campaign_email'), $user);
        if($user !=null && ($user->email != \Cookie::get('campaign_email'))){
            return json_encode(array('status'=>'ok', 'message'=>'Provided email address already register', 'reload'=> 'no'));
        } else {
            $user = User::find($request->user_id);
            if($request->password){
                if(strlen($request->password) < 6 ) {
                    return json_encode(array('status'=>'ok', 'message'=>'Password characters lenght should at least 6', 'reload'=> 'no'));
                } else {
                    $user_meta = array(
                        'temp_user' => false,
                        'temp_subs' => true,
                    );
                    $user->email = $request->email;
                    $user->password = bcrypt($request->password);
                    $user->user_meta = serialize($user_meta);
                    $user->save();

                    \Cookie::queue(\Cookie::forget('campaign_email'));
                    \Cookie::queue('campaign_email', $user->email, 7*24*60);

                    try{
                        $client = new Client;
                        $result = $client->request('POST', 'https://api2.autopilothq.com/v1/contact', [
                            'headers' => [
                                'autopilotapikey' => env('AUTOP'),
                                'Content-Type'     => 'application/json'
                            ],
                            'json' => [
                                'contact' => [
                                    'FirstName' => $user->first_name,
                                    'LastName' =>$user->last_name,
                                    'Email' =>$user->email,
                                    'custom' => [
                                        'string--Campaign' => 'Bulkly - Buffer Import Campaign',
                                    ],
                                    '_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
                                ]
                            ] //
                        ]);
                    } catch (RequestException $e) {

                    } catch (ClientException $e) {

                    }


                    return json_encode(array('status'=>'ok', 'message'=>'User created', 'reload'=> 'yes' ));
                }

            } else {
                return json_encode(array('status'=>'ok', 'message'=>'Password required', 'reload'=> 'no'));
            }
        }

    } else {

        return json_encode(array('status'=>'ok', 'message'=>'Email required', 'reload'=> 'no'));
    }
});

Route::get('/campaign', function(){
    // get campaign Cookie
    $campaign_cookie = \Cookie::get('campaign');
    $campaign_email = \Cookie::get('campaign_email');
    if(!$campaign_cookie && !$campaign_email){
        // set campaign Cookie
        \Cookie::queue('campaign', 'true', 7*24*60);
        \Cookie::queue('campaign_email', time().'@bulk.ly', 7*24*60);
    }
    $campaign_cookie = \Cookie::get('campaign');
    $campaign_email = \Cookie::get('campaign_email');
    $redirect_ur = "https://bufferapp.com/oauth2/authorize?client_id=".env('BUFFER_CLIENT_ID')."&redirect_uri=".env('BUFFER_REDIRECT')."&response_type=code";
    return redirect(url($redirect_ur));
});

Route::get('/auth/logout', function(Request $request){
    Illuminate\Support\Facades\Auth::logout();
    return redirect(url('/login'));
})->name('auth.logout');

Route::post('/auth/login', 'Auth\LoginController@loginNow')->name('auth.login');


Route::get('/rebrandly-test', function(){

    $user = \Bulkly\User::find(\Auth::id());


    try{



        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $user->rebrandly_key
            ]
        ]);

        $result = $client->post('https://api.rebrandly.com/v1/links',
            ['body' => json_encode(
                [
                    'destination' => 'https://bitbucket.org/coredeveloper2013/overlog',
                    'domain' => [
                        'id' => $user->rebrandly_domain,
                    ]
                ]
            )]
        );



        dump(json_decode($result->getBody()));

    } catch ( ClientException $e) {

        dump($e->getResponse()->getBody()->getContents());

    } catch (RequestException $e) {

        dump($e->getResponse()->getBody()->getContents());

    }



});

Route::get('/sendPostTest', 'CronController@sendPostTest');

Route::get('/app/bulk.ly/free/{code}','Auth\RegisterController@validUserRegistrationForm')->name('bulk.free-signup');
Route::post('/app/bulk.ly/free/signUp/{code}','Auth\RegisterController@validUserRegistration');


Route::resource('bufferPosts', 'BufferController');

