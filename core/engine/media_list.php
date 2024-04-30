<?php

namespace core\engine;

class media_list{

    protected $medias = [];

    public function __construct($medias_info = null){

        if($medias_info != null){

            $medias_info = json_unformat($medias_info);

            if(is_array($medias_info)){

                if(isset($medias_info['name'])){
                    $this->medias[] = new media($medias_info);
                }else{
                    foreach ($medias_info as $media_info){
                        $this->medias[] = new media($media_info);
                    }
                }

            }

        }

        return $this;
    }

    public function get_all(){
        return $this->medias;
    }


}