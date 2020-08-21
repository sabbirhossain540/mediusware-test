<?php

namespace Bulkly\Http\Controllers\Auth;

use Bulkly\User;
use Bulkly\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Mail;
use Bulkly\FreeSignUp;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $code=null)
    {
        if($code == null){
            return Validator::make($data, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6',
                'tos' => 'required',
            ]);
        } else {
            return Validator::make($data, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6',
                'token' => 'required|min:6|exists:free_sign_ups,token_key',
                'tos' => 'required',
            ]);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data, $request)
    {
        return User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'bfriday' => isset($data['bfriday']) ? $data['bfriday'] : 0,
            'ip' => $request->ip(),
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function register(Request  $request)
    {
        $input = $request->all();
        if(isset($input['first_name']) && isset($input['last_name'])){
            $cFnm = substr($input['first_name'], -2);
            $cLnm = substr($input['last_name'], -2);
            $cLf = substr($input['last_name'], 0, 1);
            
            //if(ctype_upper($cFnm)){ return redirect(route('register'))->withInput($request->input()); }
            if(ctype_upper($cLnm)){ return redirect(route('register'))->withInput($request->input()); }
        }
        $validator = $this->validator($input);



        if($validator->passes()){

            $data = $this->create($input, $request)->toArray();


            $data['verification_token'] = str_random(25);

            $user = User::find($data['id']);
            $user->verification_token = $data['verification_token'];
            $user->save();
            

            Mail::send('mails.confirmation', $data, function($message) use ($data){
                $message->to($data['email']);
                $message->subject(ucwords($data['first_name']).', let\'s confirm your Bulkly account');
            });

            
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
                                ],
                            '_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
                        ]
                    ]
                ]);
            } catch (RequestException $e) {
                
            } catch (ClientException $e) {
                
            }
            

            return redirect(route('login'))->with('status', 'A confirmation email has been sent. Please check your inbox.');
        }


        return redirect(route('register'))->with('errors', $validator->messages())->withInput($request->input());
    }



    public function UpdateTempUser(Request  $request)
    {
        print_r($request->all());

    }



    public function validUserRegistrationForm($code)
    {
        $urlData = FreeSignUp::where('code', $code)->first();
        if (!empty($urlData)) {
             return view('auth.validUserSignUp', compact('urlData'));
        }
        else{
            return redirect('/login');
        }
       
    }



    public function validUserRegistration(Request  $request, $code)
    {

        $input = $request->all();

        $validator = $this->validator($input, $code);
        if(!$validator->passes()){
            return redirect(route('bulk.free-signup', ['code'=>$code]))->with('errors', $validator->messages())->withInput($request->input());
        }



        $url_check = FreeSignUp::where('code', $code)->where('token_key', $request->token)->first();
        // dd($url_check);


        if (!empty($url_check) ) {
            $verification_token = str_random(25);

            $url_check->status = 0;
            $url_check->save();

          $validUser = new User;
          $validUser->name = $request->first_name.' '.$request->last_name ;
          $validUser->first_name = $request->first_name;          
          $validUser->last_name  = $request->last_name;
          $validUser->email      = $request->email;
          $validUser->verification_token = $verification_token;
          $validUser->password   = Hash::make($request->password);
          $validUser->save();


            $data['name'] = $validUser->name;
            $data['email'] = $validUser->email;
            $data['verification_token'] = $verification_token;


            Mail::send('mails.confirmation',  $data, function($message) use ($data){
                $message->to($data['email']);
                $message->subject('Registration Confirmation');
            });

          
          return view('pages.successPage');
        }
        else{
           
            return redirect()->back()->with('message','Please Provide The Corrrect Token Key !');
        }




    }
}
