<?php 
    $DEBUG = true;
?>
<!DOCTYPE html>
<html lang='zh-CN'>
<head>
    <meta charset='UTF-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>hololive生放送查询工具</title>
    <!-- 新 Bootstrap4 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
 
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="https://cdn.staticfile.org/jquery/3.5.1/jquery.min.js"></script>
    
    <!-- bootstrap.bundle.min.js 用于弹窗、提示、下拉菜单，包含了 popper.min.js -->
    <script src="https://cdn.staticfile.org/popper.js/1.16.0/umd/popper.min.js"></script>
    
    <!-- 最新的 Bootstrap4 核心 JavaScript 文件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <link href="css/vtb_card.css" rel="stylesheet" type="text/css" />
    <link href="css/vtb_modal.css" rel="stylesheet" type="text/css" />

    <!-- <script src="https://www.youtube.com/player_api"></script> -->
</head>
<body>
    <?php include 'components/nav.php' ?>
    <div class="container-fluid">
        <?php

            // 读取文件
            $filepath = "data/statistics/hololive.json";
            $data = file_get_contents($filepath);
            if($DEBUG)
            {
                echo $data;
            }

            // 处理json
            $data = json_decode($data, true);
            $tagList = array("无印组", "一期生", "二期生", "Gamers", "三期生", "四期生");
        ?>
            <?php
                // foreach($tagList as $tag)
                // {
                //     // echo '<div class="row d-flex flex-row justify-content-around mb-3 mt3 flex-wrap">';
                //     // $generation = $data[$tag];
                //     // foreach($generation as $vtb)
                //     // {
                //     //     makeCard($vtb);
                //     // }
                //     // echo '</div>';
                // }
            ?>
    </div>
</body>
</html>