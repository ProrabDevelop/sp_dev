<?

namespace Core\Engine;

class pagination{

    protected static $page = 1;
    protected static $limit = 10;
    protected static $total;

    public static $base_url;

    public static function set_base_url($url){
        self::$base_url = $url;
    }

    public static function set_page($num){
        self::$page = $num;
    }
    public static function get_page(){
        return self::$page;
    }

    public static function set_limit($num){
        self::$limit = $num;
    }
    public static function get_limit(){
        return self::$limit;
    }

    public static function set_total($num){
        self::$total = $num;
    }
    public static function get_total(){
        return self::$total;
    }

    public static function get_offset(){
        return (self::$page - 1) * self::$limit;
    }

    public static function view($tag = null){

        $page_count = ceil(self::$total / self::$limit); ?>

        <? if($page_count != 1 && $page_count != 0){?>

            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item"><a class="page-link" href="<?= self::$base_url?>?page=1<?=$tag?>">&laquo;</a></li>


                    <?
                    if($page_count > 8){

                        if(self::$page > 3 && self::$page < $page_count - 3){

                            $min = self::$page - 3;
                            $max = self::$page + 3;

                            if($min != 1){
                                echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                            }

                            for($i=$min;$i<=$max; $i++){

                                if($i == self::$page){
                                    echo '<li class="page-item active"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                }else{
                                    echo '<li class="page-item"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                }

                            }

                            echo '<li class="page-item disabled"><a class="page-link">...</a></li>';

                        }else{

                            if(self::$page <= 4){

                                for($i=1;$i<=8; $i++){

                                    if($i == self::$page){
                                        echo '<li class="page-item active"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                    }else{
                                        echo '<li class="page-item"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                    }

                                }

                                if($page_count > 8){
                                    echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                                }

                            }

                            if(self::$page >= $page_count - 3){

                                $count = $page_count - 7;

                                if($count != 1){
                                    echo '<li class="page-item disabled"><a class="page-link">...</a></li>';
                                }

                                for($i=$count; $i <= $page_count; $i++){
                                    if($i == self::$page){
                                        echo '<li class="page-item active"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                    }else{
                                        echo '<li class="page-item"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                                    }

                                }

                            }

                        }


                    }else{



                        for($i=1; $i <= $page_count; $i++){

                            if($i == self::$page){
                                echo '<li class="page-item active"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'" class="active">'.$i.'</a></li>';
                            }else{
                                echo '<li class="page-item"><a class="page-link" href="'.self::$base_url.'?page='.$i.$tag.'">'.$i.'</a></li>';
                            }

                        }

                    }

                    ?>

                    <li class="page-item"><a class="page-link" href="<?= self::$base_url?>?page=<?= $page_count.$tag ?>">&raquo;</a></li>

                </ul>
            </nav>

        <?}?>
        <?
    }

}



