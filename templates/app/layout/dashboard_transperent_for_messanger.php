<? get_block('header_meta');?>

<? get_block('header_menu');?>



<div class="dashboard_layout">

    <div class="wrap">

        <aside class="sidebar">
            <?
            $user = \core\engine\DATA::get('USER');
            get_block('sidebar/'.$user->role.'_dashboard');
            ?>
        </aside>

        <div class="content_wrapper">
            <main class="dashboard_transperent_for_messanger">
                <? get_content();?>
            </main>
        </div>

    </div>


</div>



</div>
<? get_block('footer_menu') ?>
<? get_block('footer');?>

</body>
</html>
