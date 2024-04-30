<?php

namespace core\engine;

use app\std_models\user;

class doc_upload{

    public $exlude_formats = ['php', 'exe'];

    public function __construct(){

        $fields = \validator::ALL_POST([
            'path' => ['req', 'str'],
            'doc_type' => ['req', 'str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            if(!isset($_FILES['file'])){
                API::error('1036', 'file not sended');
            }



            $file = $_FILES['file'];
            $format = explode(".", strtolower($file['name']));

            if(in_array($format, $this->exlude_formats)){
                API::error('549', 'sended '.strtoupper(end($format)).' file');
            }

            $unique_name = uniqid();


            $dest_folder = WEB."uploads/docs/".$fields->path.'/';
            $dest_original = $dest_folder.$unique_name.".".end($format);

            move_uploaded_file($file["tmp_name"], $dest_original);

            $media = (new media())->create_info($unique_name, end($format),'docs/'.$fields->path, []);



            $doc_type = 'type_'.$fields->path;

            //TODO Обязательно Отделить от загрузчика
            $user = (AUTH::init())->user;
            if($user instanceof user){
               if($user->is_master()){

                   $update_fields = new \stdClass();

                   if($fields->path == 'ip'){
                       $update_fields->type_ip = 1;
                       $update_fields->docs_ip = $media->get_json_info();
                   }
                   if($fields->path == 'ooo'){
                       $update_fields->type_ooo = 1;
                       $update_fields->docs_ooo = $media->get_json_info();
                   }

                   $user->update_profile($update_fields);
               }
            }

            API::response(['check_field' => $doc_type]);

            //API::response($media);

        });

    }


}