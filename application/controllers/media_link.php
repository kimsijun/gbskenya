<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose Vimeo, Youtube 등의 Api 사용을 위한 Controller
 * @author  JoonCh
 * @since   13. 7. 26.
 */

class media_link extends common {

    public function __construct(){
        parent::__construct();
    }

    public  function thumb_update() {
        $this->_print();
    }

	public function index() {
		$url =  "./assets/json/vimeo/vimeo_cate.json";
        $contents = file_get_contents($url);
        $vimeoCate = json_decode($contents);
        $data['vimeo_cate'] = $vimeoCate;

        $url =  "./assets/json/youtube/youtube_cate.json";
		$contents = file_get_contents($url);        
		$youtubeCate = json_decode($contents);
        $data['youtube_cate'] = $youtubeCate;
		$this->_print($data);	
	}
	
	
	public function videoCateUpdate_vimeo() {
		$this->load->model('vimeo');
        $vimeo = new vimeo();
        $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
            '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
        $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);

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

		redirect(base_url("/media_link/index"));
	}
	
	
	public function videoContentUpdate_vimeo() {
		$params = $this->input->post();
		$this->load->model('vimeo');
        $vimeo = new vimeo();
        $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
            '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
        $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);
		
        $idx=0;
        $videos = $vimeo->call('vimeo.channels.getVideos', array('per_page'=>10000,'channel_id'=>$params['ctChannelId']));
        if($videos){
            foreach($videos->videos->video as $v){
                $apiData[$idx]['title'] = $v->title;
                $apiData[$idx++]['channelId'] = $v->id;
            }

            $data = json_encode($apiData);
            write_file('./assets/json/vimeo/'.$params['ctChannelId'].'.json', $data);
            unset($apiData);
        }
        
        redirect(base_url("/media_link/index"));
	}

	
	public function videoCateUpdate_youtube() {
        $url =  "./assets/json/config/media_source.json";
        $contents = file_get_contents($url);
        $youtubeID = json_decode($contents);

        $idx =0;
        $start_index = 1;
        $max_results = 50;
        $pageToken='';
        while(true){
            if($pageToken){
                $url = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet&channelId='.$youtubeID->youtube.'&maxResults='.$max_results.'&pageToken='.$pageToken.'&key=AIzaSyAC3VpNlx0gEjNnC9T030rBb0aGwytP528';
            }else{
                $url = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet&channelId='.$youtubeID->youtube.'&maxResults='.$max_results.'&key=AIzaSyAC3VpNlx0gEjNnC9T030rBb0aGwytP528';
            }
            //$contents = file_get_contents($url); 
            $ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contents = curl_exec($ch);
			curl_close($ch);

            $video = json_decode($contents);

            foreach($video->items as $value){
                $apiData[$idx]['title'] = $value->{'snippet'}->{'title'};
                $apiData[$idx++]['playlistId'] = $value->{'id'};
            }
            if($video->{'nextPageToken'}){
                $pageToken = $video->{'nextPageToken'};
            }
            if(!$video->{'nextPageToken'})break;
        } 

        $data = json_encode($apiData);
        write_file('./assets/json/youtube/youtube_cate.json', $data);

		redirect(base_url("/media_link/index"));
	
	}
	
	public function videoContentUpdate_youtube() {
		$params = $this->input->post();

        $idx =0;
        $max_results = 50;
        $pageToken='';
        while(true){
            if($pageToken){
                $url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$params['ctChannelId'].'&maxResults='.$max_results.'&pageToken='.$pageToken.'&key=AIzaSyAC3VpNlx0gEjNnC9T030rBb0aGwytP528';
            }else{
                $url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$params['ctChannelId'].'&maxResults='.$max_results.'&key=AIzaSyAC3VpNlx0gEjNnC9T030rBb0aGwytP528';
            }
            //$contents = file_get_contents($url);
            //$url = 'https://gdata.youtube.com/feeds/api/playlists/'.$params['ctChannelId'].'?v=2&alt=json&max-results='.$max_results.'&start-index='.$start_index;
            $ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$contents = curl_exec($ch);
			curl_close($ch);
			
            $videos = json_decode($contents);
            foreach($videos->items as $value){
                $apiData[$idx]['title'] = $value->{'snippet'}->{'title'};
                $apiData[$idx]['thumbnail'] = $value->{'snippet'}->{'thumbnails'}->{'default'}->{'url'};
                $apiData[$idx++]['playlistId'] = $value->{'snippet'}->{'resourceId'}->{'videoId'};
            }

            if($videos->{'nextPageToken'}){
                $pageToken = $videos->{'nextPageToken'};
            }
            if(!$videos->{'nextPageToken'})break;
        }

        $data = json_encode($apiData);
        write_file('./assets/json/youtube/'.$params['ctChannelId'].'.json', $data);
        unset($apiData);


		redirect(base_url("/media_link/index"));
	}
	
}