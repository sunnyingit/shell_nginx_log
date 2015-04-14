#!/bin/bash
# 分析nginx日志文件
# 捕获到 类似于[.......]/place/(xxxxxxxx)[&test=test.......]的URL中的（xxxxxx）
LOG_DIR=m.log

# 存放最后生成的地图列表
M_LOG_DIR=m.access.log

# 查看系统 sed命令在MAC和linux下不一样
if [ -f "/etc/issue" ]; then 
    egrep -i "ubuntu" /etc/issue && sysName='ubuntu';
fi 

# 把uri=“place”的重定向到一个文件夹中
awk '/[^\/place]\/place.*/' ${LOG_DIR} > tmp.log

# 把用户搜索到的内容全部全部匹配出来
sed 's/[^GET]*GET \/place\(\/[a-z0-9\-]*\)[^HTTP]* HTTP.*/\1/g' tmp.log > tmp1.log

# 把符合匹配内容的字符重定向到另一个文件
awk '/^\//' tmp1.log > tmp2.log

if [ "$sysName" = "ubuntu" ]; then
    # 替换'/'
    sed -i 's/\// /g' tmp2.log
else
    # 替换'/'
    sed -i '' 's/\// /g' tmp2.log
fi;

#删除重复的行
sort -u tmp2.log --o ${M_LOG_DIR}

rm -rf tmp1.log tmp2.log tmp.log

#统计行数
wc -l ${LOG_DIR}

echo "DONE"


