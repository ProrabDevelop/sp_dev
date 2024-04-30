<?
namespace app\works;

use api\auth\auth;
use app\std_models\work;
use core\engine\API;
use core\engine\media;
use core\engine\std_module;

class works extends std_module {

    public $active = true;
    public $forauth = false;
    public $is_api = true;

    protected $routes = [
        '/works' => [
            '/' => 'main',
            '/{id:\d+}' => [
                '/' => 'get_work',
                '/save/' => 'save_work'
            ],
        ],
    ];

    public function main(){
        API::response(['message' => 'works main api']);
    }

    public function get_work($data){

        $id = $data['id'];

        $work = new work($id);
        if($work->id != null){
            $dest = [];

            $dest['id'] = $work->id;
            $dest['spec_id'] = $work->spec_id;
            $dest['name'] = $work->name;
            $dest['content'] = $work->content;
            $dest['price'] = money_beautiful($work->price).' ₽';

            $dest_medias = [];
            $medias = $work->get_medias();



            $std_medias = $work->medias;

            /* @var $media media*/
            foreach ($medias as $media){
                $dest_medias[] = [
                    '92x92' => $media->get_url('92x92'),
                    '160x130' => $media->get_url('160x130'),
                    '600x488' => $media->get_url('600x488'),
                    'default' => $media->get_url('default'),
                ];
            }
            $dest['medias'] = $dest_medias;
            $dest['std_medias'] = $std_medias;

            API::response($dest);
        }



        API::error(473, 'work is not exist by id='.$id);

    }


    public function save_work($data){

        $id = $data['id'];

        $rules = [
            'spec_id'  => ['req', 'int'],
            'name'     => ['req', 'str'],
            'content'  => ['req', 'str'],
            'price'     => ['req', 'int'],
            'medias' => ['arr'],
        ];

        $fields = \validator::ALL_POST($rules);


        $work = new work($id);
        if($work->id != null){
            $dest = [];

            if(!$fields->errors){

                $work->update($fields);

                $dest['id'] = $work->id;
                $dest['spec_id'] = $work->spec_id;
                $dest['name'] = $work->name;
                $dest['content'] = $work->content;
                $dest['price'] = money_beautiful($work->price).' ₽';

                $dest_medias = [];
                $medias = $work->get_medias();



                $std_medias = $work->medias;

                /* @var $media media*/
                foreach ($medias as $media){
                    $dest_medias[] = [
                        '92x92' => $media->get_url('92x92'),
                        '160x130' => $media->get_url('160x130'),
                        '600x488' => $media->get_url('600x488'),
                        'default' => $media->get_url('default'),
                    ];
                }
                $dest['medias'] = $dest_medias;
                $dest['std_medias'] = $std_medias;

                API::response($dest);

            }else{
                API::error_validator($fields);
            }
        }



        API::error(473, 'work is not exist by id='.$id);

    }

}