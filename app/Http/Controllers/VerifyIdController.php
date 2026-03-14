<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class VerifyIdController extends Controller
{
    public function verify(Request $request)
    {
        $id = trim($request->input('national_id', ''));

        if (!$id)
            return response()->json(['valid' => false, 'error' => 'Please enter your National ID number.']);
        if (!preg_match('/^\d+$/', $id))
            return response()->json(['valid' => false, 'error' => 'National ID must contain digits only.']);
        if (strlen($id) < 6)
            return response()->json(['valid' => false, 'error' => 'National ID is too short — minimum 6 digits.']);
        if (strlen($id) > 10)
            return response()->json(['valid' => false, 'error' => 'National ID is too long — maximum 10 digits.']);
        if (preg_match('/^0+$/', $id))
            return response()->json(['valid' => false, 'error' => 'Please enter a valid National ID number.']);

        return response()->json(['valid' => true, 'name' => 'Verified']);
    }
}