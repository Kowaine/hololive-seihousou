"""
Author: Kowaine
INPUT：页面地址
TODO: 查询指定页面直播预定以及正在直播列表，保存为json
OUTPUT: 直播信息的json
eg:
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
    }
"""

DEBUG = False

if DEBUG:
    pass
else:
    pass

import time
import sys, json
import re, argparse
import requests
from bs4 import BeautifulSoup
from lxml import etree
from selenium import webdriver
from selenium.webdriver.firefox.options import Options


if __name__ == '__main__':
    """ 参数处理 """
    parser = argparse.ArgumentParser()
    parser.add_argument('--url', type=str, default=None)
    parser.add_argument('--path', type=str, default="")
    args = parser.parse_args()
    args.path.replace("\\", "/")
    if args.url == None:
        sys.stderr.write(r"ERROR: No target link. 没有作为目标的链接。\n")
        sys.exit(-1)

    """ 初始化 """
    # 无界面化
    browser = None
    profile = webdriver.FirefoxProfile()
    profile.set_preference('intl.accept_languages', 'zh_CN')
    profile.set_preference('permissions.default.image', 2)
    profile.set_preference('permissions.default.stylesheet', 2)
    if DEBUG:
        # browser = webdriver.Chrome()
        browser = webdriver.Firefox(profile)
    else:
        opts = Options()
        opts.add_argument("--headless")
        opts.add_argument("--disable-gpu")
        # opts.add_experimental_option('excludeSwitches', ['enable-automation'])
        # opts.add_argument('user-agent="Mozilla/5.0 (iPod; U; CPU iPhone OS 2_1 like Mac OS X; ja-jp) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5F137 Safari/525.20"')
        browser = webdriver.Firefox(profile, options=opts)
        
        # browser.minimize_window()
    

    # 构造数据体
    info = {
        "name": "",
        "head": "",
        "is-casting": {},
        "will-cast": []      
    }

    # # 构造headers
    # headers = {
    #     "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\
    #     (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36",
    #     ":authority": "www.youtube.com",
    #     "x-client-data": "CIe2yQEIpbbJAQjEtskBCKmdygEI/rzKAQjmxsoBCOfIygEItMvKAQ==",
    # }


    """ 查询直播信息 """
    # 打开vtb空间
    browser.get(args.url)
    res = browser.page_source
    browser.quit()
    if DEBUG:
        # time.sleep(10)
        pass
    else:
        pass
        # print(res)

    page = BeautifulSoup(res, features="lxml")
    page_etree = etree.HTML(res)


    # 获取vtb信息
    channel_header = page.select("#channel-header-container")[0]
    info["name"] = channel_header.select("yt-formatted-string[class*='ytd-channel-name']")[0].text
    info["head"] = channel_header.select("#img")[0].get("src")
    info["link"] = args.url

    # 获取正在直播信息
    is_casting = {}
    try:
        is_casting_container = page_etree.xpath("//span[contains(@class,'ytd-badge-supported-renderer') and (text()='正在直播')]/ancestor::ytd-item-section-renderer")[0]
        is_casting_container = BeautifulSoup(etree.tostring(is_casting_container), features="lxml")
        if DEBUG:
            print("找到正在直播容器")
        is_casting["title"] = is_casting_container.select("#video-title")[0].get("title")
        is_casting["cover"] = is_casting_container.select("#img")[0].get("src")        
        is_casting["description"] = is_casting_container.select("#description-text")[0].text
        is_casting["link"] = is_casting_container.select("#video-title")[0].get("href")
    except IndexError:
        if DEBUG:
            sys.stdout.write(r"WARNING: No casting. " + info["name"] + " 没有正在进行的直播。\n")
        is_casting = None
    finally:
        info["is-casting"] = is_casting

    # 获取预定直播信息
    # time_reg = re.compile("^(?<=预定发布时间：)[.]{1,}$")
    will_cast = []
    try:
        will_cast_container = page_etree.xpath("//a[@title='即将进行的直播']/ancestor::ytd-item-section-renderer")[0]
        will_cast_container = BeautifulSoup(etree.tostring(will_cast_container), features="lxml")
        if DEBUG:
            print("找到预定直播容器")
        will_cast_items = will_cast_container.select("ytd-grid-video-renderer") \
                if will_cast_container.select("ytd-grid-video-renderer") != [] \
                else will_cast_container.select("ytd-video-renderer")
        for item in will_cast_items:
            item_temp = {}
            item_temp["title"] = item.select("#video-title")[0].get("title")
            item_temp["cover"] = item.select("#img")[0].get("src")
            item_temp["link"] = item.select("#video-title")[0].get("href")
            item_temp["meta-data"] = item.select("#metadata-line > span")[0].text
            will_cast.append(item_temp)
    except IndexError:
        if DEBUG:
            sys.stdout.write(r"WARNING: No casting. " + info["name"] + " 没有预定的直播。\n")
        will_cast = None
    finally:
        info["will-cast"] = will_cast

    

    """ 保存到文件 """
    # vtb个人页面唯一id
    id_reg = re.compile("[^/]{1,}$")
    vtb_id = id_reg.search(args.url).group()
    filename = args.path + "data/" + vtb_id + ".json"
    with open(filename, "w", encoding="utf8") as f:
        json.dump(info, f)

    """ 保存到统计文件 """
    statistics_filename = args.path + "data/statistics/" + vtb_id + ".json"
    with open(statistics_filename, "w+", encoding="utf8") as f:
        data = {}
        try:
            data = json.load(f)
        except json.decoder.JSONDecodeError:
            if DEBUG:
                print("统计文件为空，重新记录数据")
            data = {}
        finally:
            # 根据id唯一性来去重保存直播
            if is_casting is not None:
                live_id = id_reg.search(is_casting["link"]).group()
                data[live_id] = is_casting
            # for live in will_cast:
            #     live_id = id_reg.search(live["link"])
            #     data[live_id] = live
            json.dump(data, f)


    if DEBUG:
        print(info)
        sys.stdout.write("信息查询完成。\n")
        