<?php

namespace core\engine;

class document{

    protected $name;
    protected $type;
    protected $path = WEB.'uploads/';

    public function __construct($media_info = null){

        $media_info = json_unformat($media_info);

        if($media_info != null){

            if(!isset($media_info['name']) ||
                !isset($media_info['type']) ||
                !isset($media_info['path'])
            ){
                return false;
            }

            $this->name = $media_info['name'];
            $this->type = $media_info['type'];
            $this->path = rtrim($media_info['path'], '/\\').'/';

        }

        return $this;

    }

    public function create_info($name, $type, $path, $return_type = 'media'){
        $this->name = $name;
        $this->type = $type;
        $this->path = rtrim($path, '/\\').'/';

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
        ];
    }

    public function get_json_info(){
        return  json_encode($this->get_info(), JSON_UNESCAPED_UNICODE);
    }

    public function get_path(){
        return WEB.'uploads/'.$this->path.'/'.$this->name.'.'.$this->type;
    }

    public function get_url($size = ''){
        return SUB_URL.'uploads/'.$this->path.'/'.$this->name.'.'.$this->type;
    }





}