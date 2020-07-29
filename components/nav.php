<nav class="navbar navbar-expand-md bg-secondary navbar-dark sticky-top">
        <a class="navbar-brand" href="#">Hololive生放送查询工具</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-coll" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbar-coll" class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">主页</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/statistic.php">统计</a>
                </li>
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
</div>