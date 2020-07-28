<?php 
    $DEBUG = true;
?>
<!DOCTYPE html>
<html lang='zh'>
<head>
    <meta charset='UTF-8'>
    <title></title>
    <!-- 新 Bootstrap4 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
 
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    
    <!-- bootstrap.bundle.min.js 用于弹窗、提示、下拉菜单，包含了 popper.min.js -->
    <script src="https://cdn.staticfile.org/popper.js/1.15.0/umd/popper.min.js"></script>
    
    <!-- 最新的 Bootstrap4 核心 JavaScript 文件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

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
            '<div class="col-md-2 vtb-card" data-toggle="modal" data-target="#' . $id . '">
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
                        <h4 class="modal-title">' . $data["name"] . '</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                
                    <!-- 模态框主体 -->
                    <div class="modal-body">
                        <img id="head" src="' . $data["head"] . '" alt="头像">
                        <hr  width="100%" color="#6f5499" size="3" />
                        ' . $isCasting . '
                        ' . $willCastList . '
                    </div>
                    </div>
                </div>
            </div>';
        }
    ?>
    <nav class="navbar navbar-expand-sm bg-secondary navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Hololive生放送查询工具</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Kowaine/hololive-seihousou" target="_blank">项目地址@Github</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="modal" data-target="#document">部分说明</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/Kowaine" target="_blank">关于我@Github</a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- 模态框 -->
    <div class="modal fade" id="document">
                <div class="modal-dialog">
                    <div class="modal-content">
                
                    <!-- 模态框头部 -->
                    <div class="modal-header">
                        部分说明
                    </div>
                    <!-- 模态框主体 -->
                    <div class="modal-body">
                        1. 首先，本项目仅仅是个人兴趣而已，技术力有限，完成度不高。<br/>
                        2. 本质是拿Selenium做的爬虫，没用官方接口（GoogleAPI每天限额免费10000，然而每搜索一个频道都要消耗100，算了算压根不够用），所以效率不行，因此采用轮循方式每半个小时更新一次。之后会试试直接用原生request库，如果可以，大概会再更新一下脚本。<br/>
                        3. 其实早就有更好的工具了<a href="https://hololive.jetri.co/">Hololive Tools</a><br/>
                        4. <b>请到官方频道支持喜欢的vtb！</b>
                    </div>
                    </div>
                </div>
            </div>'
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
                    echo '<div class="row" style="flex-direction:row;justify-content:space-around;margin-bottom:1em;margin-top:1em;">';
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