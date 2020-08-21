<?php

namespace Bulkly;

use Illuminate\Database\Eloquent\Model;

class RssAutoPost extends Model
{
   public function post()
    {
       return $this->belongsTo('Bulkly\SocialPosts');

    }
}
