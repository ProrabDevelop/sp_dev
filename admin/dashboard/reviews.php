<?
namespace admin\dashboard;

use app\std_models\review;
use app\std_models\reviews_list;
use core\engine\DATA;
use core\engine\rating_range;
use core\engine\rebuild_rating;
use core\engine\std_module_admin;
use core\engine\view;

class reviews extends std_module_admin {

    public $forauth = true;

    protected $routes = [
        '/reviews' => [
            '/' => 'all_reviews',
        ],
    ];




    public function all_reviews(){

        if(isset($_POST['delete_review'])){
            $review = new review($_POST['delete_review']);
            $review->delete();
            (new rebuild_rating())->rebuild_user($review->master_id);
        }
        if(isset($_POST['delete_complaint'])){
            $review = new review($_POST['delete_complaint']);
            $review->complaint = null;
            $review->update();
        }

        $reviews = (new reviews_list())->get_to_moderate();






        DATA::set('reviews', $reviews);

    }





}