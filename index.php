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
    <?php 
        /* 函数定义 */

        // 生成卡片
        function makeCard($data)
        {
            global $DEBUG;
            // if($DEBUG)
            // {
            //     echo var_dump($data);
            // }

            // 分离id
            preg_match("/[^\/]{1,}$/", $data["link"], $id);
            $id = $id[0];
            // if($DEBUG)
            // {
            //     var_dump($id);
            // }

            $status = "";
            // 标签含义
            // 若既无正在直播也无预定直播，则为红色
            if($data["is-casting"]==null && $data["will-cast"]==null)
            {
                $status = '<br/><a class="label label-danger" title="直播" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a class="label label-danger" title="预定直播">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            elseif($data["is-casting"]!=null && $data["will-cast"]==null)
            {
                $status = '<br/><a class="label label-success" title="直播" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a class="label label-danger" title="预定直播">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            elseif($data["is-casting"]==null && $data["will-cast"]!=null)
            {
                $status = '<br/><a class="label label-danger" title="直播" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a class="label label-success" title="预定直播">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }
            elseif($data["is-casting"]!=null && $data["will-cast"]!=null)
            {
                $status = '<br/><a class="label label-success" title="直播" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a class="label label-success" title="预定直播" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
            }

            // 构建正在直播元素
            $isCasting = "";
            if($data["is-casting"]!=null)
            {
                preg_match("/(?<=watch\?v=)[a-zA-Z0-9-_]{1,}$/", $data["is-casting"]["link"], $isCastingId);
                $isCastingId = $isCastingId[0];
                $isCasting = 
                '<div id="is-casting">
                        <!--<iframe src="' . $data["is-casting"]["cover"] . '" width="246" height="138" scrolling="no" ></iframe>!-->
                        <div class="player-container">
                            <iframe class="player" src="https://www.youtube.com/embed/' . $isCastingId . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <a href="https://www.youtube.com' . $data["is-casting"]["link"] . '" target="_blank" title="点击前往YouTube观看">
                            <div><b>' . $data["is-casting"]["title"] . '</b></div>
                        </a>
                        <p>' . $data["is-casting"]["description"] . '</p>
                </div>';
            }

            // 构建预定直播列表
            $willCastList = '<div id="will-cast-list">' . "\n";
            if($data["will-cast"]!=null)
            {
                foreach($data["will-cast"] as $live)
                {
                    preg_match("/(?<=watch\?v=)[a-zA-Z0-9-_]{1,}$/", $live["link"], $willCastId);
                    // if($DEBUG)
                    // {
                    //     var_dump($live["link"]);
                    //     var_dump($willCastId);
                    // }
                    $willCastId = $willCastId[0];
                    $willCast = 
                    '<div class="will-cast">
                            <!--<iframe src="' . $live["cover"] . '" width="246" height="138" scrolling="no" ></iframe>!-->
                            <div class="player-container">
                                <iframe class="player" src="https://www.youtube.com/embed/' . $willCastId . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <a href="https://www.youtube.com' . $live["link"] . '" target="_blank" title="点击前往YouTube观看">
                                <div><b>' . $live["title"] . '</b></div>
                            </a>
                            <p>' . $live["meta-data"] . '</p>
                    </div>';
                    $willCastList = $willCastList . $willCast . "\n";
                 }
            }
            $willCastList = $willCastList . "</div>";


            echo 
            '<div class="col-md-2 vtb-card m-2 p-0 rounded" data-toggle="modal" data-target="#' . $id . '">
                <div id="card" class="card" >
                    <img class="card-img-top m-auto" src="' . $data["head"] . '" alt="头像" />
                    <div class="card-body text-center">
                        <a class="card-title"><b>' . $data["name"] . '</b></a>
                        ' . $status . '
                    </div>
                </div>
            </div>
            <!-- 模态框 -->
            <div class="modal fade vtb-modal" id="' . $id . '">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                
                    <!-- 模态框头部 -->
                    <div class="modal-header">
                        <a class="vtb-link" href="' . $data["link"] . '"><h4 class="modal-title">' . $data["name"] . '</h4></a>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                
                    <!-- 模态框主体 -->
                    <div class="modal-body">
                        <a class="vtb-link" href="' . $data["link"] . '"><img id="head" src="' . $data["head"] . '" alt="头像"></a>
                        <hr  width="100%" color="#6f5499" size="3" />
                        ' . $isCasting . '
                        ' . $willCastList . '
                    </div>
                    </div>
                </div>
            </div>';
        }
    ?>
    <?php include 'components/nav.php' ?>
    <div class="container-fluid">
        <?php

            // 读取文件
            $filepath = "data/hololive.json";
            $data = file_get_contents($filepath);
            // if($DEBUG)
            // {
            //     echo $data;
            // }

            // 处理json
            $data = json_decode($data, true);
            $tagList = array("无印组", "一期生", "二期生", "Gamers", "三期生", "四期生");
        ?>
            <?php
                foreach($tagList as $tag)
                {
                    echo '<div class="row d-flex flex-row justify-content-around mb-3 mt3 flex-wrap">';
                    $generation = $data[$tag];
                    foreach($generation as $vtb)
                    {
                        makeCard($vtb);
                    }
                    echo '</div>';
                }
            ?>
    </div>
</body>
</html>