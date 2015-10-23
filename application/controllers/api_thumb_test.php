<?php
if ( ! defined("BASEPATH")) exit("No direct script access allowed");
require_once(APPPATH ."controllers/common".EXT);

/**
 * @purpose Vimeo, Youtube 등의 Api 사용을 위한 Controller
 * @author  JoonCh
 * @since   13. 7. 26.
 */

class api_thumb_test extends common {

    public function __construct(){
        parent::__construct();
    }


    public function index() {
        $this->load->model("content_model");
        $this->load->helper('file');
        $params = $this->content_model->_select_list();

        $this->load->model('vimeo');
        $vimeo = new vimeo();
        $vimeo->setKeyToken('3b727db2432ef47c1d3855526984d2845712caae', 'cc63242aa7ea80f09858c061862f79fd0117bbb3',
            '802e0070a904b7bd17f5eb0afd3c0dde', '39aa3cabd67c6a113ba99f0ada7fc14c6374ddfd');
        $vimeo->enableCache(Vimeo::CACHE_FILE, './cache', 300);


        for($i=0; $i<count($params); $i++) {

            if($params[$i]['ctType'] == "VIMEO"){

                $videos = $vimeo->call('vimeo.videos.getInfo', array('video_id'=>$params[$i]['ctSource']));

                foreach($videos->video[0] as $k => $v)
                    $apiData[$k] = $v;

                if($apiData['thumbnails']->thumbnail[0]){

                    $data['ctThumbSOrigin'] = $apiData['thumbnails']->thumbnail[1]->_content;
                    $data['ctThumbLOrigin'] = $apiData['thumbnails']->thumbnail[2]->_content;

                    $arrFileType = explode('/', $data['ctThumbSOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeS = $arrFileType[$cntFileType];

                    $arrFileType = explode('/', $data['ctThumbLOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeL = $arrFileType[$cntFileType];

                    $data['ctThumbS'] = md5(date('YmdHis').$data['ctThumbSOrigin']).$fileTypeS;
                    $data['ctThumbL'] = md5(date('YmdHis').$data['ctThumbLOrigin']).$fileTypeL;

                    file_put_contents('./uploads/content/thumbs/'.$data['ctThumbS'], file_get_contents($data['ctThumbSOrigin']));
                    file_put_contents('./uploads/content/thumbl/'.$data['ctThumbL'], file_get_contents($data['ctThumbLOrigin']));

                }

                $this->content_model->_update($data,array('ctNO'=>$params[$i]['ctNO']));

            } else if($params[$i]['ctType'] == "YOUTUBE"){


                $url = 'https://gdata.youtube.com/feeds/api/videos/'.$params[$i]['ctSource'].'?v=2&alt=json';
                $contents = file_get_contents($url);
                $video = json_decode($contents);

                foreach($video->entry as $k => $v)
                    $apiData[$k] = $v;


                if($apiData['media$group']->{'media$thumbnail'}[1]){

                    $data['ctThumbSOrigin'] = $apiData['media$group']->{'media$thumbnail'}[1]->{'url'};
                    $data['ctThumbLOrigin'] = $apiData['media$group']->{'media$thumbnail'}[2]->{'url'};

                    $arrFileType = explode('/', $data['ctThumbSOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeS = $arrFileType[$cntFileType];

                    $arrFileType = explode('/', $data['ctThumbLOrigin']);
                    $cntFileType = count($arrFileType)-1;
                    $fileTypeL = $arrFileType[$cntFileType];

                    $data['ctThumbS'] = md5(date('YmdHis').$data['ctThumbSOrigin']).$fileTypeS;
                    $data['ctThumbL'] = md5(date('YmdHis').$data['ctThumbLOrigin']).$fileTypeL;

                    file_put_contents('./uploads/content/thumbs/'.$data['ctThumbS'], file_get_contents($data['ctThumbSOrigin']));
                    file_put_contents('./uploads/content/thumbl/'.$data['ctThumbL'], file_get_contents($data['ctThumbLOrigin']));
                }

                $this->content_model->_update($data,array('ctNO'=>$params[$i]['ctNO']));


            }
            unset($data);



        }
    }
}