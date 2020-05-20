import datetime, os, time

# 工作目录
PWD= os.getcwd() + "/"
# 启动程序
RUNNER = "python"
# 脚本位置
SCRIPT = PWD + "scripts/query_list.py"
# xml文件位置
XML = PWD + "config/hololive.xml"

PARAM = " --xml " + XML + " --path " + PWD
CMD = RUNNER + " " + SCRIPT + PARAM

# 执行时间间隔
INTERVAL = 30

if __name__ == "__main__":
    # 计算还剩多久到达整点或30分
    now_minute = time.localtime(time.time()).tm_min
    sleep_minutes = INTERVAL - (now_minute % INTERVAL)
    print("{}分钟后开始执行, 之后每间隔{}分钟执行一次".format(sleep_minutes, INTERVAL))
    time.sleep(sleep_minutes * 60)

    while True:
        os.system(CMD)
        time.sleep(INTERVAL)