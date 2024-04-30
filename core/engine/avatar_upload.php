<?php

namespace core\engine;

class avatar_upload{

    public function __construct(){

        $fields = \validator::ALL_POST([
            'user_id' => ['req', 'int'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            if(!isset($_FILES['file'])){
                API::error('1036', 'file not sended');
            }

            $file = $_FILES['file'];

            $avatar_name = $fields->user_id;

            $dest_folder = WEB."/uploads/avatars/";
            $dest_original = $dest_folder.$avatar_name."_original.png";

            imagepng(imagecreatefromstring(file_get_contents($file["tmp_name"])), $dest_original);

            $image = new thumbs($dest_original);
            $image->cut(300, 300);
            $image->savePNG($dest_folder.$avatar_name.'.png');

            (new rebuild_rating())->rebuild_user($fields->user_id);

            API::response(URL."/uploads/avatars/".$avatar_name.'.png');



        });

    }


}