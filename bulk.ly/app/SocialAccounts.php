<?php

namespace Bulkly;

use Illuminate\Database\Eloquent\Model;

class SocialAccounts extends Model
{
		//
	 public function groups($id)
		{	
			$groups = SocialPostGroups::where('user_id', \Auth::id())->get();

			$groupsArray = array();
			foreach ($groups as $key => $group) {
				$socialaccount = unserialize($group->target_acounts);
				if(in_array($id, $socialaccount)){
					array_push($groupsArray, $group);
				}
			}
		return $groups;
		}
	 public function groupsact($id)
		{ 
			$groups = SocialPostGroups::all();
			$groupsArray = array();
			foreach ($groups as $key => $group) {
				$socialaccount = unserialize($group->target_acounts);
				if(in_array($id, $socialaccount)){
					array_push($groupsArray, $group);
				}
			}
		return $groupsArray;
		}
	 public function user()
		{   
			 return $this->belongsTo('Bulkly\User');
		}
}
