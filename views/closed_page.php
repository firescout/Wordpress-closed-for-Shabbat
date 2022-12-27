<?php
    $geo_name_id = (isset($args[0]->geo_name_id) && $args[0]->geo_name_id != 0) ? $args[0]->geo_name_id :"1023441";
    $end_time = (isset($args[0]->end_time) && !empty($args[0]->end_time)) ? $args[0]->end_time : "18:00";
?>
<html>

<head>
    <title><?= get_bloginfo( 'name' ); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="px-4 my-5 text-center">
        <img class="d-block mx-auto mb-4" src="<?= site_url(); ?>/wp-content/plugins/shabbat-closed/dist/img/star.jpg" alt="" width="72" />
        <h1 class="display-5 fw-bold"><?= $args[1]->post_title; ?></h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">This site will be open again after <?= $end_time; ?></p>
        </div>
        <div class="col-lg-6 mx-auto">
            <p id="hebcal-shabbat" class="lead mb-4">
            </p>
        </div>
        <div class="col-lg-6 mx-auto">
            <?= $args[1]->post_content; ?>
        </div>
    </div>
    <script defer>
        // fetch('https://www.hebcal.com/shabbat?cfg=j&geonameid=1023441&M=on&lg=s&tgt=_top')
        fetch('https://www.hebcal.com/shabbat?cfg=i2&geonameid=<?= $geo_name_id; ?>&M=on&lg=s&tgt=_top')
            .then(response => response.text())
            .then(data => document.getElementById('hebcal-shabbat').innerHTML = data);
    </script>
</body>

</html>