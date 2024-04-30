<?php

namespace app\std_models;

use core\engine\std_model;

class speciality extends std_model{

    public $id, $similar, $name, $name_many, $name_of, $name_cat, $publish;

    protected $table_name = 'speciality';

}