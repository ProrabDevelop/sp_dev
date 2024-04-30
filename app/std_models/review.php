<?php

namespace app\std_models;

use core\engine\std_model;

class review extends std_model{

    public $id, $spec_id, $user_id, $reviewer_name, $master_id, $time, $score, $content, $answer, $complaint;

    protected $table_name = 'reviews';

    public function render_stars(){
        for ($i = 0; $i < 5; $i++){
            echo ($this->score <= $i) ? '<i class="icon icon-star gray"></i>' : '<i class="icon icon-star"></i>';
        }
    }


    public function render_fa_stars(){
        for ($i = 0; $i < 5; $i++){
            echo ($this->score <= $i) ? '<i class="fa fa-star" style="color: #b7b7b7;"></i>' : '<i class="fa fa-star" style="color: #0a91ff;"></i>';
        }
    }

}