<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Str;
use App\Models\User;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use Auth;
use App\Traits\CustomValidationResponse;

class AuthController extends Controller
{


    use CustomValidationResponse;


    public function signUp(Request $request){


        $this->validateWithCustomResponse($request, [

            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phone' => 'nullable|string',
            'email' =>'required|email',
            'password' => 'required|string'

        ]);

        DB::beginTransaction();

        try {

            $user = User::whereEmail($request->email)->first();

            if ($user){

                DB::rollback();
                return response()->json(['status' => 'Bad request', 'message' => 'Registration unsuccessful'], 400);

            }else{

                $user= User::create([

                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->password),

                ]);

                if($user){

                    $org = Organisation::create(['name' => $request->firstName.'s'.'Organisation']);

                    OrganisationUser::create(['orgId' => $org->orgId,'userId' => $user->userId]);

                }

                DB::commit();

                if (! $token = auth()->attempt(request(['email','password']))) {
                    return response()->json(['status' => 'Bad request', 'message' => 'Authentication failed'], 401);
                }

                $data = ['accessToken' => $token, 'user' => $user];

                return response()->json(['status' => 'success','message' => 'Registration successful','data' => $data], 201);

            }

        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'something went wrong'.$e->getMessage()], 500);
        }
    }


    public function signIn(Request $request){


        $this->validateWithCustomResponse($request, [

             'email' => 'required|email',
            'password' =>'required|string'

        ]);


        try {


            if (! $token = auth()->attempt(request(['email','password']))) {

                return response()->json(['status' => 'Bad request', 'message' => 'Authentication failed'], 401);
            }

            $data = ['accessToken' => $token, 'user' => auth()->user()];


            return response()->json(['status' => 'success', 'message' => 'Login successful','data' => $data], 200);

        } catch (\Exception $e) {

            return response()->json(['status' => 'error', 'message' => 'something went wrong. ' . $e->getMessage()], 500);

        }

    }



}
