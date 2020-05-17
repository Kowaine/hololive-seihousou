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
from selenium import webdriver
from selenium.webdriver.firefox.options import Options
from selenium.common import exceptions


if __name__ == '__main__':
    """ 参数处理 """
    parser = argparse.ArgumentParser()
    parser.add_argument('--url', type=str, default=None)
    args = parser.parse_args()
    if args.url == None:
        sys.stderr.write(r"ERROR: No target link. 没有作为目标的链接。\n")
        sys.exit(-1)


    """ 初始化 """
    # 无界面化
    browser = None
    profile = webdriver.FirefoxProfile()
    profile.set_preference('intl.accept_languages', 'zh_CN')
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


    """ 查询直播信息 """
    # 打开vtb空间
    browser.get(args.url)
    if DEBUG:
        # time.sleep(10)
        pass
    else:
        pass
    # print(browser.page_source)


    # 获取vtb信息
    channel_header = browser.find_element_by_id("channel-header-container")
    info["name"] = channel_header.find_element_by_css_selector("yt-formatted-string[class*='ytd-channel-name']").text
    info["head"] = channel_header.find_element_by_id("img").get_attribute("src")
    info["link"] = args.url

    # 获取正在直播信息
    is_casting = {}
    try:
        is_casting_container = browser.find_element_by_xpath("//a[@title='正在直播']/ancestor::ytd-item-section-renderer")
        if DEBUG:
            print("找到正在直播容器")
        is_casting["title"] = is_casting_container.find_element_by_id("video-title").get_attribute("title")
        is_casting["cover"] = is_casting_container.find_element_by_id("img").get_attribute("src")
        is_casting["description"] = is_casting_container.find_element_by_id("description-text").text
        is_casting["link"] = is_casting_container.find_element_by_id("video-title").get_attribute("href")
    except exceptions.NoSuchElementException:
        if DEBUG:
            sys.stdout.write(r"WARNING: No casting. " + info["name"] + " 没有正在进行的直播。\n")
        is_casting = None
    finally:
        info["is-casting"] = is_casting

    # 获取预定直播信息
    # time_reg = re.compile("^(?<=预定发布时间：)[.]{1,}$")
    will_cast = []
    try:
        will_cast_container = browser.find_element_by_xpath("//a[@title='即将进行的直播']/ancestor::ytd-item-section-renderer")
        if DEBUG:
            print("找到预定直播容器")
        will_cast_items = will_cast_container.find_elements_by_css_selector("#items > ytd-grid-video-renderer")
        for item in will_cast_items:
            item_temp = {}
            item_temp["title"] = item.find_element_by_id("video-title").get_attribute("title")
            item_temp["cover"] = item.find_element_by_id("img").get_attribute("src")
            item_temp["link"] = item.find_element_by_id("video-title").get_attribute("href")
            item_temp["meta-data"] = item.find_element_by_css_selector("#metadata-line > span").text
            will_cast.append(item_temp)
    except exceptions.NoSuchElementException:
        if DEBUG:
            sys.stdout.write(r"WARNING: No casting. " + info["name"] + " 没有预定的直播。\n")
        will_cast = None
    finally:
        info["will-cast"] = will_cast
    

    """ 保存到文件 """
    filename = "data/" + re.search("[^/]{1,}$", args.url).group() + ".json"
    with open(filename, "w+", encoding="utf8") as f:
        json.dump(info, f)


    """ 关闭 """
    if DEBUG:
        print(info)
        sys.stdout.write("信息查询完成。\n")
    browser.quit()