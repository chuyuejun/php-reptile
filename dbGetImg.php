<?php
/*
 * 抓取豆瓣电影海报
 */
$img = new GetImg();

//豆瓣电影类型
$sort = [
    'U'=>'近期热门',
    'S'=>'评分最高',
    'R'=>'最新上映',
    'T'=>'标记最多',
];
//全部形式
$tags = ['电视剧','综艺','动漫','纪录片','短片'];
//全部类型
$genres = [
    '剧情','喜剧','动作','爱情','科幻','动画','悬疑','惊悚','恐怖','犯罪',
    '同性','音乐','歌舞','传记','历史','战争','西部','奇幻','冒险','灾难',
    '武侠','情色'
];
//全部地区
$countries = [
    '中国大陆','美国' ,'香港','台湾','日本'  ,'韩国','英国', '法国','德国', '意大利',
    '西班牙','印度','泰国','俄罗斯','伊朗','加拿大','澳大利亚','爱尔兰','瑞典','巴西','丹麦'
];

//可播放的
$playable = 1;
//我没看过的
$unwatched = 1;
//评分区间
$range = '0,10';
//存放地址
$file = '/Users/chuyuejun/img/douban/';
$a = $img->getDbImg(1,10,$genres[2],$file);
echo $a;
echo '<br>';
exit;

class GetImg
{
    public function getDbImg($starts, $last_page , $genres = '动作', $file = '')
    {
        if($file  == ''){
            return 'file error';
        }
        //类型
        $page = $starts;
        //$genres = '动作';
        $range = '0,10';
        $start = bcmul(20, bcsub($starts, 1));
        //1.初始化Curl
        $curl = curl_init();
        $url = 'https://movie.douban.com/j/new_search_subjects';

        $parameter = '?sort=U&range='.$range.'&start='.$start.'&genres='.$genres;
        $path_url = $url . $parameter;
        //设置curl传输选项
        curl_setopt($curl, CURLOPT_URL, $path_url);//访问ip地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//设置为FALSE 禁止 cURL 验证对等证书
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//设置为1或true时，获取的信息以字符串返回
        $data = curl_exec($curl); //返回值
        curl_close($curl);
        //把字符串装换为数组
        $res = json_decode($data, true);
        if ($res['data']) {
            foreach ($res['data'] as $v) {
                $this->GrabImage($v['cover'], $file.$page.'/', trim($v['title']));
            }
        }
        $page++;
        if ($page <= $last_page) {
            $this->getDbImg($page, $last_page, $genres ,$file);
        }

        return 'success';
        //return $this->returnSuccessResponse([]);
    }


    /**
     * 通过图片的远程url，下载到本地
     * @param: $url为图片远程链接
     * @param: $filename为下载图片后保存的文件名
     */
    public function GrabImage($url, $dir = '', $filename)
    {
        ob_clean();
        ob_start();
        readfile($url);        //读取图片
        $img = ob_get_contents();    //得到缓冲区中保存的图片
        ob_end_clean();        //清空缓冲区
        $dir_path = iconv("UTF-8", "GBK", $dir);
        if (!file_exists($dir_path)){
            mkdir ($dir_path,0777,true);
        }
        $img_path = $dir . $filename . '.jpg';
        //先生成文件
        fopen($img_path, "w");
        //写入图片
        $fp = fopen($img_path, 'w');
        if (fwrite($fp, $img)) {
            fclose($fp);
            return true;
        }
        return false;
    }
}

