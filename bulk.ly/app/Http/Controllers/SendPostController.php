<?php
namespace Bulkly\Http\Controllers;
use Illuminate\Http\Request;
use Bulkly\SocialPostGroups;
use Bulkly\SocialPosts;
use Bulkly\SocialAccounts;
use Bulkly\BufferPosting;
use Bulkly\RssAutoPost;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
class SendPostController extends Controller
{

	// start haveanyschedulepost
	public function haveanyschedulepost($posts){
		foreach ($posts as $post) {
			if ($post->status == '0'){
				return 'have';
			}
		}
		return 'nothave';
	}

	// end haveanyschedulepost
	// start interval
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

	// start index

	public function index()
	{

// Checking active group 
print_r('Start
');	
print_r('
');	  
print_r('Current time: '.date('Y-m-d H:i:s'));	
print_r('
');



		$onlyactiveGroups = SocialPostGroups::where('status', 1)->get();

print_r('Active Group Found: '. $onlyactiveGroups->count());
print_r('
');


		foreach ($onlyactiveGroups as $key => $group) {
print_r('
');		    
print_r('Group ID: '. $group->id);
print_r('
');
print_r('Group schedule time: '. $group->next_schedule_time);
print_r('
');	
		}





		$activeGroups = SocialPostGroups::where('status', 1)->where('next_schedule_time', '<', date('Y-m-d H:i:s', time()))->get();

print_r('Group Found: '. $activeGroups->count());
print_r('
');		

		
		foreach ($activeGroups as $key => $group) {




print_r('Group ID: '. $group->id);
print_r('
');		    
print_r('Group schedule time: '. $group->next_schedule_time);
print_r('
');		
$group->next_schedule_time = date('Y-m-d H:i:s', time() + $group->interval_seconds);
$group->save();
print_r('Group next schedule time: '. $group->next_schedule_time);
print_r('
');						
print_r('
');	
		    

			if($group->user->plansubs()['subscription']->status=='past_due'){
				$group->status = '2';
				$group->save();
				if($group->user->subs_status!='past_due'){
					$group->user->subs_status='past_due';
					$group->user->save();
					try{
						$client = new Client;
						$result = $client->request('POST', 'https://api2.autopilothq.com/v1/contact', [
							'headers' => [
								'autopilotapikey' => env('AUTOP'),
								'Content-Type'     => 'application/json'
							],
							'json' => [
								'contact' => [
									'Email' => $group->user->email,
									'custom' => [
										'string--Subscription--Status' => 'past_due',
										],
									'_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
								]
							]
						]);
						
					} catch (RequestException $e) {
						
					} catch (ClientException $e) {
						
					}
				}
				
			} else {
				if($group->user->subs_status=='past_due'){
					$group->user->subs_status='active';
					$group->user->save();
					try{
						$client = new Client;
						$result = $client->request('POST', 'https://api2.autopilothq.com/v1/contact', [
							'headers' => [
								'autopilotapikey' => env('AUTOP'),
								'Content-Type'     => 'application/json'
							],
							'json' => [
								'contact' => [
									'Email' => $group->user->email,
									'custom' => [
										'string--Subscription--Status' => 'active',
										],
									'_autopilot_list' => '9ECC7B84-9EB3-43EB-8C08-72A20E2573EA'
								]
							]
						]);
					} catch (RequestException $e) {
						
					} catch (ClientException $e) {
						
					}
				}
			}
			$timestamp = $group->user->plansubs()['subscription']->current_period_start;
			
			if(!$timestamp){
			    $timestamp = date('Y-m');
			    $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));
			    
			}

			// user can send posts maximum in a month

			if($group->user->plansubs()['plan']){
				$user_pph = $group->user->plansubs()['plan']->ppm;
			} else {
				$user_pph = '5000';
			}
			

			// user sent post on current period
			$user_current_pph = \Bulkly\BufferPosting::where('user_id', $group->user->id)->where('created_at', '>', $timestamp)->count();
  
			



print_r('User Can Sent: '. $user_pph);
print_r('
');	

print_r('User Sent: '. $user_current_pph);
print_r('
');	




			if($user_current_pph < $user_pph) {

print_r('User have permition to sent post');
print_r('
');	

				// user reached the limit of sending post
				$posts = $group->posts;
				$haveanyschedulepost = $this->haveanyschedulepost($posts);
				//$haveanyschedulepost = 'have';
				if($group->recycle=='1' && $haveanyschedulepost=='nothave'){
				    
				    $start_time = strtotime($group->start_time);
				    
				    $repeat_wait = $group->repeat_wait ? $group->repeat_wait : 0;
				    
				    $nextstart = $start_time + $group->repeat_wait*24*60*60;
				    
				    $nextstart = date('Y-m-d H:i:s',  $nextstart);
				    
				    
				    $group->start_time = $nextstart;
				    $group->next_schedule_time = $nextstart;
				    $group->save();
				    
				    
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
					if($group->shuffle == 1){
						$posts  = $posts->shuffle();
					}
					foreach ($posts as $key => $post) {
						$post = SocialPosts::find($post->id);
						$post->status = 0;
						$post->sent_at = null;
						$post->save();
					}
					if($group->type=='rss-automation'){
						$haverssautoposts = array();
						foreach ($posts as $key => $post) {
							$rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
							$haveanyrssautoposts = $this->haveanyschedulepost($rssautoposts);
							//$haveanyschedulepost = 'have';
							array_push($haverssautoposts, $haveanyrssautoposts);
						}
						if (in_array('have', $haverssautoposts)) {
						} else {
							foreach ($posts as $key => $post) {
								$rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
								foreach ($rssautoposts as $key => $rssautopost) {
									$rssautopost = RssAutoPost::find($rssautopost->id);
									$rssautopost->status = 0;
									$rssautopost->save();
								}
							}
						}
					} 
					
					
					

				}
				
				
				if($group->recycle=='0' && $haveanyschedulepost=='nothave'){
					$updategroup = SocialPostGroups::find($group->id);
					if($updategroup->type=='rss-automation'){
						$haverssautoposts = array();
						foreach ($posts as $key => $post) {
							$rssautoposts = RssAutoPost::where('post_id', $post->id)->get();
							$haveanyrssautoposts = $this->haveanyschedulepost($rssautoposts);
							//$haveanyschedulepost = 'have';
							array_push($haverssautoposts, $haveanyrssautoposts);
						}
						if (in_array('have', $haverssautoposts)) {
						} else {
							$updategroup->status = 2;
						}
					} else {
						$updategroup->status = 2;
					}
					$updategroup->save();
				}
			} else{
				$group->status = 2;
				$group->save();
			}

			
			if($user_current_pph < $user_pph) {
				
			// user can send post
				if($group->type == 'rss-automation'){
print_r('Rss Group');
print_r('
');	

					$posts = SocialPosts::where('schedule_at', '<', date('Y-m-d H:i:s', time()))->whereIn('status', array('1','0'))->where('group_id', $group->id)->get();

print_r('Rss Group post found '. count($posts));
print_r('
');	
					foreach ($posts as $key => $post) {
						
						$item = RssAutoPost::where('post_id', $post->id)->where('status', 0)->first();

if($item){
	print_r('Rss Group post item found ');
}
 else {
print_r('Rss Group post no item found ');
	$rrspostUpdate = SocialPosts::find($post->id);
	$rrspostUpdate->status = 1;
	$rrspostUpdate->save();
 }



print_r('
');
						if($item){

if(isset($item->text)){
print_r('Rss Group post item text '. $item->text);					
} else {
	print_r('Rss Group post item text no text');
}
print_r('
');						
							$post->text = isset($item->text) ? htmlspecialchars($item->text) : '';
							
							$post->link = $item->link;	
							
							$targetaccounts = unserialize($post->group->target_acounts);
							
							
							
							
							if(!empty($targetaccounts)){
								$checksentpost = array();
								foreach ($targetaccounts as $key => $targetaccount) {
									$account = SocialAccounts::find($targetaccount);
									if($account->status=='1'){
										if(isset(unserialize($account->post_sent)['count'])){
											$old_post_sent_count = unserialize($account->post_sent)['count'];
										} else {
											$old_post_sent_count = 0;
										}
										if($post->group->type == 'rss-automation'){
											$hash = unserialize($post->hash);
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
			
			

print_r('Post ID '. $post->id);
print_r('
');	
print_r('Post Text '. $post->text);
print_r('
');										

										if($post->link){
											$link_urm = $post->link.''.$utm;
										} else {
											$link_urm = null;
										}
				                    					



print_r('Link before: '.$link_urm);
print_r('
');										

										if($link_urm){


											$final_link = $link_urm;

											$client = new Client();

											if($post->group->user->rebrandly_key){
												try {
                												    
                                                    $result = $client->request('GET', 'https://api.rebrandly.com/v1/links/new?destination='.urlencode($final_link), [
                                                    'headers' => [
                                                        'Authorization' => 'Bearer ' . $post->group->user->rebrandly_key
                                                        ]
                                                    ]); 
                                    
												} catch (ClientException $e) {
													$result = null;
												} catch (RequestException $e) {
													$result = null;
												}

												if($result){
													$final_link = 'https://'.json_decode($result->getBody())->shortUrl;
												}	else {
													$final_link = null;
												}
											} else {
												$final_link = $link_urm;
											}	

										} else {
												$final_link = null;

										}
print_r('Link after: '.$final_link);
print_r('
');

										if($post->text){
											$client = new Client();
											$form_params = [
												'access_token' => $account->buffer_token,
												'profile_ids' => array($account->account_id),
												'text' => htmlspecialchars_decode($post->text).' '.$final_link.' '.$hash_s,
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
												$post->status = '10';
												$post->save();
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
														$bufferposting->group_id = $group->id;
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
								$item->status = 1;
								$item->save();
								
								$postUpdateSent = SocialPosts::find($post->id);
								$interval = $postUpdateSent->group->interval;
								$frequency = $postUpdateSent->group->frequency;
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
								
								$actu_lainr = $intervals * $postUpdateSent->group->posts->count();
								$schedule_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($postUpdateSent->schedule_at)). ' + '.$actu_lainr.' second'));
								$postUpdateSent->sent_at = date('Y-m-d H:i:s', time());  
								$postUpdateSent->schedule_at =  $schedule_at;
								$postUpdateSent->save();
							}
							
							
							
							
						} else {
							$postUpdate = SocialPosts::find($post->id);
							$postUpdate->status = 1;
							$postUpdate->save();
						}
						

					}
					
					
					
				} else {
					
print_r('Non Rss Group');
print_r('
');	
					$post = SocialPosts::where('status', 0)->where('group_id', $group->id)->first();
					if($post){
						
						$targetaccounts = unserialize($post->group->target_acounts);
						if(!empty($targetaccounts)){
							$checksentpost = array();
							foreach ($targetaccounts as $key => $targetaccount) {
								$account = SocialAccounts::find($targetaccount);
								if($account->status=='1'){
									if(isset(unserialize($account->post_sent)['count'])){
										$old_post_sent_count = unserialize($account->post_sent)['count'];
									} else {
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
								

                    				
print_r('Post ID '. $post->text);
print_r('
');	
				
                    				

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
								    
                                    $result = $client->request('GET', 'https://api.rebrandly.com/v1/links/new?destination='.urlencode($final_link), [
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $post->group->user->rebrandly_key
                                        ]
                                    ]); 
                                    
								} catch (ClientException $e) {
									$result = null;
								} catch (RequestException $e) {
									$result = null;
								}

								if($result){
									$final_link = 'https://'.json_decode($result->getBody())->shortUrl;
								}	else {
									$final_link = null;
								}
							} else {
								$final_link = $link_urm;
							}	

						} else {
								$final_link = null;

						}


									
									if($post->text){
										$client = new Client();
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
											$post->status = '10';
											$post->save();
											
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
													$bufferposting->group_id = $group->id;
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
			} else{
			}
			
			


			
		}

print_r('End
');	
		
	}
}