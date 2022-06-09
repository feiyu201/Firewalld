#Firewalld
#Firewalld

脚本执行方式:bash <(curl -Lso- hjy.sh)
yx.txt 需要加载的IP列表 至于拒绝还是允许你在脚本设置拒绝还是允许然后开启防火墙
version.txt  这个是控制版本号的 如果本地和服务器这边不一致会强制更新哦
网站的伪静态是nginx.conf  目录ip 是每次访问判断是否指定时间内的 不是就请求第三方 然后保存到本地

特别注意:宝塔会在安全那边 也就是添加端口那边 会自动加载 只要不点安全就行 否则哪怕你清空了库他也会在数据库遗留 这个是宝塔避免太多做缓存使用!

添加计划任务如下:
#开始
wget --no-check-certificate -P /www/wwwroot/XXXX/ip https://www.ipdeny.com/ipblocks/data/countries/all-zones.tar.gz
cd /www/wwwroot/XXXX/ip
tar -xvf all-zones.tar.gz
rm -f all-zones.tar.gz
chmod -R 777 /www/wwwroot/XXXX/ip
#结束
设置一天更新一次
记得修改网站根目录/www/wwwroot/XXXX


伪静态如下:
location /index.html{
		rewrite  ^/index.html$ /index.php last;   break;
}
location /all-zones.tar.gz{
		rewrite  ^/all-zones.tar.gz$ /ip/all-zones.tar.gz last;   break;
}
location /ip/ {
autoindex on;
}
location / {
	if (!-e $request_filename){
		rewrite  ^/(.*).html$ /index.php?dq=$1  last;   break;
	}
}
