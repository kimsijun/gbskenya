<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose Vimeo, Youtube 등의 Api 사용을 위한 Controller
 * @author  JoonCh
 * @since   13. 7. 26.
 */

class media extends common {

    public function __construct(){
        parent::__construct();
    }


    /*  AJAX 처리 메소드    */
    public function ajax_process() {
        $params = $this->input->post();
        $this->load->helper('file');


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *  Vimeo 카테고리 Data 를 Json 으로 생성
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        if($params['mode'] == "getApiContent"){

            $media = strtolower($params['ctType']);
            $url =  "./assets/json/".$media."/".$params['ctCate'].".json";
            $contents = file_get_contents($url);
            $cate = json_decode($contents);

            $mediaID = ($media == "youtube")? "playlistId" : "channelId";
            $html = '<select name="ctSource" class="form-control">';
            $html.= '<option value="">선택</option>';
            foreach($cate as  $v)
                $html .= '<option value="'.$v->$mediaID.'">'.$v->title.'</option>';
            $html .= '</select>';

            echo json_encode($html);



        }else if($params['ctType'] == "VIMEO"){
            $this->load->model('vimeo');
            $vimeo = new vimeo();
            $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
                '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
            $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);



            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  Vimeo 카테고리 Data 를 Json 으로 생성
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($params['mode'] == "videoCateUpdate") {

                $idx=0;
                $videos = $vimeo->call('vimeo.channels.getAll', array('per_page'=>1000));
                if($videos){
                    foreach($videos->channels->channel as $k => $v){
                        $apiData[$idx]['title'] = $v->name;
                        $apiData[$idx++]['channelId'] = $v->id;
                    }

                    $data = json_encode($apiData);
                    write_file('./assets/json/vimeo/vimeo_cate.json', $data);
                }

				redirect(base_url("/adm/content/index"));

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  Vimeo 컨텐츠 Data 를 Json 으로 생성
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            } else if($params['mode'] == "videoContentUpdate") {


                $url =  "./assets/json/vimeo/vimeo_cate.json";
                $contents = file_get_contents($url);
                $cate = json_decode($contents);

                foreach($cate as $val){
                    $idx=0;
                    $videos = $vimeo->call('vimeo.channels.getVideos', array('per_page'=>10000,'channel_id'=>$val->channelId));
                    if($videos){
                        foreach($videos->videos->video as $v){
                            $apiData[$idx]['title'] = $v->title;
                            $apiData[$idx++]['channelId'] = $v->id;
                        }

                        $data = json_encode($apiData);
                        write_file('./assets/json/vimeo/'.$val->channelId.'.json', $data);
                        unset($apiData);
                    }
                }
                
                redirect(base_url("/adm/content/index"));



            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  VIMEO API 를 이용하여 컨텐츠의 데이터를 가져옴
             *  Api 에서 제공하는 영상 재생 시간은 초 단위로 받기 때문에 시:분:초 형태로 변경함
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            } else if($params['mode'] == "getApiInfo") {
                $videos = $vimeo->call('vimeo.videos.getInfo', array('video_id'=>$params['ctSource']));

                foreach($videos->video[0] as $k => $v)
                    $apiData[$k] = $v;

                if($apiData['thumbnails']->thumbnail[0]){

                    $seconds = $apiData['duration'];
                    $h = sprintf("%02d", intval($seconds) / 3600);
                    $tmp = $seconds % 3600;
                    $m = sprintf("%02d", $tmp / 60);
                    $s = sprintf("%02d", $tmp % 60);

                    $data['video']['apiID']         = $apiData['id'];
                    $data['video']['apiTitle']      = $apiData['title'];
                    $data['video']['apiDescription']= $apiData['description'];
                    $data['video']['apiRegDate']    = $apiData['upload_date'];
                    $data['video']['apiThumb']['s'] = $apiData['thumbnails']->thumbnail[1]->_content;
                    $data['video']['apiThumb']['l'] = $apiData['thumbnails']->thumbnail[2]->_content;
                    $data['video']['apiDuration']   = $h.':'.$m.':'.$s;
                    $data['video']['apiViewCount']  = $apiData['number_of_plays'];
                    $data['video']['apiLikeCount']  = $apiData['number_of_likes'];
                }

                echo json_encode($data);
            }





        } else if($params['ctType'] == "YOUTUBE"){


            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  Youtube 카테고리 Data 를 Json 으로 생성
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            if($params['mode'] == "videoCateUpdate") {

                $url = 'https://gdata.youtube.com/feeds/api/users/gbsafrica/playlists?v=2&alt=json&max-results=50';
                $contents = file_get_contents($url);
                $videos = json_decode($contents);

                $idx =0;
                foreach($videos->feed->entry as $value){
                    $apiData[$idx]['title'] = $value->{'title'}->{'$t'};
                    $apiData[$idx++]['playlistId'] = $value->{'yt$playlistId'}->{'$t'};
                }

                $data = json_encode($apiData);
                write_file('./assets/json/youtube/youtube_cate.json', $data);

				redirect(base_url("/adm/content/index"));
				

            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  Youtube 컨텐츠 Data 를 Json 으로 생성
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            } else if($params['mode'] == "videoContentUpdate") {


                $url =  "./assets/json/youtube/youtube_cate.json";
                $contents = file_get_contents($url);
                $cate = json_decode($contents);

                foreach($cate as $val){
                    $idx =0;
                    $start_index = 1;
                    $max_results = 50;

                    while(true){
                        $url = 'https://gdata.youtube.com/feeds/api/playlists/'.$val->playlistId.'?v=2&alt=json&max-results='.$max_results.'&start-index='.$start_index;
                        //                  $url = 'https://gdata.youtube.com/feeds/api/playlists/PL2468E4A9108D8F5B?v=2&alt=json&max-results='.$max_results.'&start-index='.$start_index;
                        $contents = file_get_contents($url);
                        $videos = json_decode($contents);


                        foreach($videos->feed->entry as $value){
                            $apiData[$idx]['title'] = $value->{'media$group'}->{'media$title'}->{'$t'};
                            $apiData[$idx++]['playlistId'] = $value->{'media$group'}->{'yt$videoid'}->{'$t'};
                        }

                        $crStartIndex   = $videos->{'feed'}->{'openSearch$startIndex'};
                        $crPerPage      = $videos->{'feed'}->{'openSearch$itemsPerPage'};
                        $crTotalResult  = $videos->{'feed'}->{'openSearch$totalResults'};

                        if((int)( $crStartIndex + $crPerPage) - 1  > $crTotalResult)    break;
                        $start_index += $max_results;
                    }

                    $data = json_encode($apiData);
                    write_file('./assets/json/youtube/'.$val->playlistId.'.json', $data);
                    unset($apiData);
                }
	
				redirect(base_url("/adm/content/index"));



            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *  Youtube API 를 이용하여 컨텐츠의 데이터를 가져옴
             *  Api 에서 제공하는 영상 재생 시간은 초 단위로 받기 때문에 시:분:초 형태로 변경함
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
            } else if($params['mode'] == "getApiInfo") {

                $url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id='.$params['ctSource'].'&fields=items&key=AIzaSyBeMZu_8m8NXoVqlU1r5DSOIhKSV04DKHo';
                $contents = file_get_contents($url);
                /*$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$contents = curl_exec($ch);
				curl_close($ch);*/
				
                $video = json_decode($contents)->items;
                foreach($video[0] as $k => $v)
                    $apiData[$k] = $v;

                if($apiData['snippet']){
                    $duration = $apiData['contentDetails']->{'duration'};

                    $temp = explode('PT',$duration);
                    if(strpos($temp[1],'H')){
                        $h = explode('H',$temp[1]);
                        if($h[0]<10) $h[0] = '0'.$h[0];
                        if(strpos($temp[1],'M')){
                            $m = explode('M',$h[1]);
                            if($m[0]<10) $m[0] = '0'.$m[0];
                            if(strpos($temp[1],'S')){
                                $s = explode('S',$m[1]);
                            }
                        }else{
                            $m[0]='00';
                            if(strpos($temp[1],'S')){
                                $s = explode('S',$temp[1]);
                            }
                        }
                    }else{
                        $h[0]='00';
                        if(strpos($temp[1],'M')){
                            $m = explode('M',$temp[1]);
                            if($m[0]<10) $m[0] = '0'.$m[0];
                            if(strpos($temp[1],'S')){
                                $s = explode('S',$m[1]);
                            }
                        }else{
                            $m[0]='00';
                            if(strpos($temp[1],'S')){
                                $s = explode('S',$temp[1]);
                            }
                        }
                    }
                    $data['video']['apiID']         = $apiData['id'];
                    $data['video']['apiTitle']      = $apiData['snippet']->{'title'};
                    $data['video']['apiDescription']= $apiData['snippet']->{'description'};
                    $data['video']['apiRegDate']    = $apiData['snippet']->{'publishedAt'};
                    $data['video']['apiThumb']['s'] = $apiData['snippet']->{'thumbnails'}->{'medium'}->{'url'};
                    $data['video']['apiThumb']['l'] = $apiData['snippet']->{'thumbnails'}->{'high'}->{'url'};
                    $data['video']['apiDuration']   = $h[0].':'.$m[0].':'.$s[0];
                    $data['video']['apiViewCount']  = 0;
                    $data['video']['apiLikeCount']  = 0;
                }

                echo json_encode($data);
            }
        }

    }


}