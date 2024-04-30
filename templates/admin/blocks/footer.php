<footer>
    <!--<h4>STD подвал</h4>-->
</footer>

<!-- end APP -->



</div>

<!-- end all-page -->
</div>

<!-- SYS NOTIFICATION -->
<div class="notification_wrap">
    <?
    $notifications = \Core\Engine\view::get_notifications();
    foreach($notifications as $index => $item){?>
        <div class="notification_item">
            <p class="title <?= $item['type']?>">
                <?= $item['title']?>
                <i class="close_informer fa fa-close"></i>
            </p>
            <div class="content">
                <?= $item['content']?>
            </div>
        </div>
    <?}?>
</div>
<!-- end SYS NOTIFICATION -->

<? get_block('modals') ?>

<!--<link rel="stylesheet" href="<?= URL?>assets/css/app.css">-->
<? get_block('scripts')?>


<script src="<?= URL ?>admin_assets/js/core/jquery.js"></script>
<script src="<?= URL ?>admin_assets/js/core/jquery-ui.js"></script>
<script src="<?= URL ?>admin_assets/js/core/jquery-doubletap.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<script src="<?= URL ?>admin_assets/js/adm_menu/mmenu.polyfills.js"></script>
<script src="<?= URL ?>admin_assets/js/adm_menu/mmenu.js"></script>
<script>

    var page_has_menu = false;
    if ($('#menu').length > 0) {
        page_has_menu = true;
    }

    if(page_has_menu){

        $('#menu a').each(function(){
            var href = $(this).attr('href');

            if ( window.location.href.indexOf(href) >= 0 ){
                $(this).parent('li').addClass('current-page');
            }
        });

        new Mmenu(
            document.querySelector('#menu'),
            {
                extensions: ['theme-dark', 'shadow-page', 'fx-panels-none'],
                setSelected: true,
                counters: true,
                searchfield: {
                    placeholder: 'Поиск',
                },
                iconbar: {
                    use: '(min-width: 650px)',
                    top: [
                        '<a href="#app"><span class="fa fa-bars"></span></a>',
                    ],

                    /*bottom: [
                        '<a href="#"><span class="fa fa-twitter"></span></a>',
                        '<a href="#"><span class="fa fa-facebook"></span></a>',
                        '<a href="#"><span class="fa fa-youtube"></span></a>',
                    ],*/
                },
                sidebar: {
                    collapsed: {
                        use: '(min-width: 750px)',
                        hideNavbar: false,
                    },
                    expanded: {
                        use: '(min-width: 992px)',
                        initial: 'remember',
                    },


                },
                navbars: [
                    {
                        content: ['searchfield'],
                    },
                    {
                        type: 'tabs',
                        content: [
                            '<a href="#panel-menu"><i class="fa fa-bars"></i> <span>Меню</span></a>',
                            '<a href="#panel-account"><i class="fa fa-user"></i> <span>Аккаунт</span></a>',
                        ],
                    },
                    {
                        content: ['prev', 'breadcrumbs', 'close'],
                    },
                ],
            },
            {
                searchfield: {
                    clear: true,
                },
                navbars: {
                    breadcrumbs: {
                        removeFirst: true,
                    },
                },
                offCanvas: {
                    page: {
                        selector: "#all-page"
                    }
                },
                classNames: {
                    selected: "current-page"
                }
            }
        );

    }

    $(function () {
        $(document).on('click', '.close_informer', function () {
            var parent = $(this).parent().parent();
            parent.animate({left:'-500px'},300);

            setTimeout(function (){

                parent.animate({height:'0px', padding:'0px', margin:'0px'},300);
                setTimeout(function (){
                    parent.remove();
                }, 300);

            }, 300);

        })
    });

</script>








