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
    os.system(CMD)