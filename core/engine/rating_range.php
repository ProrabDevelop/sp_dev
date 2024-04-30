<?

namespace core\engine;

class rating_range extends std_model {

    public $id, $param, $ru_name, $value;
    protected $table_name = 'rating_ranges';

    protected $rating_range_list = [];

    public function get_list(){

        $rating_range_list = \ORM::for_table($this->table_name)->find_many();

        if($rating_range_list){
            foreach ($rating_range_list as $rating_range_item){
                $this->rating_range_list[$rating_range_item->param] = (new rating_range())->set_ormdata($rating_range_item);
            }
        }

        return $this->rating_range_list;

    }


}
