#!/bin/bash
# 分析nginx日志文件
# 捕获到 类似于[.......]/GET (xxxxxxxx)/userinfo[.......]的URL中的（xxxxxx）
LOG_DIR=r.log

# 存放最后生成的餐厅列表
R_LOG_DIR=r.access.log

# 查看系统 sed命令在MAC和linux下不一样
if [ -f "/etc/issue" ]; then 
    egrep -i "ubuntu" /etc/issue && sysName='ubuntu';
fi 

# 把用户搜索到的内容全部全部匹配出来
sed 's/.*GET \(.*\)\/userinfo.*/\1/g' $LOG_DIR > tmp.log

# 把符合匹配内容的字符重定向到另一个文件
awk '/^\//' tmp.log > tmp1.log

# MAC下面sed替换需要在前面加一个“‘’”
if [ "$sysName" = "ubuntu" ]; then
    # 替换'/'
    sed -i 's/\// /g' tmp1.log
else
    # 替换'/'
    sed -i '' 's/\// /g' tmp1.log
fi;

#删除重复的行
sort -u tmp1.log -o $R_LOG_DIR
rm -rf tmp1.log tmp.log

#统计行数

wc -l $R_LOG_DIR

echo "DONE"



