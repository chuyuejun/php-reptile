<?php
/**
 * Created by PhpStorm.
 * User: chuyuejun
 * Date: 2019/1/11
 * Time: 15:50
 */
class ExportFile
{
    public function exportVipOrder()
    {
        set_time_limit(0);

        header ( "Content-type:application/vnd.ms-excel" );
        header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GB18030", date('Y-m-d',time()) ) . ".csv" );//导出文件名

        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        //$column_name = "id,操作内容,发生时间,管理员,管理员ID,IP,url,对应表,操作方法";
        $column_name = "id,公司名称,工作岗位,工作经验,学历,公司情况,公司人数,所在地区,薪资范围,福利,公司标签,工作标签,技能标签,firstType,secondType,thirdType";
        $column_name = explode(',',$column_name);
        // 将中文标题转换编码，否则乱码
        foreach ($column_name as $i => $v) {
            $column_name[$i] = iconv('utf-8', 'GB18030', $v);
        }
        // 将标题名称通过fputcsv写到文件句柄
        fputcsv($fp, $column_name);
        //$admin = new AdminLog();
        //$total_export_count = $admin->count();
        $pre_count = 50;
        $j=0;
        // for ($i=0;$i<intval($total_export_count/$pre_count)+1;$i++){
        //切割每份数据
        // $export_data = $admin->offset($i*$pre_count)->limit($pre_count)->get()->toArray();
        //导出拉勾ios招聘文件
        $export_data = require ('/Users/chuyuejun/img/ios.php');
        //整理数据
        foreach ( $export_data as &$val ) {
            $tmpRow = [];
            $tmpRow[] =++$j;
            $tmpRow[] =$val['companyFullName'];
            $tmpRow[] =$val['positionName'];
            $tmpRow[] =$val['workYear'];
            $tmpRow[] =$val['education'];
            $tmpRow[] =$val['financeStage'];
            $tmpRow[] =$val['companySize'];
            $tmpRow[] =$val['city'].$val['district'];
            $tmpRow[] =$val['salary'];
            $tmpRow[] =$val['positionAdvantage'];
            $tmpRow[] =implode(',',$val['companyLabelList']) ?? '';
            $tmpRow[] =implode(',',$val['positionLables'])?? '';
            $tmpRow[] =implode(',',$val['skillLables'])?? '';
            $tmpRow[] =$val['firstType'];
            $tmpRow[] =$val['secondType'];
            $tmpRow[] =$val['thirdType'];
            $rows = array();
            foreach ( $tmpRow as $export_obj){
                $rows[] = iconv('utf-8', 'GB18030', $export_obj);
            }
            fputcsv($fp, $rows);
        }
        // 将已经写到csv中的数据存储变量销毁，释放内存占用
        unset($export_data);
        ob_flush();
        flush();
        // }
        exit ();

    }
}