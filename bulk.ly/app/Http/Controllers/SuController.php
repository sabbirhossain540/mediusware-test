<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Http\Request;

use Bulkly\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Bulkly\Plan;
use Alert;



use Bulkly\FreeSignUp;


class SuController extends Controller
{

       public function __construct()
    {
        $this->middleware('auth');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function index()
    {   
        $subscriptions = \DB::table('subscriptions')->select(\DB::raw('count(*) as count, name'))->groupBy('name')->get();
        return view('admin.index')->with('users', User::all())->with('subscriptions', $subscriptions);
    }









    public function manageUser()
    {   
        return view('admin.users')->with('users', User::all());
    }

    public function manageUserAdd()
    {   
        return view('admin.user-create')->with('users', User::all())->with('plans_m', Plan::where('type', 'Month')->orderBy('price')->get())->with('plans_y', Plan::where('type', 'Year')->orderBy('price')->get());
    }


    public function manageUserAddPost(Request $request)
    { 

        $input = $request->all();
        $validator = $this->validator($input);
        if($validator->passes()){
            $data = $this->create($input)->toArray();
            $user = User::find($data['id']);
            $user->varifide = 0;
            
            $data['verification_token'] = str_random(25);
            $user->verification_token = $data['verification_token'];
            
            $user->trial_ends_at = \Carbon\Carbon::now()->addDays($request->date);
            $user->save();

            
            
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
                                'string--Confirmation--Url' => route('confirmation', $user->verification_token),
                                'string--Subscription--Plan' => 'freeplan',
                                ],
                            '_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
                        ]
                    ]
                ]);
                
            } catch (RequestException $e) {
                
            } catch (ClientException $e) {
                
            }

            // $asdfg = Mail::send('mails.confirmation', ['verification_token' => $data['verification_token']], function ($message) use ($user) {
            //     $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            //     $message->to($user->email);
            // });

            // dd($asdfg);



            return redirect(route('admin/manage-user'));
        } else {
            return redirect(route('admin/manage-user/create'))->with('errors', $validator->messages())->withInput($request->input());
        }
    }

    public function manageUserEdit($id)
    {         
        return view('admin.user-edit')->with('user', User::find($id));
    }

    public function manageUserEditPost(Request $request)
    {  
        $user = User::find($request->id);
        $input = $request->all();
        $validator = $this->validator($input);
        if($validator->passes()){
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();
        } else {
            return redirect(route('admin/manage-user/create/edit', $user->id))->with('errors', $validator->messages())->withInput($request->input());
        }
        return view('admin.user-edit')->with('user', User::find($request->id));
    }

    public function membershipPlan()
    {   
        return view('admin.membership')->with('users', User::all())->with('plans_m', Plan::where('type', 'Month')->orderBy('price')->get())->with('plans_y', Plan::where('type', 'Year')->orderBy('price')->get());
    }

    public function membershipPlanAdd()
    {   
        return view('admin.membership-add')->with('users', User::all());
    }

    public function membershipPlanEdit($id)
    { 
        $plan = Plan::find($id);
        return view('admin.membership-edit')->with('plan', $plan);
    }

    public function membershipPlanEditPost(Request $request)
    { 
        $plan = Plan::find($request->id);

        $plan->name = $request->name;
        $plan->type = $request->type;
        $plan->price = $request->price;
        $plan->ppm = $request->ppm;
        $plan->connucted_buf_account = $request->connucted_buf_account;
        $plan->save_content_upload_post = $request->save_content_upload_post;
        $plan->save_content_upload_group = $request->save_content_upload_group;
        $plan->save_content_curation_feeds = $request->save_content_curation_feeds;
        $plan->save_content_curation_group = $request->save_content_curation_group;
        $plan->save_rss_auto_feeds = $request->save_rss_auto_feeds;
        $plan->save_rss_auto_group = $request->save_rss_auto_group;
        $plan->save();

        return view('admin.membership-edit')->with('plan', $plan);
    }

    public function membershipPlanDelete($id)
    { 
        $plan = Plan::find($id);
        $plan->delete();
        return redirect('admin/membership-plan');
    }

    public function membershipPlanAddPost(Request $request)
    {   
        $plan = new Plan;
        $plan->name = $request->name;
        $plan->slug = str_replace(' ', '', strtolower($request->name.$request->type));
        $plan->type = $request->type;
        $plan->price = $request->price;
        $plan->ppm = $request->ppm;
        $plan->connucted_buf_account = $request->connucted_buf_account;
        $plan->save_content_upload_post = $request->save_content_upload_post;
        $plan->save_content_upload_group = $request->save_content_upload_group;
        $plan->save_content_curation_feeds = $request->save_content_curation_feeds;
        $plan->save_content_curation_group = $request->save_content_curation_group;
        $plan->save_rss_auto_feeds = $request->save_rss_auto_feeds;
        $plan->save_rss_auto_group = $request->save_rss_auto_group;
        $plan->save();
        return redirect('admin/membership-plan');
    }



    public function freeSignUp()
    {   
        $showAccounts = FreeSignUp::get();
        return view('admin.free-sign-up')->with(['showAccounts'=>$showAccounts]);
    }


    public function createFreeSignUp()
    {
        $createFreeSignUp = new FreeSignUp; 

        $code = str_random(20);

        $createFreeSignUp->url = "app/bulk.ly/free/".$code;
        $createFreeSignUp->code = $code;
        $createFreeSignUp->token_key = rand(100000 , 600000);
        $createFreeSignUp->trial_ends_at = '5000' . 'days';
        // $createFreeSignUp->trial_ends_at = \Carbon\Carbon::now()->subDay(1);
        $createFreeSignUp->save();

//        Alert::success('Success Message', 'Successfully Created');

        // dd($createFreeSignUp);  

        return redirect('/admin/free-sign-up')->with('message','User Create Successfully..!!');
        }


        public function deleteFreeSignUp($id)
        {
            $deleteUrl = FreeSignUp::find($id)
                        ->delete();

            return redirect('/admin/free-sign-up');
        }

        public function renewFreeAccount($id)
        {
            $renewAccount = FreeSignUp::find($id);          
            $renewAccount->status = 1;
            $renewAccount->save();


         

             return redirect('/admin/free-sign-up');
        }

}
