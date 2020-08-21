<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Bulkly\User;

use Bulkly\SocialPostGroups;

use Bulkly\SocialPosts;


class ContentUploadController extends Controller
{
    //
    public function index(){
        if(!Auth::guard('web')->check()){
            return redirect('/login');
        }
		$user = User::find(Auth::id());
    	return view('group.index')
        ->with('user', $user)
        ->with('type', 'upload');
    }
    //
    public function pendingGroup($id){
        if(!Auth::guard('web')->check()){
            return redirect('/login');
        }
		$group = SocialPostGroups::find($id);
		$user = User::find(Auth::id());
        if($group == null){
            return redirect('/content-upload');
        } else {
            return view('group.single')->with('user', $user)->with('group', $group);
        }
    }
    //
    public function activeGroup($id){
        if(!Auth::guard('web')->check()){
            return redirect('/login');
        }
        $group = SocialPostGroups::find($id);
        $user = User::find(Auth::id());
        if($group == null){
            return redirect('/content-upload');
        } else {
            return view('group.single')->with('user', $user)->with('group', $group);
        }

    }

    public function completedGroup($id){
        if(!Auth::guard('web')->check()){
            return redirect('/login');
        }
        $group = SocialPostGroups::find($id);
        $user = User::find(Auth::id());
        if($group == null){
            return redirect('/content-upload');
        } else {
            return view('group.single')->with('user', $user)->with('group', $group);
        }
    }

}
