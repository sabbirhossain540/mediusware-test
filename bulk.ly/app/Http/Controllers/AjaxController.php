<?php

namespace Bulkly\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Bulkly\User;
use Bulkly\RssAutoPost;
use Bulkly\SocialPostGroups;
use Bulkly\SocialPosts;
use Bulkly\SocialAccounts;
use Bulkly\BufferPosting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class AjaxController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',

        ]);
    }

    public function updateUser(Request $request)
    {
        $user = User::find(Auth::id());
        $input['first_name'] = $request->first_name;
        $input['last_name'] = $request->last_name;
        $input['name'] = $request->first_name.' '.$request->last_name;
        $input['email'] = $request->email;
        // print_r($request->all());
        $validator = $this->validator($input);
        if($validator->passes()){
            $user->name =$input['first_name'].' '.$input['last_name'];;
            $user->first_name = $input['first_name'];
            $user->last_name = $input['last_name'];
            $user->email = $input['email'];

            if($request->password){
                $user->password = bcrypt($request->password);
            }

            if($user->save()){
                return '1';
            }
        }




        return $validator->messages();
    }

    public function homePostingFrequency(Request $request){
        if(Auth::check()){

            try{

                $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
                $frequency = array();
                foreach ($months as $key => $month) {
                    $frequency[] = BufferPosting::where('user_id', Auth::id())->whereMonth('sent_at', '=', $month)->whereYear('sent_at', '=', date('Y'))->get()->count();
                }
                return response()->json(['status' => 2000, 'data' => $frequency]);

            } catch (\Exception $e){
                return response()->json(['status' => 5000, 'msg' => $e->getMessage()]);
            }

        } else {
            return response()->json(['status' => 1000, 'msg' => 'Unauthorized Request']);
        }
    }






    public function CsvToReupload(Request $request)
    {



        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        if( mb_detect_encoding(file_get_contents($file), 'UTF-8, ISO-8859-2', true) == 'ISO-8859-2'){
            $fileO = $this->utf8_fopen_read($file);
        } else {
            $fileO = fopen($file, 'r');
        }
        $column=fgetcsv($fileO);
        while(!feof($fileO)){
            $rows[]=fgetcsv($fileO);
        }



        foreach ($rows as $key => $row) {
            if($row[0] && $row[1]){
                $SocialPosts = SocialPosts::find($row[1]);
                $SocialPosts->text = $row[2];
                $SocialPosts->link = $row[3];
                $SocialPosts->image = $row[4];
                $SocialPosts->save();

            } elseif($row[0] && !$row[1]) {
                $SocialPosts = new SocialPosts;
                $SocialPosts->group_id = $row[0];
                $SocialPosts->text = isset($row[2]) ? htmlspecialchars($row[2]) : null;
                $SocialPosts->link = isset($row[3]) ? $row[3] : null;
                $SocialPosts->image = isset($row[4]) ? $row[4] : null;
                $SocialPosts->save();
            } elseif($row[0] && $row[1] && !$row[2] && !$row[3] && !$row[4]) {
                $SocialPosts = SocialPosts::find($row[1]);
                $SocialPosts->delete();
            } else {

            }
        }




    }



    public function ExportContent(Request $request)
    {
        $group = SocialPostGroups::find($request->id);
        $posts = $group->posts;


        $results = array();

        foreach ($posts as $key => $post) {

            array_push($results, array(
                'group_id' => $group->id,
                'id' => $post->id,
                'text' => $post->text,
                'link' => $post->link,
                'image' => $post->image,
            ));
        }


        $fileName = str_replace(' ', '-', $group->name).'.csv';

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        $headerDisplayed = false;

        foreach ( $results as $data ) {
            // Add a header row if it hasn't been added yet
            if ( !$headerDisplayed ) {
                // Use the keys from $data as the titles
                fputcsv($fh, array_keys($data));
                $headerDisplayed = true;
            }

            // Put the data into the stream
            fputcsv($fh, $data);
        }
        // Close the file
        fclose($fh);
        // Make sure nothing else is sent, our file is done
        exit;
    }










    public function upgradePlan(Request $request)
    {
        $user = User::find(Auth::id());
        $change = $user->subscription($user->subscriptions[0]->name)->swap(explode('|', $request->input('id'))[0]);
        if($change){
            $subscriptions = \DB::table('subscriptions')->where('user_id', Auth::id())->update(['name' => explode('|', $request->input('id'))[1]]);
            return '1';
        } else {
            return '0';
        }
    }


    public function group_id($name, $type, $fileLink, $filelinkname, $link, $hashtags){
        if( $name == 'Untitled'){
            $group = false;
        } else {
            $group = SocialPostGroups::where('user_id', Auth::id())->where('name', $name)->where('type', $type)->first();
        }
        if($group){
            if($fileLink=='csv'){
                $csv = 1;
            } else {
                $csv = 0;
            }
            $file_link_old = unserialize($group->files_links);
            if(isset($filelinkname)){
                $files =  $file_link_old['file'];
                if(!in_array($filelinkname, $files)){
                    array_push($files, $filelinkname);
                }
            } else {
                $files = array();
            }
            if(isset($link)){
                $links =  $file_link_old['link'];
                if(!in_array($link, $links)){
                    array_push($links, $link);
                }
            } else {
                $links = array();
            }
            $file_link = array(
                'csv' => $csv,
                'file' => $files,
                'link' => $links
            );
            $group->files_links = serialize($file_link);
            $group->save();
            return $group->id;
        } else {
            $user = User::find(Auth::id());



            $group = new SocialPostGroups;

            if($fileLink=='csv'){
                $csv = 1;
            } else {
                $csv = 0;
            }
            $file_link = array(
                'csv' => $csv,
                'file' => array($filelinkname),
                'link' => array($link)
            );
            $start_time = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone(\Auth::user()->timezone));
            $group->start_time = $start_time;
            $group->frequency = 1;
            $group->interval = 'daily';
            $interval = $this->interval($group->interval, $group->frequency);
            $group->interval_seconds = $interval;
            $group->next_schedule_time = $group->start_time;
            $group->user_id = $user->id;
            $group->name = $name;
            $group->type = $type;
            $group->files_links = serialize($file_link);
            $group->target_acounts = serialize(array());
            $group->hash = serialize($hashtags);
            $group->save();
            return $group->id;
        }
    }


    public function utf8_fopen_read($file) {
        $fc = iconv('windows-1250', 'utf-8', file_get_contents($file));
        $handle=fopen("php://memory", "rw");
        fwrite($handle, $fc);
        fseek($handle, 0);
        return $handle;
    }

    public function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function CsvToContentUpload(Request $request){
        $newgroup = array();
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        if( mb_detect_encoding(file_get_contents($file), 'UTF-8, ISO-8859-2', true) == 'ISO-8859-2'){
            $fileO = $this->utf8_fopen_read($file);
        } else {
            $fileO = fopen($file, 'r');
        }
        $column=fgetcsv($fileO);
        while(!feof($fileO)){
            $row[]=fgetcsv($fileO);
        }
        $user = User::find(Auth::id());
        foreach ($row as $key => $item) {
            if($item[0] ){
                $hashtags = array(
                    'fb' => null,
                    'tw' => null,
                    'g' => null,
                    'in' => null,
                    'ins' => null,
                );
                $group_id = $this->group_id($item[0], 'upload', 'csv', $filename, '', $hashtags);
                array_push($newgroup, $group_id);

                /*				if($item[2] && !isset($item[3])){
                                    $html = $this->file_get_contents_curl($item[2]);
                                    $doc = new \DOMDocument();
                                    @$doc->loadHTML($html);
                                    $metas = $doc->getElementsByTagName('meta');
                                    for ($i = 0; $i < $metas->length; $i++)
                                    {
                                        $meta = $metas->item($i);
                                        if($meta->getAttribute('property') == 'og:image'){
                                            $ogImage = $meta->getAttribute('content');
                                        }
                                    }
                                    $item[3] = $ogImage;
                                }*/

                $post = new SocialPosts;
                $post->group_id = $group_id;
                $post->text = isset($item[1]) ? htmlspecialchars($item[1]) : null;
                $post->link = isset($item[2]) ? $item[2] : null;
                $post->image = isset($item[3]) ? $item[3] : null;
                $post->hash = serialize($hashtags);
                $post->save();
            }
        }
        $newgroups = array_unique($newgroup);
        $groupname = array();
        $groupid = array();
        foreach ($newgroups as $key => $group) {
            $group = SocialPostGroups::where('id', $group)->first();
            array_push($groupname,  $group->name);
            array_push($groupid,  $group->id);
        }
        sort($groupname);
        $groupids = SocialPostGroups::where('user_id', Auth::id())->where('name', $groupname[0])->where('type', 'upload')->first();
        return $groupids->id;
    }


    public function CsvToCurationUpload(Request $request){
        $newgroup = array();
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $fileO = fopen($file, 'r');
        $column=fgetcsv($fileO);
        while(!feof($fileO)){
            $row[]=fgetcsv($fileO);
        }
        foreach ($row as $key => $feeditem) {
            libxml_disable_entity_loader(false);
            if($feeditem[0]){
                $sslerr=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );
                $xml = file_get_contents($feeditem[1], false, stream_context_create($sslerr));
                $xml = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '', $xml);
                $xml = simplexml_load_string($xml);
                if( $xml->channel->item ){
                    $items = $xml->channel->item;
                } elseif ($xml->entry) {
                    $items = $xml->entry;
                }
                foreach ($items as $key => $item) {
                    $hashtags = array(
                        'fb' => isset($feeditem[2]) ? $feeditem[2] : null,
                        'tw' => isset($feeditem[5]) ? $feeditem[5] : null,
                        'g' => isset($feeditem[3]) ? $feeditem[3] : null,
                        'in' => isset($feeditem[4]) ? $feeditem[4] : null,
                        'ins' => isset($feeditem[6]) ? $feeditem[6] : null,
                    );
                    $group_id = $this->group_id($feeditem[0], 'curation', 'csv', $filename, $feeditem[1], $hashtags);
                    array_push($newgroup, $group_id);
                    $post_ = (array)$item;
                    if(is_object($post_['link'])){
                        $link = (string)$post_['link']->attributes()['href'];
                    } else {
                        $link = (string)$post_['link'];
                    }
                    $post = new SocialPosts;
                    $post->group_id = $group_id;
                    $post->text = htmlspecialchars($post_['title']);
                    $post->link = $link;
                    $post->rsslink = $feeditem[1];
                    $post->save();
                }
            }
        }
        $newgroups = array_unique($newgroup);
        $groupname = array();
        $groupid = array();
        foreach ($newgroups as $key => $group) {
            $group = SocialPostGroups::where('id', $group)->first();
            array_push($groupname,  $group->name);
            array_push($groupid,  $group->id);
        }
        sort($groupname);
        $groupids = SocialPostGroups::where('name', $groupname[0])->where('type', 'curation')->first();
        return $groupids->id;
    }


    public function CsvToRssAutomationUpload(Request $request){
        $newgroup = array();
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $fileO = fopen($file, 'r');
        $column=fgetcsv($fileO);
        while(!feof($fileO)){
            $row[]=fgetcsv($fileO);
        }
        $user = User::find(Auth::id());
        foreach ($row as $key => $item) {
            if($item[0] ){
                $hashtags = array(
                    'fb' => null,
                    'tw' => null,
                    'g' => null,
                    'in' => null,
                    'ins' => null,
                );
                $group_id = $this->group_id($item[0], 'rss-automation', 'csv', $filename, '', $hashtags);
                array_push($newgroup, $group_id);
                $hash = array(
                    'fb' => isset($item[2]) ? $item[2] : null,
                    'tw' => isset($item[5]) ? $item[5] : null,
                    'g' => isset($item[3]) ? $item[3] : null,
                    'in' => isset($item[4]) ? $item[4] : null,
                    'ins' => isset($item[6]) ? $item[6] : null,
                );
                $post = new SocialPosts;
                $post->group_id = $group_id;
                $post->rsslink = isset($item[1]) ? $item[1] : null;
                $post->hash = serialize($hash);
                $post->save();
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
                            $checkpost = RssAutoPost::where('post_id', $post->id)->where('text', $allpost['title'])->where('link', $allpost['link'])->count();
                            if($checkpost==0 && $allpost['link'] != '' && $allpost['link'] != null){
                                $rssautopost = new RssAutoPost;
                                $rssautopost->post_id = $post->id;
                                $rssautopost->text = $allpost['title'];
                                $rssautopost->link = $allpost['link'];
                                $rssautopost->pub_date = $allpost['pubDate'];
                                $rssautopost->save();
                            }
                        }
                    }
                    catch (\Exception $e) {
                    }
                }
            }
        }
        $newgroups = array_unique($newgroup);
        $groupname = array();
        $groupid = array();
        foreach ($newgroups as $key => $group) {
            $group = SocialPostGroups::where('id', $group)->first();
            array_push($groupname,  $group->name);
            array_push($groupid,  $group->id);
        }
        sort($groupname);
        $groupids = SocialPostGroups::where('name', $groupname[0])->where('type', 'rss-automation')->first();
        return $groupids->id;
    }


    public function AddContentOnline(Request $request){
        $hashtags = array();
        $group_id = $this->group_id('Untitled', 'upload', 'online', '', '', $hashtags );
        $post = new SocialPosts;
        $post->group_id = $group_id;
        $post->text = htmlspecialchars($request->text);
        $post->link = $request->url;
        $post->image = $request->image;
        $post->hash = serialize($hashtags);
        $post->save();
        return $group_id;
    }


    public function AddCurationOnline(Request $request){
        $hashtags = array(
            'fb' => null,
            'tw' => null,
            'g' => null,
            'in' => null,
            'ins' => null,
        );
        $sslerr=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $xml = file_get_contents($request->url, false, stream_context_create($sslerr));
        $xml = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '', $xml);
        $xml = simplexml_load_string($xml);
        $group_id = $this->group_id('Untitled', 'curation', 'online', '', $request->url, $hashtags);
        if( $xml->channel->item ){
            $items = $xml->channel->item;
        } elseif ($xml->entry) {
            $items = $xml->entry;
        }
        foreach ($items as $key => $item) {
            $post_ = (array)$item;
            if(is_object($post_['link'])){
                $link = (string)$post_['link']->attributes()['href'];
            } else {
                $link = (string)$post_['link'];
            }
            $post = new SocialPosts;
            $post->group_id = $group_id;
            $post->text = htmlspecialchars($post_['title']);
            $post->link = $link;
            $post->rsslink = $request->url;

            $post->status = '0';
            $post->save();
        }
        return $group_id;
    }


    public function AddRssAutomationOnline(Request $request){
        $hashtags = array(
            'fb' => null,
            'tw' => null,
            'g' => null,
            'in' => null,
            'ins' => null,
        );
        $group_id = $this->group_id('Untitled', 'rss-automation', 'online', '', $request->url, $hashtags);
        $hashtags = array(
            'fb' => isset($request->fb) ? $request->fb : null,
            'tw' => isset($request->tw) ? $request->tw : null,
            'g' => isset($request->g) ? $request->g : null,
            'in' => isset($request->in) ? $request->in : null,
            'ins' => isset($request->ins) ? $request->ins : null,
        );
        $post = new SocialPosts;
        $post->group_id = $group_id;
        $post->rsslink = isset($request->url) ? $request->url  : null;
        $post->hash = serialize($hashtags);
        $post->save();
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
                    $checkpost = RssAutoPost::where('post_id', $post->id)->where('text', $allpost['title'])->where('link', $allpost['link'])->count();
                    if($checkpost==0 && $allpost['link'] != '' && $allpost['link'] != null){
                        $rssautopost = new RssAutoPost;
                        $rssautopost->post_id = $post->id;
                        $rssautopost->text = $allpost['title'];
                        $rssautopost->link = $allpost['link'];
                        $rssautopost->pub_date = $allpost['pubDate'];
                        $rssautopost->save();
                    }
                }
            }
            catch (\Exception $e) {
                dd($e->getMessage());
            }
        }
        return $group_id;
    }


    public function AddContentOnlineIngroup(Request $request){
        $group_id = $request->group_id;
        $post = new SocialPosts;
        $post->group_id = $group_id;
        $post->text = htmlspecialchars($request->text);
        $post->link = $request->url;
        $post->image = $request->image;
        $post->save();
    }


    public function AddCurationOnlineIngroup(Request $request){
        $xml = file_get_contents($request->url);
        //$xml = utf8_encode($xml);
        $xml = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '', $xml);
        $xml = simplexml_load_string($xml);
        $group_id = $request->group_id;
        if( $xml->channel->item ){
            $items = $xml->channel->item;
        } elseif ($xml->entry) {
            $items = $xml->entry;
        }
        $group = SocialPostGroups::find($group_id);
        $file_link_old = unserialize($group->files_links);
        $csv = $file_link_old['csv'];
        $files =  $file_link_old['file'];
        $links =  $file_link_old['link'];
        if(!in_array($request->url, $links)){
            array_push($links, $request->url);
        }
        $file_link = array(
            'csv' => $csv,
            'file' => $files,
            'link' => $links
        );
        $group->files_links = serialize($file_link);
        $group->save();
        foreach ($items as $key => $item) {
            $post_ = (array)$item;
            if(is_object($post_['link'])){
                $link = (string)$post_['link']->attributes()['href'];
            } else {
                $link = (string)$post_['link'];
            }
            $post = new SocialPosts;
            $post->group_id = $group_id;

            $post->text = htmlspecialchars($post_['title']);
            $post->link = $link;
            $post->rsslink = $request->url;
            $post->image = null;
            $post->status = '0';
            $post->save();
        }
    }


    public function AddRssAutomationOnlineIngroup(Request $request){
        $hashtags = array(
            'fb' => null,
            'tw' => null,
            'g' => null,
            'in' => null,
            'ins' => null,
        );
        $hash = array(
            'fb' => isset($request->fb) ? $request->fb : null,
            'tw' => isset($request->tw) ? $request->tw : null,
            'g' => isset($request->g) ? $request->g : null,
            'in' => isset($request->in) ? $request->in : null,
            'ins' => isset($request->in) ? $request->in : null,
        );
        $post = new SocialPosts;
        $post->group_id = $request->group_id;
        $post->rsslink = isset($request->url) ? $request->url  : null;
        $post->hash = serialize($hash);
        $post->save();
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
                    $checkpost = RssAutoPost::where('post_id', $post->id)->where('text', $allpost['title'])->where('link', $allpost['link'])->count();
                    if($checkpost==0 && $allpost['link'] != '' && $allpost['link'] != null){
                        $rssautopost = new RssAutoPost;
                        $rssautopost->post_id = $post->id;
                        $rssautopost->text = $allpost['title'];
                        $rssautopost->link = $allpost['link'];
                        $rssautopost->pub_date = $allpost['pubDate'];
                        $rssautopost->save();
                    }
                }
            }
            catch (\Exception $e) {

            }
        }
    }


    public function PostUpdate(Request $request)
    {
        $text = isset($request->text) ? $request->text : null;
        $link = isset($request->link) ? $request->link : null;
        $rsslink = isset($request->rsslink) ? $request->rsslink : null;
        $image = isset($request->image) ? $request->image : null;
        $hash = array(
            'fb' => isset($request->fb) ? $request->fb : null,
            'tw' => isset($request->tw) ? $request->tw : null,
            'g' => isset($request->g) ? $request->g : null,
            'in' => isset($request->in) ? $request->in : null,
            'ins' => isset($request->ins) ? $request->ins : null,
        );
        $post = SocialPosts::where('id', $request->post_id)->first();
        $post->text = htmlspecialchars($text);
        $post->link = $link;
        $post->rsslink  = $rsslink;
        $post->image = $image;
        $post->hash = serialize($hash);
        $post->save();
    }


    public function PostDelete(Request $request)
    {
        $group = SocialPosts::find($request->post_id)->delete();
    }


    public function TargetSocialAccounts(Request $request){
        if($request->social_accounts){
            $social_accounts = serialize($request->social_accounts);
        } else {
            $social_accounts = serialize(array());
        }
        $group = SocialPostGroups::find($request->group_id);
        $group->target_acounts = $social_accounts;
        $group->save();
    }


    public function ScheduleUpdate(Request $request){
        $group = SocialPostGroups::find($request->group_id);
        $group->frequency = $request->quantity;
        $group->interval = $request->postingFrequency;
        $interval = $this->interval($group->interval, $group->frequency);
        $group->interval_seconds = $interval;
        if($request->start){
            $group->start_time = date('Y-m-d H:i:s', $request->start_time/1000);
        } else {
            $group->start_time = null;
        }
        if($request->end){
            $group->end_time = date('Y-m-d H:i:s', $request->end_time/1000);
        } else {
            $group->end_time = null;
        }
        $start_year = new \Carbon\Carbon($group->start_time);
        $end_year = new \Carbon\Carbon($group->end_time);
        if($start_year->year==1970){
            $group->start_time = null;
        }
        if($end_year->year==1970){
            $group->end_time = null;
        }
        $start_time = strtotime($group->start_time);
        if( time() > $start_time ){
            $group->next_schedule_time= date('Y-m-d H:i:s', time());
        } else {
            $group->next_schedule_time= date('Y-m-d H:i:s', $start_time);
        }

        $group->top_buffer_queue = 0;
        if(isset($request->top_buffer_queue)){
            $group->top_buffer_queue = $request->top_buffer_queue;
        }
        $group->enable_slot = 0;
        if(isset($request->enable_slot)){
            $group->enable_slot = $request->enable_slot;
        }
        $group->slot_amount = 0;
        if(isset($request->slot_amount)){
            $group->slot_amount = $request->slot_amount;
        }

        $group->save();
    }


    public function groupupdateanytime(Request $request,$id){
        $group = SocialPostGroups::find($id);
        $group->frequency = $request->quantity;
        $group->interval = $request->postingFrequency;
        $interval = $this->interval($group->interval, $group->frequency);
        $group->interval_seconds = $interval;
        $group->next_schedule_time =  date('Y-m-d H:i:s', time());
        $group->save();
    }


    public function ChangeGroupStatus(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id);
        if($group->status == '0'){
            if(!$group->posts[0]->schedule_at){
                $posts = $group->posts;
                $now = date('Y-m-d H');
                $now = $now.':00:00';
                foreach ($posts as $key => $post) {
                    $post = SocialPosts::find($post->id);
                    $post->schedule_at = date('Y-m-d H:i:s', strtotime($now . ' +'.$key.' day'));
                    $post->save();
                }
                $group->start_time = $now;
            }
            $group->status = 1;
            $group->save();
        }
        if($request->activate){
            $group->status = 1;
        } else {
            $group->status = 0;
        }
        $group->save();
        return $group->type;
    }


    public function RecycleGroupUpdate(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id);

        $group->recycle = isset($request->recycle) ? 1 : 0;
        $group->save();
    }


    public function ShuffleGroupUpdate(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id);
        $group->shuffle = isset($request->shuffle) ? 1 : 0;
        $group->save();
    }

    public function AddImageGroupUpdate(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id);
        $group->add_image = isset($request->add_image) ? 1 : 0;
        $group->save();
    }



    public function HashtagsUpdate(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id);
        $hashtags = array(
            'fb' => $request->fhash,
            'tw' => $request->ghash,
            'g' => $request->thash,
            'in' => $request->lhash,
            'ins' => $request->ihash,
        );
        $group->hash = serialize($hashtags);
        $group->save();
    }


    public function GroupDelete(Request $request)
    {
        $group = SocialPostGroups::find($request->group_id)->delete();
    }

    public function GroupDeleteIds(Request $request)
    {
        $ids = $request->ids;
        $ids = explode(',', $ids);
        foreach ($ids as $group_id){
            if((int)$group_id > 0 && $group_id != null){
                $group = SocialPostGroups::find($group_id)->delete();
            }
        }
    }

    public function GroupNameUpdate(Request $request)
    {
        $utm = array(
            'utm_campaign' => $request->utm_campaign,
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_content' => $request->utm_content,
        );
        $group = SocialPostGroups::find($request->group_id);
        $group->name =  $request->group_name;
        $group->utm =  serialize($utm);

        $group->skip_post_newer = $request->skip_post_newer;
        $group->skip_post_older = $request->skip_post_older;
        $group->keyword = $request->keyword;
        $group->skip_keyword = $request->skip_keyword;

        $group->save();
        return $group->name;
    }


    public function DeletePostByRsslink(Request $request)
    {
        $group = SocialPostGroups::where('id', $request->group_id)->first();
        $file_link = unserialize($group->files_links);
        $old_links = $file_link['link'];
        if(($key = array_search($request->rsslink, $old_links)) !== false) {
            unset($old_links[$key]);
        }
        $newfile_link = array(
            'csv' =>  $file_link['csv'],
            'file' => $file_link['link'],
            'link' => $old_links
        );
        $group->files_links = serialize($newfile_link);
        $group->save();
        $posts = SocialPosts::where('group_id',$request->group_id)->where('rsslink', 'regexp', $request->rsslink)->get();
        foreach ($posts as $key => $post) {
            $posts = SocialPosts::find($post->id)->delete();
        }
    }


    public function CurationRefresh(Request $request)
    {
        $current_posts = SocialPosts::where('group_id', $request->group_id)->get();
        $old_links = array();
        foreach ($current_posts as $key => $current_post) {
            array_push($old_links, unserialize($current_post->post)['link']);
        }
        $group = SocialPostGroups::find($request->group_id);
        $links = unserialize($group->files_links)['link'];
        foreach ($links as $key => $feeditem) {
            $sslerr=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            libxml_disable_entity_loader(false);
            $xml = file_get_contents($feeditem, false, stream_context_create($sslerr));
            $xml = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f]/', '', $xml);
            $xml = simplexml_load_string($xml);
            if( $xml->channel->item ){
                $items = $xml->channel->item;
            } elseif ($xml->entry) {
                $items = $xml->entry;
            }
            foreach ($items as $key => $item) {
                $group_id = $request->group_id;
                $post_ = (array)$item;
                if(is_object($post_['link'])){
                    $link = (string)$post_['link']->attributes()['href'];
                } else {
                    $link = (string)$post_['link'];
                }
                if(!in_array($link, $old_links)){
                    $post = new SocialPosts;
                    $post->group_id = $group_id;
                    $post->text = htmlspecialchars($post_['title']);
                    $post->link = $link;
                    $post->rsslink = $feeditem;
                    $post->save();
                }
            }
        }
    }


    public function DragDrop(Request $request)
    {
        $group = SocialPostGroups::where('id', $request->group_id)->first();
        if($group->status == '0'){
            if($request->target_status =='active'){
                if(!$group->posts[0]->schedule_at){
                    $posts = $group->posts;
                    $now = date('Y-m-d H');
                    $now = $now.':00:00';
                    foreach ($posts as $key => $post) {
                        $post = SocialPosts::find($post->id);
                        $post->schedule_at = date('Y-m-d H:i:s', strtotime($now . ' +'.$key.' day'));
                        $post->save();
                    }
                    $group->start_time = $now;
                }
                $group->status = 1;
                $group->save();

                $this->groupupdateanytime($group->id);
            }
            if($request->target_status=='completed'){
                $group->status = 2;
                $group->save();
            }
        }
        if($group->status == '1'){
            if($request->target_status=='pending'){
                $group->status = 0;
                $group->save();
                $posts = SocialPosts::where('group_id', $group->id)->get();
                foreach ($posts as $key => $post) {
                    $post = SocialPosts::find($post->id);
                    $post->status = '0';
                    $post->schedule_at = null;
                    $post->sent_at = null;
                    $post->save();
                }
            }
            if($request->target_status=='completed'){
                $group->status = 2;
                $group->save();
            }
        }
        if($group->status == '2'){
            if($request->target_status=='pending'){
                $group->status = 0;
                $group->save();
                $posts = SocialPosts::where('group_id', $group->id)->get();
                foreach ($posts as $key => $post) {
                    $post = SocialPosts::find($post->id);
                    $post->status = '0';
                    $post->schedule_at = null;
                    $post->sent_at = null;
                    $post->save();
                }
            }
            if($request->target_status=='active'){
                $group->status = 1;
                $group->save();
                $this->groupupdateanytime($group->id);
            }
        }
    }


    public function SocialAccountIdGroup(Request $request){
        $groupRem = SocialPostGroups::where('user_id', Auth::id())->get();
        foreach ($groupRem as $key => $group) {
            $target_acounts_arr = unserialize($group->target_acounts);
            if(($key = array_search($request->account_id, $target_acounts_arr)) !== false) {
                unset($target_acounts_arr[$key]);
            }
            $group->target_acounts = serialize($target_acounts_arr);
            $group->save();
        }
        $groups = $request->group_id;
        if(!empty($groups)){
            foreach ($groups as $key => $group) {
                $group = SocialPostGroups::find($group);
                $target_acounts_arr = unserialize($group->target_acounts);
                if(!in_array($request->account_id, $target_acounts_arr)){
                    array_push($target_acounts_arr, $request->account_id);
                }
                $group->target_acounts = serialize($target_acounts_arr);
                $group->save();
            }
        }
    }


    public function AccountActiveInactive(Request $request){
        if(!empty($request->ids)){
            if(!empty($request->active_inactive)){
                $account = SocialAccounts::find($request->ids);
                $account->status = '1';
                $account->save();
            } else {
                $account = SocialAccounts::find($request->ids);
                $account->status = '0';
                $account->save();
            }
        }
    }


    public function ReActivateGroup(Request $request){
        $group = SocialPostGroups::find($request->group_id);
        //reset group if all post sent
        $interval = $group->interval;
        $frequency = $group->frequency;
        if ($interval == 'hourly') {
            $hour = 1;
        }
        if ($interval == 'daily') {
            $hour = 24;
        }
        if ($interval == 'weekly') {
            $hour = 7 * 24;
        }
        if ($interval == 'monthly') {
            $hour = 31 * 24;
        }
        $rawinterval = $hour * 60 * 60;
        $intervals = $rawinterval / $frequency;
        $now = date('Y-m-d H:i:s');
        $posts = SocialPosts::where('group_id', $request->group_id)->get();
        foreach ($posts as $key => $post) {
            $post = SocialPosts::find($post->id);
            $post->schedule_at = date('Y-m-d H:i:s', strtotime($now . ' +'.$key*intval($intervals).' second'));
            $post->status = 0;
            $post->sent_at = null;
            $post->save();
        }
        $group->status = 1;
        $group->save();
        //$this->groupupdateanytime($group->id);
        return $group->type;
    }


    public function updateGroupanytime($id)
    {
        $group = SocialPostGroups::find($id);
        $intervals = $this->intervals($group);
        if($group->type == 'upload' || $group->type=='curation'){
            $posts = SocialPosts::where('group_id', $group->id)->orderBy('schedule_at')->get();
            $first_post_time = SocialPosts::where('group_id', $group->id)->where('schedule_at', '!=', null)->orderBy('schedule_at')->first()->schedule_at;
            $group->start_time = new \DateTime($first_post_time);
            foreach ($posts as $key => $post) {
                if($key == 0){
                    $schedule_at = $group->start_time;
                } else {
                    $schedule_at = $group->start_time->add(new \DateInterval('PT'.floor($intervals).'S'));
                }
                $post->schedule_at = $schedule_at->format('Y-m-d H:i:s');
                $post->save();
            }
        }
    }


    public function intervals($group)
    {
        $interval = $group->interval;
        $frequency = $group->frequency;

        if ($interval == 'hourly') {
            $hour = 1;
        }
        if ($interval == 'daily') {
            $hour = 24;
        }
        if ($interval == 'weekly') {
            $hour = 7 * 24;
        }
        if ($interval == 'monthly') {
            $hour = 31 * 24;
        }
        $rawinterval = $hour * 60 * 60;
        $intervals = $rawinterval / $frequency;
        return $intervals;
    }


    public function TimezoneForm(Request $request)
    {
        $user = User::find($request->user_id);
        $user->timezone = $request->timezone;
        $user->save();
    }


    public function SendIdea(Request $request)
    {
        \Mail::send([], [], function ($message) use ($request) {
            $message->from($request->email)->to(env('ADMINEMAIL'))->subject($request->sub)->setBody($request->message, 'text/html');
        });
    }


    public function PostsSort(Request $request)
    {

    }


    public function interval($interval, $frequency)
    {
        if ($interval == 'hourly') {
            $hour = 1;
        }
        if ($interval == 'daily') {
            $hour = 24;
        }
        if ($interval == 'weekly') {
            $hour = 7 * 24;
        }
        if ($interval == 'monthly') {
            $hour = 30 * 24;
        }
        $rawinterval = $hour * 60 * 60;
        $intervals = round($rawinterval / $frequency);
        return $intervals;
    }


    public function sendPostNow(Request $request)
    {
        $post = SocialPosts::find($request->post_id);

        if($post){
            $targetaccounts = unserialize($post->group->target_acounts);
            if(!empty($targetaccounts)){
                $checksentpost = array();
                foreach ($targetaccounts as $key => $targetaccount) {
                    $account = SocialAccounts::find($targetaccount);
                    if($account->status=='1'){
                        if(isset(unserialize($account->post_sent)['count'])){
                            $old_post_sent_count = unserialize($account->post_sent)['count'];
                        }
                        else {
                            $old_post_sent_count = 0;
                        }
                        if($post->group->type == 'curation' || $post->group->type == 'upload'){
                            $hash = unserialize($post->group->hash);
                        }
                        if($account->type =='facebook'){
                            $hash_s = isset($hash['fb']) ? $hash['fb'] : '';
                        }
                        if($account->type =='google'){
                            $hash_s = isset($hash['g']) ? $hash['g'] : '';
                        }
                        if($account->type =='linkedin'){
                            $hash_s = isset($hash['in']) ? $hash['in'] : '';
                        }
                        if($account->type =='twitter'){
                            $hash_s = isset($hash['tw']) ? $hash['tw'] : '';
                        }
                        if($account->type =='instagram'){
                            $hash_s = isset($hash['ins']) ? $hash['ins'] : '';
                        }

                        $hash_s = str_replace(array('{', '}'), array('', ''), $hash_s);
                        $hash_s = explode('|', $hash_s);
                        $rand = mt_rand(0, count($hash_s)-1);
                        $hash_s = $hash_s[$rand];

                        $utm_campaignarr = 	explode('utm_campaign', $post->link);

                        if($account->type=='facebook'){
                            $utm_source = 'facebook.com';
                        }
                        if($account->type=='google'){
                            $utm_source = 'plus.google.com';
                        }
                        if($account->type=='linkedin'){
                            $utm_source = 'linkedin.com';
                        }
                        if($account->type=='twitter'){
                            $utm_source = 'twitter.com';
                        }
                        if($account->type=='instagram'){
                            $utm_source = 'instagram.com';
                        }
                        if(isset($utm_campaignarr[1])){
                            $utm ='';
                        } else {
                            if(!empty(unserialize($post->group->utm))){
                                $utm_campaign = !empty(unserialize($post->group->utm)['utm_campaign']) ? unserialize($post->group->utm)['utm_campaign'] : 'Bulkly';
                                $utm_source = !empty(unserialize($post->group->utm)['utm_source']) ? unserialize($post->group->utm)['utm_source'] : $utm_source;
                                $utm_medium = !empty(unserialize($post->group->utm)['utm_medium']) ? unserialize($post->group->utm)['utm_medium'] : 'social';
                                $utm_content = !empty(unserialize($post->group->utm)['utm_content']) ? unserialize($post->group->utm)['utm_content'] : 'Bulkly'.$post->group->id;
                                $utm = '?utm_campaign='.$utm_campaign.'&utm_source='.$utm_source.'&utm_medium='.$utm_medium.'&utm_content='.$utm_content;
                            } else {
                                $utm = '?utm_campaign=Bulkly&utm_source='.$utm_source.'&utm_medium=social&utm_content=Bulkly'.$post->group->id;
                            }
                        }




                        if($post->group->type == "rss-automation"){
                            $rssPost = null;
                            $rssPostModel = new RssAutoPost();
                            if($post->group->shuffle == 1){
                                if($rssPostModel->where('post_id', $post->id)->where('status', 0)->count() > 0){
                                    $rssPostRand = $rssPostModel->where('post_id', $post->id)->whereIn('status', [0,1])->get()->random(1);
                                    foreach ($rssPostRand as $rnd){
                                        $rssPost = $rnd;
                                    }
                                } else {
                                    $rssPostRand = $rssPostModel->where('post_id', $post->id)->get()->random(1);
                                    foreach ($rssPostRand as $rnd){
                                        $rssPost = $rnd;
                                    }
                                }
                            } else {
                                $rssPost = $rssPostModel->where('post_id', $post->id)->where('status', 0)->get()->first();
                            }
                            if($rssPost != null){
                                $post->link = $rssPost->link;
                                $post->text = $rssPost->text;
                                $post->image = $rssPost->image;
                                $rssPostModel = new RssAutoPost();
                                $rssPostModel->where('id', $rssPost->id)->update(['status'=> 1]);
                            }
                        }
                        if($post->group->add_image == 1){
                            if(!isset($post->image)){
                                $html = $this->file_get_contents_curl($post->link);
                                $doc = new \DOMDocument();
                                @$doc->loadHTML($html);
                                $metas = $doc->getElementsByTagName('meta');
                                $ogImage = '';
                                for ($i = 0; $i < $metas->length; $i++) {
                                    $meta = $metas->item($i);
                                    if($meta->getAttribute('property') == 'og:image'){
                                        $ogImage = $meta->getAttribute('content');
                                    }
                                }

                                $post->image= $ogImage;
                            }
                        }





                        if($post->link){
                            $link_urm = $post->link.''.$utm;
                        } else {
                            $link_urm = null;
                        }





                        if($link_urm){


                            $final_link = $link_urm;

                            $client = new Client();

                            if($post->group->user->rebrandly_key){
                                try {



                                    $rebrandly_domain = $post->group->user->rebrandly_domain;

                                    if($rebrandly_domain) {
                                        $rebrandly_domain = $rebrandly_domain;
                                    } else {
                                        $rebrandly_domain = '';
                                    }


                                    $client = new Client([
                                        'headers' => [
                                            'Content-Type' => 'application/json',
                                            'Authorization' => 'Bearer ' . $post->group->user->rebrandly_key
                                        ]
                                    ]);

                                    $result = $client->post('https://api.rebrandly.com/v1/links',
                                        ['body' => json_encode(
                                            [
                                                'destination' => $final_link,
                                                'domain' => [
                                                    'id' => $rebrandly_domain,
                                                ]
                                            ]
                                        )]
                                    );

                                    print_r(json_decode($result->getBody()));



                                } catch (ClientException $e) {
                                    $json = $e->getResponse()->getBody();
                                    print_r(json_decode($json));
                                    $result = null;
                                } catch (RequestException $e) {
                                    $json = $e->getResponse()->getBody();
                                    print_r(json_decode($json));
                                    $result = null;
                                }

                                if($result){
                                    $final_link = 'http://'.json_decode($result->getBody())->shortUrl;
                                }	else {
                                    $final_link = null;
                                }
                            } else {
                                $final_link = $link_urm;
                            }

                        } else {
                            $final_link = null;

                        }

                        print_r($final_link);

                        // $final_link = $link_urm;



                        if(!$final_link){
                            $final_link  = $link_urm;
                        }


                        print_r(' Post text'. $post->text);
                        print_r(' Post image'. $post->image);
                        print_r(' Post link'. $final_link);


                        if($post->text || $post->image || $final_link){
                            $client = new Client();
                            // if($post->text != ''){
                            //     $post->text = $this->breakMe($post->text);
                            // }
                            $form_params = [
                                'access_token' => $account->buffer_token,
                                'profile_ids' => array($account->account_id),
                                'text' => htmlspecialchars_decode($post->text).' '.$final_link.' '.$hash_s
                            ];
                            if($account->type == 'instagram'){
                                $form_params['text'] = htmlspecialchars_decode($post->text).' '.$final_link.' '.PHP_EOL.PHP_EOL.$hash_s;
                            }
                            if(isset($post->image) && $post->image != ''){
                                $form_params['media']['photo'] = $post->image;
                            } else if(isset($final_link) && $final_link != ''){
                                $form_params['media']['link'] = $final_link;
                            }
                            try {
                                if($form_params['text'] != ''){
                                    $form_params['text'] = str_replace('amp;','',$form_params['text']);
                                }
                                $result = $client->request('POST', 'https://api.bufferapp.com/1/updates/create.json', [
                                    'form_params' => $form_params,
                                ]);
                                $json = $result->getBody();
                                print_r(date('Y-m-d H:i:s', time()).' - Sucess: ' .$account->name.' -- '.json_decode($json)->message."\n");
                            } catch (ClientException $e) {
                                $json = $e->getResponse()->getBody();
                                print_r(date('Y-m-d H:i:s', time()).' - Buffer Error: ' .$account->name.' -- '.json_decode($json)->message."\n");
                            } catch (RequestException $e) {
                                $json = $e->getResponse()->getBody();
                                print_r(date('Y-m-d H:i:s', time()).' - Buffer Error: ' .$account->name.' -- '.json_decode($json)->message."\n");
                            }
                            if(isset($result)){
                                $output = json_decode($result->getBody());
                                if(isset($output->updates)){
                                    if($output->updates[0]->id){
                                        array_push($checksentpost, 'ok');
                                        $bufferposting = new BufferPosting;
                                        $bufferposting->user_id = $account->user->id;
                                        $bufferposting->group_id = $post->group->id;
                                        $bufferposting->post_id = $post->id;
                                        $bufferposting->account_id = $account->id;
                                        $bufferposting->account_service = $account->type;
                                        $bufferposting->buffer_post_id = $output->updates[0]->id;
                                        $bufferposting->post_text = $post->text;
                                        $bufferposting->sent_at = date('Y-m-d H:i:s', time());
                                        $bufferposting->save();
                                    }
                                }
                                $new_post_sent = array(
                                    'count' => 1 + $old_post_sent_count,
                                    'last_sent_at' => date('Y-m-d H:i:s', time())
                                );
                                $account->post_sent = serialize($new_post_sent);
                                $account->save();
                            }
                        }

                    }
                }
            }

            if(!empty($checksentpost)){
                $postUpdateSent = SocialPosts::find($post->id);
                $postUpdateSent->sent_at = date('Y-m-d H:i:s', time());
                $postUpdateSent->status = 1;
                $postUpdateSent->save();
            }



        }




    }


    public function breakMe($str)
    {
        $rv = '';
        if ($str == '' || $str == null) {
            return '';
        }
        $arr = explode(' ', $str);
        if (count($arr) > 0) {
            $hash = 0;
            foreach ($arr as $a) {
                $check = strpos($a, '#');
                if ($check !== false) {
                    if ($hash == 1) {
                        $rv .= $a . ' ';
                    } else {
                        $hash = 1;
                        $rv .= PHP_EOL . $a . ' ';
                    }
                } else {
                    if ($hash == 0) {
                        $rv .= $a . ' ';
                    } else {
                        $hash = 0;
                        $rv .= PHP_EOL . $a . ' ';
                    }
                }
            }
        } else {
            return '';
        }
        return $rv;
    }



}
