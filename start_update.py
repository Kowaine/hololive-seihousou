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
    temp_time = time.localtime(time.time())
    print("开始更新", "{}/{:0>2d}/{:0>2d} {:0>2d}:{:0>2d}:{:0>2d}".format(temp_time.tm_year, temp_time.tm_mon, temp_time.tm_mday, 
            temp_time.tm_hour, temp_time.tm_min, temp_time.tm_sec))
    os.system(CMD)
    temp_time = time.localtime(time.time())
    print("更新结束", "{}/{:0>2d}/{:0>2d} {:0>2d}:{:0>2d}:{:0>2d}".format(temp_time.tm_year, temp_time.tm_mon, temp_time.tm_mday, 
            temp_time.tm_hour, temp_time.tm_min, temp_time.tm_sec))
    print("---------------------------------------")
    # 计算还剩多久到达整点或30分
    now_minute = time.localtime(time.time()).tm_min
    sleep_minutes = INTERVAL - (now_minute % INTERVAL)
    print("{}分钟后开始每间隔{}分钟更新一次".format(sleep_minutes, INTERVAL))
    time.sleep(sleep_minutes * 60)

    while True:
        temp_time = time.localtime(time.time())
        print("开始更新", "{}/{:0>2d}/{:0>2d} {:0>2d}:{:0>2d}:{:0>2d}".format(temp_time.tm_year, temp_time.tm_mon, temp_time.tm_mday, 
                temp_time.tm_hour, temp_time.tm_min, temp_time.tm_sec))
        os.system(CMD)
        temp_time = time.localtime(time.time())
        print("更新结束", "{}/{:0>2d}/{:0>2d} {:0>2d}:{:0>2d}:{:0>2d}".format(temp_time.tm_year, temp_time.tm_mon, temp_time.tm_mday, 
                temp_time.tm_hour, temp_time.tm_min, temp_time.tm_sec))
        print("---------------------------------------")
        # 计算还剩多久到达整点或30分
        now_minute = time.localtime(time.time()).tm_min
        sleep_minutes = INTERVAL - (now_minute % INTERVAL)
        print("{}分钟后开始下一次更新".format(sleep_minutes))
        time.sleep(sleep_minutes * 60)