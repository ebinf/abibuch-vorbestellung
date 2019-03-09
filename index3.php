<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>
                <div class="flex-fill p-3 overflow-auto">
                    <h1 class="display-3"><?=$translation["thanks"]?></h1>
                    <h3><?=$translation["thanks_sub"]?></h3>
                    <p class="lead"><?php printf($translation["thanks_email"], '<a href="mailto:' . $config["general"]["contact_email"] . '">' . $config["general"]["contact_email"] . '</a>'); ?></p>
                </div>
                <div class="p-0 col-4 float-right d-none d-sm-block">
                    <img src="./res/img/spotlight.png" class="float-right img-fluid" style="width: 100%; height: 100%;" />
                </div>
<?php echo $footer; ?>
