<?
namespace admin\dashboard;

use core\engine\DATA;
use core\engine\rating_range;
use core\engine\rebuild_rating;
use core\engine\std_module_admin;
use core\engine\view;

class rating extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/rating' => [
            '/' => 'rating',
        ],
    ];


    public function rating(){

        $rating_ranges = (new rating_range())->get_list();

        $fields_rules = [];
        foreach ($rating_ranges as $param_name => $val){
            $fields_rules[$param_name] = ['int', ['min', 0], ['max',100]];
        }

        $fields = \validator::ALL_POST($fields_rules);

        if($fields->POST()){


            if(isset($_POST['action']) && $_POST['action'] == 'refresh_rating'){

                (new rebuild_rating())->rebuild_all();

            }else{

                if($fields->errors){
                    view::validator_errors($fields->errors);
                }else{

                    foreach ($rating_ranges as $param_name => $rating_range){
                        /* @var $rating_range rating_range*/
                        $rating_range->value = $fields->$param_name;
                        $rating_range->update();
                    }


                    view::set_notification('success', [
                        'title' => 'Сохранение',
                        'content' => 'Рапределение весов рейтинга успешно обновлено',
                    ]);

                }

            }



        }



        DATA::set('rating_ranges', $rating_ranges);


    }





}