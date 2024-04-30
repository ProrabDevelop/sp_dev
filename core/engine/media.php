<?php

namespace core\engine;

class media{

    protected $sizes = ['default', 'thumb'];
    protected $name;
    protected $type;
    protected $path = WEB.'uploads/';

    public function __construct($media_info = null){

        $media_info = json_unformat($media_info);

        if($media_info != null){

            if(!isset($media_info['name']) ||
                !isset($media_info['type']) ||
                !isset($media_info['path']) ||
                !isset($media_info['sizes']) && !is_array($media_info['sizes'])
            ){
                return false;
            }

            $this->name = $media_info['name'];
            $this->type = $media_info['type'];
            $this->path = rtrim($media_info['path'], '/\\').'/';

            foreach ($media_info['sizes'] as $size_name){
                if(!in_array($size_name, $this->sizes)){
                    $this->sizes[] = $size_name;
                }
            }

        }

        return $this;

    }

    public function create_info($name, $type, $path, array $sizes = [], $return_type = 'media'){
        $this->name = $name;
        $this->type = $type;
        $this->path = rtrim($path, '/\\').'/';

        foreach ($sizes as $size){

            if(!in_array($size, $this->sizes)){
                $this->sizes[] = $size;
            }

        }

        switch ($return_type){
            case 'array':
                $return = $this->get_info();
                break;
            case 'json':
                $return = $this->get_json_info();
                break;
            default:
                $return = $this;
                break;
        }

        return $return;
    }

    public function get_info(){
        return  [
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'sizes' => $this->sizes
        ];
    }

    public function get_json_info(){
        return  json_encode($this->get_info(), JSON_UNESCAPED_UNICODE);
    }

    public function get_path($size = ''){
        return WEB.'uploads/'.$this->path.$this->get_media_filename_by_size($size);
    }

    public function get_url($size = ''){

        //copy(WEB.'assets/img/work.png', WEB.'uploads/'.$this->path.$this->get_media_filename_by_size($size));

        return BASE_URL.'uploads/'.$this->path.$this->get_media_filename_by_size($size);

    }

    protected function get_media_filename_by_size($size_name = 'default'){
        $size = 'default';
        $seporator = '-';

        if(in_array($size_name, $this->sizes)){
            $size = $size_name;
        }

        if($size == 'default'){
            $size = '';
            $seporator = '';
        }

        return $this->name.$seporator.$size.'.'.$this->type;
    }

    public function no_photo(array $sizes = []){


         return $this->create_info('no-photo', 'png', 'std', $sizes);
    }

}