<?php

namespace Bulkly\Console\Commands;

use Illuminate\Console\Command;

use Bulkly\SocialPosts;
use Bulkly\RssAutoPost;
use Bulkly\SocialPostGroups;
use Bulkly\User;
class RssAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $teusers = User::all();
        foreach ($teusers as $key => $user) {
            $user_meta = unserialize($user->user_meta);
            if(isset($user_meta['temp_subs'])) {
                if($user_meta['temp_subs']===true){
                    $cdate = new \Carbon\Carbon;
                    $cdate->subWeek();
                    $spc = SocialPostGroups::where('user_id', $user->id)->where('created_at', '<', $cdate->toDateTimeString() )->get();

                    if($spc->count() > 0){
                        foreach ($spc as $key => $spcs) {
                            print_r($spcs->id);
                            // $spcs->delete();
                        }
                    }
                }
            }
        }









  $allrss = SocialPosts::whereNotNull('rsslink')->get();
  foreach ($allrss as $key => $post) {
    
      if($post->group->status == 1){

 print_r("
 Group ID: ".$post->group->id);
 print_r("
");
 print_r(
$post->rsslink);
 print_r("
");


    if($post->rsslink){
    try {
        $rssurl = $post->rsslink;
        $sslerr=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  
        $rssurl = file_get_contents($rssurl, false, stream_context_create($sslerr));
        $rssurl = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '', $rssurl);
        
        $feed = new \DOMDocument();
        $feed->loadXML($rssurl);
        
        
        
        $posts = array();
        $getItem = $feed->getElementsByTagName('channel')->item(0);
        if( $getItem != null )
        {
            $items = $getItem->getElementsByTagName('item');
        } 
        else 
        {
            $items = $feed->getElementsByTagName('entry');
        }
        
        foreach($items as $key => $item) {
            
            if($item->getElementsByTagName('title')){
                if($item->getElementsByTagName('title')->item(0)){
                    if($item->getElementsByTagName('title')->item(0)->firstChild){
                        if($item->getElementsByTagName('title')->item(0)->firstChild->nodeValue){
                            $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
                        } else {
                            $title = '';
                        }
                    } else {
                        $title = '';
                    }
                } else {
                    $title = '';
                }
            } else {
                $title = '';
            }
            
            
            if($item->getElementsByTagName('link')){
                if($item->getElementsByTagName('link')->item(0)){
                    $getLink = $item->getElementsByTagName('link')->item(0);
                } else{
                    $getLink = '';
                }
                
            } else {
                $getLink = '';
            }
            
            
            
            if( $getLink->firstChild != null )
            {
                $validator = filter_var($getLink->firstChild->nodeValue, FILTER_VALIDATE_URL);
                if ($validator == true)
                {
                    $link = $getLink->firstChild->nodeValue;
                }
                else 
                {
                    $link = $getLink->getAttribute('href');
                }
            } 
            else 
            {
                $link = $getLink->getAttribute('href');
            }
            if($item->getElementsByTagName('pubDate')){
    			if($item->getElementsByTagName('pubDate')->item(0)){
    				if($item->getElementsByTagName('pubDate')->item(0)->firstChild){
    					if($item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue){
    						$pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
    					} else {
    						$pubDate = '';
    					}
    				} else {
    					$pubDate = '';
    				}
    			} else {
    				$pubDate = '';
    			}
    		} else {
    			$pubDate = '';
    		}



		    $posts[$key]['pubDate'] = date('Y-m-d H:i:s', strtotime($pubDate));
            $posts[$key]['title'] = $title;
            $posts[$key]['link'] = $link; 
        }
        
        $allposts = array_reverse($posts);



        foreach ($allposts as $key => $allpost) {
            $checkpost = RssAutoPost::where('post_id', $post->id)->where('text', $allpost['title'])->where('link', $allpost['link'])->first();
            if($checkpost != null && $allpost['link'] != '' && $allpost['link'] != null){
                $checkpost->text = $allpost['title'];
                $checkpost->link = $allpost['link'];
                $checkpost->pub_date = $allpost['pubDate'];
                $checkpost->save();
                print_r('Date '.$allpost['pubDate']. PHP_EOL);
                print_r('Updated'. PHP_EOL);
            } else if($checkpost == null && $allpost['link'] != '' && $allpost['link'] != null) {
                $rssautopost = new RssAutoPost;
                $rssautopost->post_id = $post->id;
                $rssautopost->text = $allpost['title'];
                $rssautopost->link = $allpost['link'];
                $rssautopost->pub_date = $allpost['pubDate'];
                $rssautopost->save();
                print_r('Date '.$allpost['pubDate']. PHP_EOL);
                print_r('New Added'. PHP_EOL);
            }
        }

    }
    catch (\Exception $e) {
        
    }

}







      }
  }




















       
    }
}
