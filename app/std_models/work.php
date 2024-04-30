<?php

namespace app\std_models;

use core\engine\media;
use core\engine\media_list;
use core\engine\std_model;

class work extends std_model {

    public $id, $spec_id, $user_id, $name, $price, $content, $medias;
    protected $media_list;

    protected $table_name = 'works';


    protected function after_remap(){
        $this->media_list = (new media_list($this->medias))->get_all();
    }

    public function get_medias(){
        return $this->media_list;
    }

    public function get_first_media(){

        if(isset($this->media_list[0])){
            return $this->media_list[0];
        }
        return (new media())->no_photo(['704x512']);
    }
}
