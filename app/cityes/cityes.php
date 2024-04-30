<?
namespace app\cityes;


use core\engine\API;
use core\engine\std_module;

class cityes extends std_module {

    public $active = true;
    public $forauth = false;
    public $is_api = true;

    protected $routes = [
        '/cityes' => [
            '/' => 'main',
            '/list/' => 'citys_list',
        ],
    ];

    public function main(){
        API::response(['message' => 'Cityes main api']);
    }

    public function citys_list(){

        $fields = \validator::ALL_POST(['chars' => ['req','str']]);

        API::auto_validate($fields, function ($fields) {
            $cities = \ORM::for_table('cityes_for_search');
            $cities->where_like('name', '%'.$fields->chars.'%');

            if (city_available) {
                $cities->where_in('name', city_available);
            }

            $cities->order_by_desc('id')->limit(10);
            $cities = $cities->find_many();

            $response_data = [];
            $results = [];

            if ($cities) {
                foreach ($cities as $city) {
                    $results[] = [
                        'id' => $city->id,
                        'text' => $city->name,
                    ];
                }
            }

            $response_data['results'] = $results;

            echo json_encode($response_data, JSON_UNESCAPED_UNICODE);
            exit;
        });
    }

}
