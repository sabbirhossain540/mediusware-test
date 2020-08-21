<?php

namespace Bulkly;

use Illuminate\Database\Eloquent\Model;

class SocialPostGroups extends Model
{
    //
    protected $fillable = ['name', 'type'];


   public function posts()
    {
        return $this->hasMany('Bulkly\SocialPosts', 'group_id');
    }


   public function rssautopost()
    {
        return $this->hasManyThrough('Bulkly\RssAutoPost', 'Bulkly\SocialPosts', 'group_id', 'post_id');
    }
    
    
   public function targertservices()
    {	
    	$group = $this->find($this->id);
    	$target_accounts = unserialize($group->target_acounts);
    	$accountsArray = array();
    	foreach ($target_accounts as $key => $target_account) {

    		
        if(SocialAccounts::find($target_account)){
          $account = SocialAccounts::find($target_account)->type;
          array_push($accountsArray, $account);
        }


    		
    	}
    	return (object) array_unique($accountsArray);
    }

   public function user()
    {
       return $this->belongsTo('Bulkly\User');

    }
    
   public function sent()
    {
       return $this->hasMany('Bulkly\BufferPosting', 'group_id');

    }


    public function socialGroup()
    {
        return $this->hasOne(BufferPosting::Class, 'user_id', 'id');
    }
 


}
