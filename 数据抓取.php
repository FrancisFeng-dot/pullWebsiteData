public function netquery(){
    $pro = nethtml("http://tianqi.2345.com/");
    $prodiv=$pro->find('div$class=\"clearfix custom\"')->find('a')->result;
    for ($l=0; $l < count($prodiv[0]); $l++){ 
        $province = nettext($prodiv[0][$l]);                         
        $provhref = nethref($prodiv[0][$l]);
        $content = nethtml("http://tianqi.2345.com/".$provhref);
        $content = $content->find('div$class=\"citychk\"')->find('dl')->find('dt')->find('a')->result;
        for ($o=0; $o < count($content);$o++) {
            for ($n=0; $n < count($content[$o]); $n++) { 
                $content[$o][$n] = str_replace('天气','',$content[$o][$n]);
                $city = nettext($content[$o][$n]);  
                $conthref = nethref($content[$o][$n]); 
                $cityhref = "http://tianqi.2345.com/".$conthref; 
                $insert = array('city'=>$city,'pid'=>$l+1,'collecttime'=>time(),'url'=>$cityhref);
                $result = Db::name('city')->insert($insert);
            }
        }
    }  
}   

public function netquery(){
    $hot = nethtml("http://tianqi.2345.com/shenzhen/59493.htm"); 
    $div=$hot->find("div");  
    $ali=$div->find('li')->deal();
    for ($j=0; $j < count($ali); $j++) { 
        for ($k=0; $k < count($ali[$j]); $k++) { 
            $tmparray = strpos($ali[$j][$k],"查看今日天气详情"); 
            if ($tmparray) {
                for ($l=0; $l < count($ali[$j]); $l++) { 
                    $title = nettext($ali[$j][$l]); 
                    $ltem=explode("℃",$title)[0];//拿掉℃
                    $htem=explode("～",$ltem);//获取最高温度
                    $length=strlen($htem[0]);//获取剩余字符串的长度
                    $ltem=(double)(substr($htem[0],$length-2,$length));//获取最低温度并且转为double类型
                    $htem=(double)($htem[1]);//转换为double类型
                    $vtem=($htem+$ltem)/2;
                    $wdate=explode("(",$title)[0];//获取当前天气日期
                    $time = date('Y-m-d H:i:s',time());
                    $year=((int)substr($time,0,4));//取得年份
                    $month=((int)substr($wdate,1,3));//取得月份
                    $day=((int)substr($wdate,6,8));//取得几号
                    $wdate=mktime(0,0,0,$month,$day,$year);
                    $insert = array('wdate' => $wdate,'lastupdate'=>time(),'htem'=>$htem,'ltem'=>$ltem,'vtem'=>$vtem);
                    $result = Db::name('weather')->insert($insert);
                }
                for ($m=0; $m < count($ali[$j+1]); $m++) { 
                    $title2 = nettext($ali[$j+1][$m]); 
                    $ltem2=explode("℃",$title2)[0];//拿掉℃
                    $htem2=explode("～",$ltem2);//获取最高温度
                    $length2=strlen($htem2[0]);//获取剩余字符串的长度
                    $ltem2=(double)(substr($htem2[0],$length2-2,$length2));//获取最低温度并且转为double类型
                    $htem2=(double)($htem2[1]);//转换为double类型
                    $vtem2=($htem2+$ltem2)/2;
                    $wdate2=explode("(",$title2)[0];//获取当前天气日期
                    $month2=((int)substr($wdate2,1,3));//取得月份
                    $day2=((int)substr($wdate2,6,8));//取得几号
                    $wdate2=mktime(0,0,0,$month2,$day2,$year);
                    $insert2 = array('wdate' => $wdate2, 'lastupdate'=>time(),'htem'=>$htem2,'ltem'=>$ltem2,'vtem'=>$vtem2);
                    $result = Db::name('weather')->insert($insert2);
                }
            }              
        }      
    }   
} 

function nethref($value){
    $href = explode("href=\"",$value);
    $realhref = explode("\"",$href[1])[0];
    return $realhref;
}
function nettext($value){
    $text = preg_replace("/\<.*?\>|\<.*?\>/", '', $value);
    return $text;
} 
function nethtml($url){
    $html=curl_get($url);
    header('content-Type:text/html;charset=utf-8');
    $html = mb_convert_encoding($html,'utf-8','gb2312'); 
    $query = new \org\Vquery($html); 
    return $query;
}  
function filehtml($url){
    header('content-Type:text/html;charset=utf-8');
    $html = file_get_contents($url); 
    $query = new \org\Vquery($html); 
    return $query;
}  
function curl_get($url, $gzip=false){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    if($gzip) curl_setopt($curl, CURLOPT_ENCODING, "gzip"); // 关键在这里
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

function curl_get2($url,$gzip=false,$data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 5);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    if($gzip) curl_setopt($curl, CURLOPT_ENCODING, "gzip"); // 关键在这里
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);
    if($data){
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($data)));
    }
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}


curl_init创建一个curl会话$ch = curl_init()；
curl_setopt（$ch, CURLOPT_URL, “http://www.google.com/”）传递一个URL路径给这个函数
CURLOPT_RETURNTRANSFER参数获得内容不输出，设为非0值或true
CURLOPT_FOLLOWLOCATION当你尝试获取一个PHP的页面，然后这个PHP的页面中有一段跳转代码 ,curl将从http://new_url获取内容，而不是返回跳转代码
CURLOPT_MAXREDIRS允许你定义跳转请求的最大次数
CURLOPT_AUTOREFERER curl会自动添加Referer header在每一个跳转链接
CURLOPT_CONNECTTIMEOUT 通常用来设置curl尝试请求链接的时间
CURLOPT_TIMEOUT用来设置curl允许执行的时间需求
 CURLOPT_USERAGENT，它允许你自定义请求是的客户端名称，比如webspilder或是IE6.0
CURLOPT_ENCODING  header中“Accept-Encoding: ”部分的内容，支持的编码格式为："identity"，"deflate"，"gzip"。如果设置为空字符串，则表示支持所有的编码格式
file_get_contents把整个文件读入一个字符串中
curl_exec将获取的内容打印出来
curl_close关闭会话

header('content-Type:text/html;charset=utf-8');
$html = mb_convert_encoding($html,'utf-8','gb2312'); 
mb_convert_encoding中文转换
header()函数的作用是给客户端发送头信息。

strpos查找字符第一次出现的位置
preg_replace替换
str_replace替换
deal()
result