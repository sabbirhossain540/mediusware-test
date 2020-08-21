<?php

namespace Bulkly;

use Illuminate\Database\Eloquent\Model;

class SocialPosts extends Model
{
   public function group()
    {
          return $this->belongsTo('Bulkly\SocialPostGroups');
    }

   public function rssautopost()
    {
        return $this->hasMany('Bulkly\RssAutoPost', 'post_id');
    }

}
