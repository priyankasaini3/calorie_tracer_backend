<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use Mail;
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Support\Facades\DB;
class UserController extends Controller 
{
public $successStatus = 200;
/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
            'gender' => 'required', 
            'height' => 'required', 
            'weight' => 'required', 
            'age' => 'required', 
        ]);
if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
$input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
return response()->json(['success'=>$success], $this-> successStatus); 
    }
/** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 

    public function forgotPassword(){ 
		if(request('email') != ""){
			//first check matchable result 
			$user = DB::table('users')->select('id','name','email')
					->where('email', '=', request('email'))
					->first();
			#echo "<pre>user=";print_r($user);die;
			if (!$user) {
				return response()->json([
					"isSuccess" => "false",
					"data" => "{}",
					"errorCode" => "9001",
					"errorMessage" => "This email is not registered with us",
					"cause" => "This email is not registered with us"
				], 200);
			} 
			else {
				
					$forgot_pwd_token =  mt_rand(100000,999999);
			
					DB::table('users')
					->where('id',$user->id)
					->update(['forgot_pwd_token' => $forgot_pwd_token]);
					
					if($user->name != ""){
						$firstNameStr = $user->name;
					} else {
						$firstNameStr = $user->email;
					}
					$otp_token = "OTP -".$forgot_pwd_token;
					$data = array(
							'mail_title'=>"Forgot Password",
							'name' =>$firstNameStr,
							'mail_body_text'=>"Please use this OTP code to reset your account password on application.",
							'otp_token'=>$otp_token
						);
					Mail::send('mail', $data, function($message) use($user) {
						$message->to($user->email, 'Forgot Password')->subject('Social Study Forgot Password');
						$message->from('sainipriyanka1983@gmail.com','Support Team');
					});
					$user->is_account_verified = 1; //updated as per priyanka sugested
					return response()->json([
						"isSuccess" => "true",
						"data" => $user,
						"errorCode" => "0",
						"errorMessage" => "",
						"cause" => ""
					], 200);
				
			}
		} 
		else {
			if(request('email') == ""){
				return response()->json([
					"isSuccess" => "false",
					"data" => "{}",
					"errorCode" => "9001",
					"errorMessage" => "Email field is required",
					"cause" => "Email field is required"
				], 401);
			}
		}
	}
	
	//For resetPassword
    public function resetPassword(){ 
		if(request('email') != "" && request('newpwd') != "" && request('forgot_pwd_token') != ""){
			//first check matchable result 
			$user = DB::table('users')->select('id','email','password')
			->where('email', '=', request('email'))
			->where('forgot_pwd_token', '=', request('forgot_pwd_token'))
			->first();
			#echo "<pre>user=";print_r($user);#die;
			if (!$user) {
				return response()->json([
					"isSuccess" => "false",
					"data" => "{}",
					"errorCode" => "9001",
					"errorMessage" => "Your OTP is not matched",
					"cause" => "Your OTP is not matched"
				], 200);
			} else {
			
						//update old pwd
						DB::table('users')
						->where('id',$user->id)
						->update(['password' => bcrypt(request('newpwd'))]);
				
					return response()->json([
						"isSuccess" => "true",
						"data" => $user,
						"errorCode" => "0",
						"errorMessage" => "",
						"cause" => ""
					], 200);
				
			}
		} 
		else {
			if(request('email') == "" && request('newpwd') == "" && request('forgot_pwd_token') == ""){
				return response()->json([
					"isSuccess" => "false",
					"data" => "{}",
					"errorCode" => "9001",
					"errorMessage" => "Email or Password and Token fields are required",
					"cause" => "Email or Password and Token fields are required"
				], 401);
			} else {
				if(request('email') == ""){
					return response()->json([
						"isSuccess" => "false",
						"data" => "{}",
						"errorCode" => "9001",
						"errorMessage" => "Email field is required",
						"cause" => "Email field is required"
					], 401);
				}
				if(request('newpwd') == ""){
					return response()->json([
						"isSuccess" => "false",
						"data" => "{}",
						"errorCode" => "9001",
						"errorMessage" => "Password field is required",
						"cause" => "Password field is required"
					], 401);
				}
				if(request('forgot_pwd_token') == ""){
					return response()->json([
						"isSuccess" => "false",
						"data" => "{}",
						"errorCode" => "9001",
						"errorMessage" => "Token field is required",
						"cause" => "Token field is required"
					], 401);
				}
			}
		}
	}
	


}