<?php

namespace App\Http\Controllers;
use \Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use App\Traits\CustomValidationResponse;

class OrganisationController extends Controller
{

    use CustomValidationResponse;

    public function index($orgId){

        try{
             $org = Organisation::where('orgId',$orgId)->first();

            return response()->json(['success' => 'success', 'message' => 'Organisation details retrieved successfully', 'data' => $org], 200);

        }catch(\Exception $e){

            return response()->json(['success' => 'error', 'message' => 'Could not get organisation' . $e->getMessage()], 500);

        }

    }


    public function store(Request $request){


        $this->validateWithCustomResponse($request, [

            'name' => 'required|string',
            'description' => 'nullable|string',

        ]);


        DB::beginTransaction();

        try {

            $org = Organisation::create(['name' => $request->name,'description' => $request->description]);

            DB::commit();
            return response()->json(['status' => 'success','message' => 'Organisation created successfully','data' => $org], 201);

        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'something went wrong'], 500);
        }
    }


    public function addUser(Request $request,$orgId){

       
        $this->validateWithCustomResponse($request, [

            'userId' => 'required|string',

        ]);

        DB::beginTransaction();

        try {

            $user = User::where('userId',$request->userId)->first();

            $org = Organisation::where('orgId',$orgId)->first();

            if($user && $org ){

                $orgUser = OrganisationUser::create(['orgId' => $orgId,'userId' => $request->userId]);

            }
            else{

                return response()->json(['status' => 'Bad request', 'message' => 'Client error'], 400);
            }

            DB::commit();
            return response()->json(['status' => 'success','message' => 'user added to Organisation successfully'], 201);

        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'something went wrong'. $e->getMessage()], 500);
        }
    }

}
