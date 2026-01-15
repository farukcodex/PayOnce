<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OfferNotification;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function send(Request $request){

        if(Auth()->user()->role != 'admin'){
            return apiError('Only authorised person can sent notification');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'users' => 'required'
        ]);

        $users = User::whereIn('id',$request->users)->get();

        foreach($users as $user){
            $user->notify(new OfferNotification($request->title,$request->message));
        }
        return apiSuccess('Notifications send');

    }
}
