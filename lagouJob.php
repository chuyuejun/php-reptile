<?php
/*
 * 抓取拉勾招聘
 */

$job = new GetJob();
//职位
$jobs= [
    'php',
    'ui',
    'ios',
];
$a = $job->getLgJob(1,$jobs[2],20);
echo $a;
exit;

class GetJob
{
    public function getRandCode(){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-'
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );
        return $uuid;
    }

    public function getLgJob($page =1, $kd = 'php' , $last_page) {

        $district = '%E6%BB%A8%E6%B1%9F%E5%8C%BA';
        $url = 'https://www.lagou.com/jobs/positionAjax.json?px=default&city=%E6%9D%AD%E5%B7%9E&needAddtionalResult=false&first=false&pn='.$page.'&kd='.$kd;
        $path_url = $url;
        $post_data = [
            'first'=>false,
            'pn'=>1,
            'kd'=>'PHP',
        ];
        $cookie = 'Cookie: JSESSIONID='.$this->getRandCode().';
         _ga=GA1.2.558663646.1547100310;
         _gid=GA1.2.784003059.1547100310;
         user_trace_token='.$this->getRandCode().'; 
         LGUID='.$this->getRandCode().'; 
         index_location_city=%E6%9D%AD%E5%B7%9E
         TG-TRACK-CODE=index_hotsearch; 
         WEBTJ-ID='.$this->getRandCode().'; 
         Hm_lvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1547100310,1547170186,1547170191; 
         X_MIDDLE_TOKEN=0de486eb8a92e31462cd2828ca1482c8; 
         SEARCH_ID='.$this->getRandCode().';
         Hm_lpvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1547170218; 
         LGRID='.$this->getRandCode();
        $headers = [
            'Accept: application/json, text/javascript, */*; q=0.01',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: zh-CN,zh;q=0.9',
            'Connection: keep-alive',
            'Content-Length: 22',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            $cookie,
            'Host: www.lagou.com',
            'Origin: https://www.lagou.com',
            'Referer: https://www.lagou.com/jobs/list_PHP?px=default&city=%E6%9D%AD%E5%B7%9E',
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
            'X-Anit-Forge-Code: 0',
            'X-Anit-Forge-Token: None',
            'X-Requested-With: XMLHttpRequest',
        ];
        //1.初始化Curl
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_URL, $path_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);//设置为1或true时，是post请求，
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//post请求参数
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置为1或true时，获取的信息以字符串返回
        $output = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($output, true);
        if($res){
            //获取职位信息
            $job_info_list = $res['content']['positionResult']['result'];
            $job_info = [];
            $keys = bcmul(15,bcsub($page,1));
            foreach ($job_info_list as $key=>$val){
                if($page == 1){
                    $job_info[$key]  = $val;
                }else{
                    $job_info[$keys] = $val;
                    $keys ++;
                }
            }
        }else{
            $job_info = '';
        }
        $path_file = '/Users/chuyuejun/img/'.$kd.'.php';
        //处理数据
        $job_arr = require ($path_file);
        if(!empty($job_arr)){
            $job_info = var_export(array_merge($job_arr,$job_info),true);
        }else{
            $job_info = var_export($job_info,true);
        }
        $str = "<?php\nreturn ".$job_info."\n?>";
        file_put_contents($path_file,$str);
        //循环请求
        $page++;
        if ($page <= $last_page) {
            //没数据也直接结束
            if($job_info){
                //睡1秒,道德底线
                sleep(2);
                $this->getLgJob($page, $kd, $last_page);
            }else{
                return 'success';
            }
        }
        return 'success';
    }

}

