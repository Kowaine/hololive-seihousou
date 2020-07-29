<?php 
    $DEBUG = false;
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

    <script src="./js/echarts.min.js"></script>
</head>
<body>
    <?php include 'components/nav.php' ?>
    <div class="container">
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
                foreach($tagList as $tag)
                {
                    $generation = $data[$tag];
                    echo '<div id="' . $tag . '" style="width:100%;height:30em;"></div>';
                    $names = array();
                    $liveCount = array();
                    foreach($generation as $vtb)
                    {
                        array_push($names, $vtb["name"]);
                        array_push($liveCount, count($vtb["lives"]));
                    }
                    
                    echo 
                    '<script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var tempChart = echarts.init(document.getElementById("' . $tag . '"));
            
                    // 指定图表的配置项和数据
                    var option = {
                        title: {
                            text: "' . $tag . '"
                        },
                        color: ["#3398DB"],
                        tooltip: {
                            trigger: "axis",
                            axisPointer: {            
                                type: "shadow"
                            }
                        },
                        legend: {
                            data:[]
                        },
                        xAxis: {
                            type: "category",
                            data: ' .  json_encode($names) . ',
                            axisTick: {
                                alignWithLabel: true
                            }
                        },
                        yAxis: {
                            minInterval: 1,
                            type: "value"
                        },
                        series: [{
                            name: "直播次数",
                            type: "bar",
                            barWidth: "60%",
                            data: ' .  json_encode($liveCount) . '
                        }]
                    };
            
                    // 使用刚指定的配置项和数据显示图表。
                    tempChart.setOption(option);
                    </script>';
                }
            ?>
    </div>
</body>
</html>