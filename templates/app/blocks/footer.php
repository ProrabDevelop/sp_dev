

<!-- end APP -->

<? get_block('footer_xs_menu') ?>

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
                <i class="close_informer icon icon-cancel"></i>
            </p>
            <div class="content">
                <?= $item['content']?>
            </div>
        </div>
    <?}?>

</div>


<!-- end SYS NOTIFICATION -->

<? get_block('modals') ?>

<script src="<?= URL ?>assets/js/core/jquery.js"></script>
<script src="<?= URL ?>assets/js/core/jquery-ui.js"></script>
<script src="<?= URL ?>assets/js/core/jquery.tablesorter.min.js"></script>
<script src="<?= URL ?>assets/js/core/jquery.dateformat.min.js"></script>
<script src="<?= URL ?>assets/js/core/jquery.cookie-1.4.1.min.js"></script>
<script src="<?= URL ?>assets/js/core/jquery.toggle-password.js"></script>
<script src="<?= URL ?>assets/js/core/inputmask.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="<?= URL ?>assets/js/core/slick.min.js"></script>
<script type="text/javascript" src="<?= URL ?>assets/js/core/jquery.mobile.custom.min.js"></script>


<? render_scripts();?>

<script src="<?= URL ?>assets/js/main.js?v=<?= rand(100000, 999999)?>"></script>









