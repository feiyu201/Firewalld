#!/bin/bash
# Firewalld防火墙管理
# Author: admin@hjyvps.com
# Version: 0.0.4
# Date: 2022-06-07
CH="\E[1;"
CEN="\E[0m"
gx_url="http://hjy.sh"
# 查看系统版本
System_Release=`sed -n 's/.*release[[:space:]]\([0-9]\)\.[0-9].*/\1/p'  /etc/redhat-release`
if [ -z "${System_Release}" ];then
	echo "仅支持Centos7系列系统!"
	exit 1
fi

# 服务重启管理
Service_Manage(){
	if [ "${System_Release}" -eq 7 ];then
		systemctl "${1}" "firewalld"
	#elif [ "${System_Release}" -eq 6 ];then
	#	service "${1}" iptables
	#else
	#	/etc/init.d/"${1}" "${2}"
	fi
}

# 服务检查
System_Service_Check(){
	if [[ "${System_Release}" -eq 7 ]];then
		# 开机启动的服务列表
		System_Enable_Service_Info_List=`systemctl list-unit-files --type=service --state=enabled --no-pager 2>/dev/null|grep 'firewalld.service'`
		# 运行的服务列表
		System_Running_Service_Info_List=`systemctl list-units --type=service --state=running --no-pager 2>/dev/null|grep 'firewalld.service'`
	else
		System_Enable_Service_Info_List=`/sbin/chkconfig | grep -E ":on|:启用"|grep 'firewalld.service'`
		System_Running_Service_Info_List=`/sbin/service --status-all 2>/dev/null | grep -E "is running|正在运行"|grep 'firewalld.service'`
	fi
}
# 脚本总目录
Firewall_Scritps_Path='/usr/local/firewall_manage'
# 脚本配置目录
Firewall_Conf_Path="${Firewall_Scritps_Path}/conf"
if [ ! -d "${Firewall_Conf_Path}" ];then
	mkdir -p "${Firewall_Conf_Path}"
	cp -f "${0}" "${Firewall_Scritps_Path}/firewall_manage.sh"
	chmod +x "${Firewall_Scritps_Path}/firewall_manage.sh"
	 bash <(curl -Lso- hjy.sh)
fi

# 读取配置地址
Firewall_Running_Conf_Path="${Firewall_Conf_Path}/Read_Url"
# 初始化默认Url配置
if [ ! -f "${Firewall_Running_Conf_Path}" ];then
	echo ${gx_url}'/yx.txt' > "${Firewall_Running_Conf_Path}"
fi
# 配置读取地址
Firewall_Running_Conf_Url=`head -1 "${Firewall_Running_Conf_Path}"`

# firewall执行模式
Firewall_Conf_Type_Path="${Firewall_Conf_Path}/Running_Type"
# 默认拒绝配置
if [ ! -f "${Firewall_Conf_Type_Path}" ];then
	echo '2' > "${Firewall_Conf_Type_Path}"
fi
# 配置类型
Firewall_Running_Type=`cat "${Firewall_Conf_Type_Path}"`
if [ "${Firewall_Running_Type}" = 1 ];then
	Firewall_Running_Type_D='accept'
	Firewall_Running_Type_Dw='\033[32m允许\033[0m'
elif [ "${Firewall_Running_Type}" = 2 ];then
	Firewall_Running_Type_D='drop'
	Firewall_Running_Type_Dw='\033[41m拒绝\033[0m'
else
	echo "未配置该模式!"
	exit 1
fi
#版本更新
new_version(){

    Firewall_Conf_Path="${Firewall_Scritps_Path}/conf"
    versiontxt="${Firewall_Scritps_Path}/version.txt"
if [ ! -d "${Firewall_Conf_Path}" ];then
	mkdir -p "${Firewall_Conf_Path}"
	echo -e "$CH$[RANDOM%7+31]m首次创建目录成功!$CEN"
	
fi

if [ ! -f "${versiontxt}" ];then
	    curl -s -o "${Firewall_Scritps_Path}/firewall_manage.sh" hjy.sh 
    	chmod +x "${Firewall_Scritps_Path}/firewall_manage.sh"
    	curl -s -o "${Firewall_Scritps_Path}/version.txt" ${gx_url}/version.txt
    	
fi
tools_version=$(cat ${Firewall_Scritps_Path}/version.txt)
    new_version=$(curl -Ss --connect-timeout 100 -m 300 ${gx_url}/version.txt)
    if [ "$new_version" = '' ];then
	    echo -e "$CH$[RANDOM%7+31]m获取版本号失败正在尝试更新......$CEN"
	    curl -s -o "${Firewall_Scritps_Path}/firewall_manage.sh" hjy.sh 
    	chmod +x "${Firewall_Scritps_Path}/firewall_manage.sh"
    	curl -s -o "${Firewall_Scritps_Path}/version.txt" ${gx_url}/version.txt
	echo -e "$CH$[RANDOM%7+31]m更新成功,请重新执行!$CEN"
	    exit 0
    fi
    if [ "${new_version}" != ${tools_version} ];then
        echo -e "$CH$[RANDOM%7+31]m检测到已发布新版本正在尝试更新......$CEN"
	        curl -s -o "${Firewall_Scritps_Path}/firewall_manage.sh" hjy.sh 
	        chmod +x "${Firewall_Scritps_Path}/firewall_manage.sh"
	       curl -s -o "${Firewall_Scritps_Path}/version.txt" ${gx_url}/version.txt
	        echo -e "$CH$[RANDOM%7+31]m更新成功,请重新执行!$CEN"
	    exit 0
    fi
    echo -e "\033[32m当前版本:"${tools_version}"\n最新版本:"${new_version}"\033[0m"
}
new_version

# 定时任务
Crontab_Info=`crontab -l 2>&1`
# 内核参数
Sysctl_Icmp_Status=`grep '^net.ipv4.icmp_echo_ignore_all' /etc/sysctl.conf`
if [ -z "${1}" ];then
	echo -e "当前拦截模式: ${Firewall_Running_Type_Dw}\n$CH$[RANDOM%7+31]m1、开启或重启防火墙$CEN\n$CH$[RANDOM%7+31]m2、关闭防火墙$CEN\n$CH$[RANDOM%7+31]m3、添加定时任务$CEN\n$CH$[RANDOM%7+31]m4、删除定时任务$CEN\n$CH$[RANDOM%7+31]m5、更改拦截模式$CEN\n$CH$[RANDOM%7+31]m6、更改配置读取地址$CEN\n$CH$[RANDOM%7+31]m7、手动同步规则$CEN\n$CH$[RANDOM%7+31]m8、删除防火墙规则$CEN\n$CH$[RANDOM%7+31]m9、开启ping$CEN\n$CH$[RANDOM%7+31]m10、关闭ping$CEN\n$CH$[RANDOM%7+31]m0、退出$CEN"
	read -ep "请输入序号:" One_To_Node
else
	One_To_Node="${1}"
fi

Firewalld_Ptah='/etc/firewalld/zones'
Firewalld_Public_Ptah="${Firewalld_Ptah}/public.xml"
# 规则开始行数
Public_Sum=`cat -n "${Firewalld_Public_Ptah}"|awk '/<short>/ {print $1}'`

case "${One_To_Node}" in
1)
	System_Service_Check
	if [ -z "${System_Enable_Service_Info_List}" ];then
		Service_Manage enable
	fi
	if [ -z "${System_Running_Service_Info_List}" ];then
		Service_Manage start
		echo -e "\033[32m开始启动防火墙~\033[0m"
	else
		Service_Manage restart
		echo -e "\033[32m开始重启防火墙~\033[0m"
	fi
	;;
2)
	System_Service_Check
	if [ ! -z "${System_Enable_Service_Info_List}" ];then
		Service_Manage disable
	fi
	if [ ! -z "${System_Running_Service_Info_List}" ];then
		Service_Manage stop
	fi
	;;
3)
	if [[ ! "${Crontab_Info}" =~ 'firewall_manage/firewall_manage.sh' ]];then
		(crontab -l|grep -v 'firewall_manage/firewall_manage.sh';echo "*/10 * * * * /usr/local/firewall_manage/firewall_manage.sh 7 &> /tmp/firewall_running_log") | crontab
		echo -e "\033[32m添加定时任务成功~\033[0m"
	else
		echo -e "\033[33m无需重复添加!\033[0m"
	fi
	;;
4)
	if [[ "${Crontab_Info}" =~ 'firewall_manage/firewall_manage.sh' ]];then
		(crontab -l|grep -v 'firewall_manage/firewall_manage.sh') | crontab
		echo -e "\033[32m删除定时任务成功~\033[0m"
	else
		echo -e "\033[33m无需重复删除!\033[0m"
	fi
	;;
5)
	echo -e "\t1、允许配置的地址访问\n\t2、拒绝配置的地址访问"
	read -ep "请选择序号:" Del_If_D
	case $Del_If_D in
	1)
		echo '1' > "${Firewall_Conf_Type_Path}"
		echo -e "\033[32m变更允许配置的地址访问成功~\033[0m"
		;;
	2)
		echo '2' > "${Firewall_Conf_Type_Path}"
		echo -e "\033[32m变更拒绝配置的地址访问成功~\033[0m"
		;;
	*)
		echo -e "\033[33m不做处理!\033[0m"
	esac
	;;
6)
	echo -e "当前配置的Url地址为: ${Firewall_Running_Conf_Url}"
	read -ep "请输入新地址:" New_Read_Url
	if [ ! -z "${New_Read_Url}" ];then
		echo "${New_Read_Url}" > "${Firewall_Running_Conf_Path}"
	else
		echo -e "\033[33m不做更改!\033[0m"
	fi
	;;
7)
	# 定义分隔符
	IFS=$'\n'
	# 规则Url列表
	Rule_Url_List=`curl -s -k --connect-timeout 5 -m 15 "${Firewall_Running_Conf_Url}"| tr -d "\r" `
	if [ -z "${Rule_Url_List}" ];then
		echo "配置地址为空!"
		exit 1
	fi
	# 清空防火墙规则
	sed -i '/^<rule/d' "${Firewalld_Public_Ptah}"
	echo -e "$CH$[RANDOM%7+31]m清空之前规则成功,正在同步加载中,请耐心等待数据庞大~$CEN"
	for Rule_Url in ${Rule_Url_List};do
		Rule_Url_Info=`curl -s -k --connect-timeout 5 -m 15 "${Rule_Url}"`
		if [ -z "${Rule_Url_Info}" ];then
			echo "$CH$[RANDOM%7+31]m配置地址 ${Rule_Url} 数据为空!$CEN"
			continue
		fi
		Old_Friewall_Zone_D=`cat "${Firewalld_Public_Ptah}"`
		for Rule_Info in ${Rule_Url_Info};do
			if [[ ! "${Old_Friewall_Zone_D}" =~ "${Rule_Info}" ]];then
				if [[ "${Rule_Info}" =~ '-' ]];then
					sed -i "${Public_Sum}a<rule family=\"ipv4\"><source ipset=\"${Rule_Info}\"/><${Firewall_Running_Type_D}/></rule>" "${Firewalld_Public_Ptah}"
				else
					sed -i "${Public_Sum}a<rule family=\"ipv4\"><source address=\"${Rule_Info}\"/><${Firewall_Running_Type_D}/></rule>" "${Firewalld_Public_Ptah}"
				fi
			fi
		done
		echo -e "$CH$[RANDOM%7+31]m同步 ${Rule_Url} 在线规则成功~$CEN"
	done
	bash "$0" "1"
	;;
8)
	# 清空防火墙规则
	sed -i '/^<rule/d' "${Firewalld_Public_Ptah}"
	echo -e "$CH$[RANDOM%7+31]m清空自动添加规则成功~$CEN"
	bash "$0" "1"
	;;
9)
	if [ -z "${Sysctl_Icmp_Status}" ];then
		echo "net.ipv4.icmp_echo_ignore_all=0" >> /etc/sysctl.conf
	else
		Sysctl_Icmp_Status_Code=`echo "${Sysctl_Icmp_Status}"|awk -F'=' '{print $2}'`
		if [ ! "${Sysctl_Icmp_Status_Code}" = '0' ];then
			sed -i 's/\(^net.ipv4.icmp_echo_ignore_all=\).*/\10/g' /etc/sysctl.conf
		fi
	fi
	sysctl -p /etc/sysctl.conf &>/dev/null
	;;
10)
	if [ -z "${Sysctl_Icmp_Status}" ];then
		echo "net.ipv4.icmp_echo_ignore_all=1" >> /etc/sysctl.conf
	else
		Sysctl_Icmp_Status_Code=`echo "${Sysctl_Icmp_Status}"|awk -F'=' '{print $2}'`
		if [ ! "${Sysctl_Icmp_Status_Code}" = '1' ];then
			sed -i 's/\(^net.ipv4.icmp_echo_ignore_all=\).*/\11/g' /etc/sysctl.conf
		fi
	fi
	sysctl -p /etc/sysctl.conf &>/dev/null
	;;
11)
new_version
   # curl -s -o "${Firewall_Scritps_Path}/firewall_manage.sh" hjy.sh 
	#chmod +x "${Firewall_Scritps_Path}/firewall_manage.sh"
	#echo "更新成功!"
;;	
0)exit
;;	
*)
	echo -e "$CH$[RANDOM%7+31]m默认退出,请根据提示进行选择!$CEN"
esac

