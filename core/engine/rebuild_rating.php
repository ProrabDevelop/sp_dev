<?

namespace core\engine;

use app\catalog\service;
use app\std_models\reviews_list;
use app\std_models\services_list;
use app\std_models\user;
use app\std_models\users_list;
use app\std_models\works_list;

class rebuild_rating{

    private array $rating_ranges;

    public function __construct(){
        $this->rating_ranges = (new rating_range())->get_list();
    }


    public function rebuild_user($id){

        $sort_ratings = \ORM::for_table('sort_rating')->where('user_id', $id)->find_many();

        $users_ids_raw = [];
        foreach ($sort_ratings as $sort_rating){
            $users_ids_raw[] = $sort_rating->user_id;
        }

        $users_ids = array_unique($users_ids_raw);

        $users = (new users_list())->get($users_ids);

        $this->rebuild_action($sort_ratings, $users);

    }

    public function rebuild_all(){

        $sort_ratings = \ORM::for_table('sort_rating')->find_many();

        $users_ids_raw = [];
        foreach ($sort_ratings as $sort_rating){
            $users_ids_raw[] = $sort_rating->user_id;
        }

        $users_ids = [];

        if(is_array($users_ids_raw)){
            $users_ids = array_unique($users_ids_raw);
        }


        $users = (new users_list())->get($users_ids);

        $this->rebuild_action($sort_ratings, $users);

    }


    private function rebuild_action($sort_ratings, $users) : void{

        // $this->rating_ranges['param_name']->value;



        foreach ($sort_ratings as $sort_rating){

            $temp_rating = 0;
            //print_r(!isset($sort_rating->_data));
            //exit();

            if(!isset($sort_rating->_data)){
                //continue;
            }
            //services_count

            $services_count = (new services_list())->get_by_spec_for_user_count_only($sort_rating->user_id, $sort_rating->spec_id);

            
            if($this->rating_ranges['services_count']->value == ""){
                $s = 0;
            } else {
                $s = $this->rating_ranges['services_count']->value;
            }

            $s = (int)$s;
            $temp_rating+= $services_count * $s;//$this->rating_ranges['services_count']->value;

           
            /*print_r("-----".$sort_rating->user_id."ddd".$sort_rating->spec_id);
            print_r("!!!!");
            print_r($this->rating_ranges['reviews']->value);
            print_r("<br>fff<br>");*/
            //reviews
            $reviews = (new reviews_list())->get_by_spec_for_master_count_only($sort_rating->user_id, $sort_rating->spec_id);




            
            $user_reviews = \ORM::for_table('reviews')->where('master_id', $sort_rating->user_id)->find_many();


                $all_score = 0;
                $temp_score = 0;
                if(count($user_reviews)>0){
                    $l = 0;
                    for($i = 0; $i<count($user_reviews); $i++){
                        
                        if($user_reviews[$i]["spec_id"] == $sort_rating->spec_id){
                            $temp_score += $user_reviews[$i]["score"];
                            $l++;
                        }
                    }



                    if($temp_score != 0){
                        $all_score = round($temp_score / $l, 1);
                    }
                }
                if($all_score>5){
                    //$all_score = 5;
                }

                //print_r($all_score."-----мастер ид--".$sort_rating->user_id."------".$sort_rating->spec_id."-----и--".$l."------------очки-".$temp_score);
                //print_r("<br><br>");

            
                /*
                    $all_score = 0;
                    $temp_score = 0;
                    print_r($all_score."----------".$sort_rating->user_id);
                    print_r("<br><br>");
                */
            if($this->rating_ranges['reviews']->value == ""){
                $r = 0;
            } else {
                $r = $this->rating_ranges['reviews']->value;
            }
            $r = (int)$r;


            $temp_rating+= $reviews * $r;//$this->rating_ranges['reviews']->value;

            //works
            $works = (new works_list())->get_by_spec_for_user_count_only($sort_rating->user_id, $sort_rating->spec_id);

            
            if($this->rating_ranges['works']->value == ""){
                $w = 0;
            } else {
                $w = $this->rating_ranges['works']->value;
            }


            $w = (int)$w;
            $temp_rating+= $works * $w;//$this->rating_ranges['works']->value;

            //avatar
            if(file_exists(WEB.'uploads/avatars/'.$sort_rating->user_id.'.png')){
                $temp_rating+= 1 * $this->rating_ranges['avatar']->value;
            }



            //experience

            
            if($users[$sort_rating->user_id]->master_data->get('experience') == ""){
                $exp = 0;
            } else {
                $exp = $users[$sort_rating->user_id]->master_data->get('experience');
            }
            $exp = (int)$exp;
            $temp_rating+= $this->rating_ranges['experience']->value * $exp;//$users[$sort_rating->user_id]->master_data->get('experience');


            //finished
            if($users[$sort_rating->user_id]->master_data->get('finished') == ""){
                $usf = 0;
            } else {
                $usf = $users[$sort_rating->user_id]->master_data->get('finished');
            }

            $usf = (int)$usf;
            $temp_rating+= $this->rating_ranges['finished']->value * $usf;//$users[$sort_rating->user_id]->master_data->get('finished');

            //guarantee
            if($users[$sort_rating->user_id]->master_data->get('guarantee') == ""){
                $gua = 0;
            } else {
                $gua = $users[$sort_rating->user_id]->master_data->get('guarantee');
            }
            $gua = (int)$gua;
            $temp_rating+= $this->rating_ranges['guarantee']->value * $gua;//$users[$sort_rating->user_id]->master_data->get('guarantee');

            //telegram
            
            if(!empty($users[$sort_rating->user_id]->master_data->get('telegram'))){

                if($this->rating_ranges['telegram']->value == ""){
                    $tg = 0;
                } else {
                    $tg = $this->rating_ranges['telegram']->value;
                }
                $tg = (int)$tg;
                $temp_rating+= 1 * $tg;//$this->rating_ranges['telegram']->value;
            }

            //whatsapp
            if(!empty($users[$sort_rating->user_id]->master_data->get('whatsapp'))){

                if($this->rating_ranges['whatsapp']->value == ""){
                    $ws = 0;
                } else {
                    $ws = $this->rating_ranges['whatsapp']->value;
                }
                $ws = (int)$ws;
                $temp_rating+= 1 * $ws;//$this->rating_ranges['whatsapp']->value;
            }

            //viber
            if(!empty($users[$sort_rating->user_id]->master_data->get('viber'))){
                if($this->rating_ranges['viber']->value == ""){
                    $vb = 0;
                } else {
                    $vb = $this->rating_ranges['viber']->value;
                }
                $vb = (int)$vb;
                $temp_rating+= 1 * $vb;//$this->rating_ranges['viber']->value;
            }

            //vk
            if(!empty($users[$sort_rating->user_id]->master_data->get('vk'))){
                
                if($this->rating_ranges['vk']->value == ""){
                    $vk = 0;
                } else {
                    $vk = $this->rating_ranges['vk']->value;
                }
                $vk = (int)$vk;
                $temp_rating+= 1 * $vk;//$this->rating_ranges['vk']->value;
            }

            //fb
            if(!empty($users[$sort_rating->user_id]->master_data->get('fb'))){
                
                if($this->rating_ranges['fb']->value == ""){
                    $fb = 0;
                } else {
                    $fb = $this->rating_ranges['fb']->value;
                }
                $fb = (int)$fb;
                $temp_rating+= 1 * $fb;//$this->rating_ranges['fb']->value;
            }

            //inst
            if(!empty($users[$sort_rating->user_id]->master_data->get('inst'))){
                
                if($this->rating_ranges['inst']->value == ""){
                    $inst = 0;
                } else {
                    $inst = $this->rating_ranges['inst']->value;
                }
                $inst = (int)$inst;
                $temp_rating+= 1 * $inst;//$this->rating_ranges['inst']->value;
            }

            //docs
            if($users[$sort_rating->user_id]->master_data->get('type_ooo') == ""){
                $type = 0;
            } else {
                $type = $users[$sort_rating->user_id]->master_data->get('type_ooo');
            }
            $type = (int)$type;
            $docs = $users[$sort_rating->user_id]->master_data->get('type_ip') + $type;//$users[$sort_rating->user_id]->master_data->get('type_ooo');
            if($docs > 0){
                $temp_rating+= $this->rating_ranges['docs']->value * 1;
            }


            
            //REBUILD ITEM
            $sort_rating->rating_summ = $temp_rating;
            $sort_rating->rating = $all_score;
            $sort_rating->save();
        }



    }




}