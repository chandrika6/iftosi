<?php
include APICLUDE.'common/db.class.php';
class enquiry extends DB
{
        function __construct($db) 
        {
            parent::DB($db);
        }

        
        ## For fetching user details of customer
        
        /* 
         * Fetch Details of user from tbl_registration
         * fetch details of clicked Product according to product_id passed while clicking
         * 
         * Fill details using two arrays. 1-- udetail  2-- prod
         * 
         *  */

    public function filLog($params)
    {  
        $uid=$params['uid'];
        $udsql="SELECT 
                                logmobile,
                                user_name,
                                email
                FROM 
                                tbl_registration
                WHERE
                                user_id=".$uid."";
        $udres=$this->query($udsql);
        $chkres=$this->numRows($udres);
        if($chkres=1)
        {
            while($row1=$this->fetchData($udres)) 
            {
                $udetail['umobile']=$row1['logmobile'];
                $udetail['uname']=$row1['user_name'];
                $udetail['uemail']=$row1['email'];
            }
            
            $chksql="SELECT user_id FROM tbl_product_enquiry where user_id=".$uid." AND product_id=".$params['pid']." and vendor_id=".$params['pid'].""; 
            $chkres=$this->query($chksql);
            $numchk=$this->numRows($chkres);
                if($numchk<1)
                 {
                 $isql="INSERT
                 INTO 
                                    tbl_product_enquiry
                                   (user_id,
                                   user_name,
                                   user_mobile,
                                   user_email,
                                   product_id,
                                   vendor_id,
                                   type_flag,
                                   updatedby,
                                   date_time)
                   VALUES
                                (".$uid.",
                                \"".$udetail['uname']."\",
                                \"".$udetail['umobile']."\",    
                                \"".$udetail['uemail']."\",
                                \"".$params['pid']."\",
                                \"".$params['vid']."\",
                                  1,
                                 'customer',
                                  now())";
                $ires=$this->query($isql);
                if($ires)
                {
                    $arr="Log Entry is successfully completed";
                    $err=array('Code'=>0,'Msg'=>'Data inserted');
                }
                    else
                {
                    $arr=array();
                    $err=array('Code'=>1,'Msg'=>'Error in completing the operation');
                }
            }
        }
        $result=array('results'=>$arr,'error'=>$err);
        return $result;
    }
    
    # view log by vendor for his product being viewed
    
    public function viewLog($params)
    {
        $sql="SELECT silver_rate, gold_rate, dollar_rate FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res=$this->query($sql);
        if ($res) {
            $rates = $this->fetchData($res);
            $dollarValue=dollarValue;
            if(!empty($rates['dollar_rate']) && $rates['dollar_rate']!='0.00') {
                $dollarValue = $rates['dollar_rate'];
            }
            $goldRate=goldRate;
            if(!empty($rates['gold_rate']) && $rates['gold_rate']!='0.00') {
                $goldRate = $rates['gold_rate'];
            }
            $silverRate=silverRate;
            if(!empty($rates['silver_rate']) && $rates['silver_rate']!='0.00') {
                $silverRate = $rates['silver_rate'];
            }
        }
        
        
        # check the products under the requested vendor
        $page   = ($params['page'] ? $params['page'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 15);
        $viewprod=" SELECT
                                   user_id,
                                   user_name,
                                   user_mobile,
                                   user_email,
                                   product_id,
                                   vendor_id,
                                   user_ip_address,
                                   type_flag,
                                   updatedby,
                                   update_time
                    FROM
                                    tbl_product_enquiry
                    WHERE
                                    vendor_id=".$params['vid']."
                    ORDER BY
                                    update_time
                ";
        
        
        $viewres=$this->query($viewprod);
        $totalEnqs=$this->numRows($viewres);
        $arr['total_enqs']=$totalEnqs;
        $total_pages = ceil($totalEnqs/$limit);
        $arr['total_pages']=$total_pages;
        
        if (!empty($page))
        {
            $start = ($page * $limit) - $limit;
            $viewprod.=" LIMIT " . $start . ",$limit";
        }
        $viewres=$this->query($viewprod);
        $chkres=$this->numRows($viewres);
        if($chkres>0)
        {   
            while($row=$this->fetchData($viewres))
            {   
                $csql='SELECT barcode, product_name, prd_price FROM tbl_product_master WHERE product_id='.$row['product_id'].'';
                $cres=$this->query($csql);
                if($this->numRows($cres)>0) {
                    while($crow=$this->fetchData($cres))
                    {
                        $row['pro_dtls']=$crow;
                        $prdPrice = $crow['prd_price'];
                    }
                }
                $csql='SELECT gold_purity, gold_weight, metal FROM tbl_product_search WHERE product_id='.$row['product_id'].'';
                $cres=$this->query($csql);
                if($this->numRows($cres)>0) {
                    while($crow=$this->fetchData($cres))
                    {
                        $purity=$crow['gold_purity'];
                        $metal=strtolower($crow['metal']);
                        $weight=$crow['gold_weight'];
                    }
                }
                $csql='SELECT b.cat_name,b.catid FROM tbl_product_category_mapping AS a, tbl_category_master AS b WHERE a.product_id='.$row['product_id'].' AND b.catid=a.category_id AND b.p_catid in (0,10000,10001,10002)';
                $cres=$this->query($csql);
                if($this->numRows($cres)>0) {
                    while($crow=$this->fetchData($cres))
                    {
                        $row['pro_dtls']['cat_name'][]=$crow['cat_name'];
                        if($crow['catid']==10000 || $crow['catid']==10001 || $crow['catid']==10002) {
                            $row['pro_dtls']['mCatid']=$crow['catid'];
                            if($crow['catid']==10000) {
                                $row['pro_dtls']['prd_price']=$prdPrice*$dollarValue;
                            }
                            if($crow['catid']==10002) {
                                $metalRate=$silverRate;
                                if($metal=='gold') {
                                    $metalRate=$goldRate;
                                }
                                $finalRate=($metalRate)*($purity/995);
                                $row['pro_dtls']['prd_price']=$finalRate*$weight;
                            }
                        }
                    }
                }
                $arr['enq'][]=$row;
            }
            $err=array('Code'=>0,'Msg'=>'Values fetched successfully');
        }
        else
        {
            $arr=array('total_enqs' => $totalEnqs, 'total_pages' => $total_pages);
            $err=array('Code'=>0,'Msg'=>'No Values fetched');
        }
        $result=array('results'=>$arr,'error'=>$err);
        return $result;
    }    
}