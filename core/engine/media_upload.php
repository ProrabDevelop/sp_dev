<?php

namespace core\engine;

class media_upload{

    public function __construct(){

        $fields = \validator::ALL_POST([
            'path' => ['req', 'str'],
            'sizes' => ['str'],
        ]);

        API::auto_validate($fields, function (\validator $fields){

            if(!isset($_FILES['file'])){
                API::error('1036', 'file not sended');
            }

            $file = $_FILES['file'];

            $unique_name = uniqid();
            $sizes = array_map('trim', explode(',', $fields->sizes));

            $dest_folder = WEB."/uploads/".$fields->path.'/';
            $dest_original = $dest_folder.$unique_name.".png";

            imagepng(imagecreatefromstring(file_get_contents($file["tmp_name"])), $dest_original);

            foreach ($sizes as $size){
                $size_wh = explode('x', $size);

                $image = new thumbs($dest_original);
                $image->cut($size_wh[0], $size_wh[1]);
                $image->savePNG($dest_folder.$unique_name.'-'.$size.'.png');

            }

            $media = (new media())->create_info($unique_name, 'png', $fields->path, $sizes, 'array');

            API::response($media);

        });

    }


}