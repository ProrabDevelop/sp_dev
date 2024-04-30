<?
class sb_menu{

    protected \admin\std_models\user $user;
    protected $std_menu = [
        //[
        //    'text' => 'Основной экран',
        //    'slug' => 'dashboard',
        //],
        [
            'text' => 'Пользователи',
            'slug' => 'users',
        ],
        [
            'text' => 'Рапределение рейтинга',
            'slug' => 'rating',
        ],

        [
            'text' => 'Отзывы',
            'slug' => 'reviews',
        ],
        [
            'text' => 'Специализации',
            'items' => [
                [
                    'text' => 'Существующие',
                    'slug' => 'spec/',
                ],
                [
                    'text' => 'Создать',
                    'slug' => 'spec/add_spec',
                ],
                [
                    'text' => 'Запросы',
                    'slug' => 'spec/spec_request',
                ],
            ]
        ],
    ];

    public function __construct(){
        $this->user = \core\engine\DATA::get('USER');
    }

    public function render_menu(){
        $this->mapping_items($this->std_menu);
    }

    protected function mapping_items($items){

        foreach ($items as $item){

            //single
            if(!isset($item['items'])){
                if(isset($item['perm'])){
                    if(!$this->user->can($item['perm'])){
                        continue;
                    }
                }
                echo '<li><a href="'.URL.$item['slug'].'">'.$item['text'].'</a></li>';
                continue;
            }

            //many
            if(isset($item['perm'])){
                if(!$this->user->can($item['perm'])){
                    continue;
                }
            }
            echo'<li><span>'.$item['text'].'</span><ul>';
            $this->mapping_items($item['items']);
            echo'</ul></li>';
        }

    }

}

?>

<div class="mobile_menu_header">
    <span class="mh-btns-left">
    	<a href="#menu" class="fa fa-bars"></a>
    </span>
</div>

<div class="menu_wrapper">
    <nav id="menu">
        <div id="panel-menu">
            <ul>
                <?
                $menu = new sb_menu();
                $menu->render_menu();
                ?>
            </ul>
        </div>

        <div id="panel-account">
            <ul>
                <li><a href="<?=URL?>profile/logout">Выход</a></li>
            </ul>
        </div>

    </nav>
</div>









