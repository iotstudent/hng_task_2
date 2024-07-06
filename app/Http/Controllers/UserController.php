<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OrganisationUser;
use App\Models\Organisation;
use App\Traits\CustomValidationResponse;

class UserController extends Controller
{

    use CustomValidationResponse;

    public function index($id)
    {
        try {

            $loggedInUser = auth()->user();

            if ($loggedInUser->userId == $id) {
                return response()->json(['success' => 'success', 'message' => 'User profile retrieved successfully', 'data' => $loggedInUser], 200);
            }

            $loggedInUserOrgIds = OrganisationUser::where('userId', $loggedInUser->userId)->pluck('orgId');
            $userToCheckOrgIds = OrganisationUser::where('userId', $id)->pluck('orgId');

            $commonOrgIds = $loggedInUserOrgIds->intersect($userToCheckOrgIds);

            if ($commonOrgIds->isNotEmpty()) {
                $user = User::find($id);
                if ($user) {
                    return response()->json(['success' => 'success', 'message' => 'User profile retrieved successfully', 'data' => $user], 200);
                } else {
                    return response()->json(['status' => 'Bad request', 'message' => 'Client error'], 400);
                }
            } else {
                return response()->json(['status' => 'Bad request', 'message' => 'Client error'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['succ' => 'error', 'message' => 'Could not get profile: ' . $e->getMessage()], 500);
        }
    }


    public function getUserOrganizations()
    {
        try {

            $authUserId = auth()->user()->userId;

            $orgIds = OrganisationUser::where('userId', $authUserId)->pluck('orgId');

            if ($orgIds->isEmpty()) {

                return response()->json(['success' => true, 'message' => 'User is not associated with any organizations', 'data' => []], 200);
            }

            $organizations = Organisation::whereIn('orgId', $orgIds)->get();

            return response()->json(['success' => true, 'message' => 'Organizations retrieved successfully', 'data' => ['organisations' =>$organizations] ], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not retrieve organizations: ' . $e->getMessage()], 500);
        }
    }
    
}
