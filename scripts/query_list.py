"""
Author: Kowaine
INPUT：xml文件路径
TODO: 查询指定xml文件中的所有链接的直播信息，保存为json
OUTPUT: 直播信息的json
eg:
{
    "分组名":
    [
        {
            "name": "フブキCh。白上フブキ", 
            "head": "https://yt3.ggpht.com/a/AATXAJwQ-d9UTMFsLifyHKfEGU-GILwojhtCEk8qEA=s100-c-k-c0xffffffff-no-rj-mo",
            "link": "https://youtube.com/xxxxx",
            "is-casting": 
            {
                "title": "xxx",
                "cover": "xxx",
                "description": "xxx",
                "link": "xxx"
            },
            "will-cast":
            [
                {
                    "title": "xxx",
                    "cover": "xxx",
                    "link": "xxx",
                    "meta-data": "xxx"
                },
                {
                    ...
                }
            ]
        },
        {
            ...
        }
    ],
    ...
}
"""

DEBUG = False
SCRIPT = "scripts/query.py"

import os, json, argparse, sys, multiprocessing, re
from xml.dom.minidom import parse
from xml.dom import minidom

def query_generation(filedom, tagname, path):
    generation = filedom.getElementsByTagName(tagname)[0]
    process_list = []
    for vtb in generation.getElementsByTagName("vtb"):
        if DEBUG:
            print(vtb.getElementsByTagName("link")[0].childNodes[0].data)
        cmd = "python " + path + SCRIPT + " --url " + vtb.getElementsByTagName("link")[0].childNodes[0].data + " --path " + path
        # os.system(cmd)
        # _thread.start_new_thread(os.system, cmd)
        temp_process = multiprocessing.Process(target=os.system, args=(cmd,))
        temp_process.start()
        process_list.append(temp_process)

    # 等待进程退出
    for process in process_list:
        process.join()


def read_generation(filedom, tagname, path):
    """ 分别读取每一分组的直播信息 """
    file_reg = re.compile("[^/]{1,}$")
    generation_info = []
    generation = filedom.getElementsByTagName(tagname)[0]
    for vtb in generation.getElementsByTagName("vtb"):
        link = vtb.getElementsByTagName("link")[0].childNodes[0].data
        filename = path + "data/" + file_reg.search(link).group() + ".json"
        with open(filename, "r", encoding="utf8") as f:
            generation_info.append(json.load(f))
    return generation_info
            


if __name__ == '__main__':
    """ 参数处理 """
    parser = argparse.ArgumentParser()
    parser.add_argument('--xml', type=str, default=None)
    parser.add_argument('--path', type=str, default="")
    args = parser.parse_args()
    args.xml.replace("\\", "/")
    args.path.replace("\\", "/")
    if args.xml == None:
        sys.stderr.write(r"ERROR: No target xml. 没有作为目标的xml文件。\n")
        sys.exit(-1)


    """ 读取dom """
    hololive_dom = minidom.parse(args.xml).documentElement


    """ 根据信息调用查询脚本 """
    tag_list = ["无印组", "一期生", "二期生", "Gamers", "三期生", "四期生"]
    process_list = []
    for tag in tag_list:
        # temp_process = multiprocessing.Process(target=query_generation, args=(hololive_dom, tag,))
        # temp_process.start()
        # process_list.append(temp_process)
        query_generation(hololive_dom, tag, args.path)

    # 等待子进程完成
    # for process in process_list:
    #     process.join()

    sys.stdout.write("信息查询完成。\n")

    """ 统合所有数据 """
    data = {}
    for tag in tag_list:
        data[tag] = read_generation(hololive_dom, tag, args.path)

    
    """ 将数据写入文件 """
    filename = args.path + "data/" + re.search("[^/\\\\]{1,}(?=\\.xml$)", args.xml).group() + ".json"
    with open(filename, "w", encoding="utf8") as f:
        json.dump(data, f)

    sys.stdout.write("信息统合完成。\n")
    
    

