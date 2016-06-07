<?php
//include APICLUDE.'common/db.class.php';
class vendor extends DB
{
    function __construct($db)
    {
        parent::DB($db);
    }
    public function vendorList($params)
    {
        $page   = ($params['pgno'] ? $params['pgno'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 50);


        $total_products = 0;
        $sql = "SELECT *,date_format(profile_expiry_date,'%D %M, %Y') as expiry_show FROM tbl_vendor_master WHERE is_complete=2 ORDER BY date_time DESC";

        if (!empty($page)) {
            $start = ($page * $limit) - $limit;
            $sql.=" LIMIT " . $start . ",$limit";
        }
       // echo $sql;die;
        $res = $this->query($sql);
        $total = $this->numRows($res);

        $arr = array();
        if($res)
        {
            while($row = $this->fetchData($res))
            {
                if(empty($row['profile_expiry_date']) || $row['profile_expiry_date'] == '0000-00-00 00:00:00')
                {
                    $row['expiry_show'] = 'Not Available';
                }

                $arr[] = $row;
            }
            $results=array('vendors'=>$arr,"total_vendors"=>$total);
            $err = array('Code' => 0, 'Msg' => 'Vendor details fetched successfully!');
        }
        else {
            $err = array('Code' => 1, 'Msg' => 'Something went wrong');
        }
        //  echo "<pre>";print_r($arr);die;
        $result = array('results' => $results, 'error' => $err);
        return $result;
    }

    public function getVendorBySearch($params)
    {
        $page   = ($params['pgno'] ? $params['pgno'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 50);


        $total_vendors = 0;

        $sql = "    SELECT
                                *,
                                date_format(profile_expiry_date,'%D %M, %Y') as expiry_show
                    FROM
                                tbl_vendor_master
                    WHERE
                                (
                                    MATCH(orgName) AGAINST('" . urldecode($params['srchTxt']) . "*' IN BOOLEAN MODE)
                    OR
                                    MATCH(email) AGAINST('" . urldecode($params['srchTxt']) . "*' IN BOOLEAN MODE)
                    OR
                                    MATCH(contact_mobile) AGAINST('" . urlencode($params['srchTxt']) . "*' IN BOOLEAN MODE)
                    OR
                                    MATCH(city) AGAINST('" . urlencode($params['srchTxt']) . "*' IN BOOLEAN MODE)
                                )
                    AND
                                    is_complete = 2
                    ORDER BY
                                date_time
                    DESC";

        if (!empty($page)) {
            $start = ($page * $limit) - $limit;
            $sql.=" LIMIT " . $start . ",$limit";
        }

        $res = $this->query($sql);
        $total = $this->numRows($res);

        $arr = array();
        if($res)
        {
            while($row = $this->fetchData($res))
            {
                if(empty($row['profile_expiry_date']) || $row['profile_expiry_date'] == '0000-00-00 00:00:00')
                {
                    $row['expiry_show'] = 'Not Available';
                }

                $arr[] = $row;
            }
            $results=array('vendors'=>$arr,"total_vendors"=>$total);
            $err = array('Code' => 0, 'Msg' => 'Vendor details fetched successfully!');
        }
        else {
            $err = array('Code' => 1, 'Msg' => 'Something went wrong');
        }

        $result = array('results' => $results, 'error' => $err);
        return $result;
    }

    public function addVendorPrdInfo($params)
    {
        $dt= json_decode($params['dt'],1);
        $detls  = $dt['result'];

        $sql="SELECT city from tbl_vendor_master where active_flag=1 and vendor_id=\"".$detls['vid']."\"";
        $res=$this->query($sql);
        $row=$this->fetchData($res);
        $city=$row['city'];


        $sql="INSERT INTO tbl_vendor_product_mapping(product_id,vendor_id,vendor_price,vendor_quantity,vendor_currency,vendor_remarks,city,active_flag,updatedby,date_time)";
        $sql.="VALUES(".$detls['pid'].",".$detls['vid'].",".$detls['vp'].",".$detls['vq'].",'".$detls['vc']."','".$detls['vr']."',".$city.",".$detls['af'].",'vendor',now())";
        $res = $this->query($sql);
        if($res)
        {
            $arr="Product mapping is done successfully";
            $err = array('Code' => 0, 'Msg' => 'Product details added successfully!');
        }
        else
        {
            $arr=array();
            $err = array('Code' => 1, 'Msg' => 'Product details Not Added!');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function getOwnerCheck($params)
    {
        $sql="  SELECT
                        vendor_id
                FROM
                        tbl_vendor_product_mapping
                WHERE
                        product_id = \"".$params['pid']."\"
                AND
                        vendor_id=\"".$params['uid']."\"";

        $res=$this->query($sql);
        $row=$this->fetchData($res);

        if($row['vendor_id'] == $params['uid'])
        {
            $err = array('code' => 1, 'msg' => 'Product is of same vendor');
        }
        else
        {
            $err = array('code' => 0, 'msg' => 'Product and vendor are different');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function getVproducts($params)
    {
        $page   = ($params['page'] ? $params['page'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 15);


        $total_products = 0;

        $cnt_sql = "SELECT COUNT(1) as cnt FROM tbl_vendor_product_mapping  WHERE active_flag!=2 and vendor_id =".$params['vid'];
        $cnt_res = $this->query($cnt_sql); //checking number of products registered under vendor id provided

        $chkcnt=$this->numRows($cnt_res);
         if($chkcnt>0)
        {
            $vsql="select product_id,vendor_price,vendor_quantity,vendor_currency,city,active_flag from tbl_vendor_product_mapping where active_flag=1 or active_flag=0 and vendor_id=".$params['vid'];
            if (!empty($page))
            {
                $start = ($page * $limit) - $limit;
                $vsql.=" LIMIT " . $start . ",$limit";
            }
            $vres=$this->query($vsql);
            $prsql.=" LIMIT " . $start . ",$limit";

            $i=-1;
            $vpmap=array();
            while($row1=$this->fetchData($vres))
            {   $i++;
                $vpmap['product_id'][$i]=$row1['product_id'];
                $vpmap['vendor_price'][$i]=$row1['vendor_price'];
                $vpmap['vendor_quantity'][$i]=$row1['vendor_quantity'];
                $vpmap['vendor_currency'][$i]=$row1['vendor_currency'];
                $vpmap['vendor_city'][$i]=$row1['city'];
                $vpmap['active_flag'][$i]=$row1['active_flag'];
                $vmap[]=$vpmap;
            }
            $vmapProd=implode(',',$vmap[$i]['product_id']);

            $prsql="SELECT product_id,product_name,product_display_name,product_model,product_brand,prd_img,desname
                    FROM tbl_product_master WHERE product_id IN(".$vmapProd.") AND active_flag=1 or active_flag=0";
            $prsql.=" LIMIT " . $start . ",$limit";

            $pres=$this->query($prsql);
            $j=-1;
            $preslt=array();
            while ($prow = $this->fetchData($pres))
            {       $j++;

                    $preslt['product_name'][$j]         = $prow['product_name'];
                    $preslt['product_display_name'][$j] = $prow['product_display_name'];
                    $preslt['product_model'][$j]        = $prow['product_model'];
                    $preslt['product_brand'][$j]        = $prow['product_brand'];
                    $preslt['product_image'][$j]        = $prow['prd_img'];
                    $preslt['desname'][$j]              = $prow['desname'];
                    $presults[] = $preslt;
            }
            if($cnt_res)
            {
                $cnt_row = $this->fetchData($cnt_res);
                if($cnt_row && !empty($cnt_row['cnt']))
                {
                $total_products = $cnt_row['cnt'];
                }
            }
            $arr=array('productdet'=>$presults[$j],'vendor_prod'=>$vmap[$i],'total_products'=>$total_products);
            $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
          }
             else
             {
                $arr=array();
                $err=array('Code'=>1,'Msg'=>'Error in fetching data');
             }
            $result = array('results'=>$arr,'error'=> $err);
            return $result;
    }


    public function getVProductsByBcode($params)
    {
        $page   = ($params['page'] ? $params['page'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 15);
        $catid = ($params['catid'] ? $params['catid'] : 10000);
        $total_pages = $chkcnt = $total_products = 0;

        $sql1="SELECT "
                . "silver_rate, "
                . "gold_rate, "
                . "dollar_rate "
                . "FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res1=$this->query($sql1);
        if ($res1) {
            $rates = $this->fetchData($res1);
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

        $chkpidsinvid="SELECT product_id FROM tbl_vendor_product_mapping WHERE vendor_id=".$params['vid']."";
        $respidsinvid=$this->query($chkpidsinvid);
        $chkcnt=$this->numRows($respidsinvid);
        if($chkcnt>0)
        {
            $k=0;
            while($row=$this->fetchData($respidsinvid))
            {
                $pdet1[]=$row['product_id'];
                $prDetails[]=$pdet;
            }
            $pId = implode(',',$pdet1);
            $psql='';
            if($catid == 10000) {
                $psql='d.color, d.carat, d.shape, d.certified AS cert, d.clarity,d.price,d.b2b_price';
            } else if($catid == 10001) {
                $psql='d.shape,d.metal,c.lotref,d.price,d.gold_weight,d.dwt as dwt';
            } else if($catid == 10002) {
                $psql='d.type, d.metal,d.bullion_design as bullion_design,d.gold_purity as gold_purity, d.gold_weight as gold_weight';
            }
                $sql = "select
                                    DISTINCT a.product_id
                AS id,
                                    c.product_name,
                                    a.vendor_price
                AS price,
                                    c.barcode,
                                    c.update_time,
                                    c.active_flag,
                                    ".$psql."
                FROM
                                    tbl_vendor_product_mapping
                AS a,
                                    tbl_product_category_mapping
                AS b,
                                    tbl_product_master
                AS c,
                                    tbl_product_search
                AS d
                where
                                    a.product_id=b.product_id
                AND
                                    a.product_id=c.product_id
                AND
                                    a.product_id=d.product_id
                AND
                                    b.category_id = " . $catid . "
                AND
                                    a.vendor_id=" . $params['vid'] . "
                AND
                    (
                        MATCH(c.barcode) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(d.shape) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        d.color LIKE '" . $params['bcode'] . "%'
                OR
                        MATCH(d.clarity) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(dwt) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(d.metal) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(d.carat) AGAINST('".$params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        d.price LIKE '".$params['bcode']."%'
                OR
                        d.b2b_price LIKE '".$params['bcode']."%'
                OR
                        MATCH(d.type) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(bullion_design) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(gold_purity) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        MATCH(gold_weight) AGAINST('" . $params['bcode'] . "*' IN BOOLEAN MODE)
                OR
                        d.certified LIKE '" . $params['bcode'] . "%'
                OR
                        d.product_id = '".$params['bcode']."'
                    )
                AND
                        d.active_flag <> 2
                AND
                        a.product_id IN(".$pId.")
                ORDER BY
                                    id";
		$res = $this->query($sql);
		$total_products = $this->numRows($res);
        if (!empty($page))
        {
            $start = ($page * $limit) - $limit;
            $sql.=" LIMIT " . $start . ",$limit";
        }
        $res = $this->query($sql);

        if($total_products>0)
        {
            $j=0;
            while ($row1=$this->fetchData($res))
            {
                if ($catid == 10001) {
                    $csql="SELECT p_catid,cat_name FROM tbl_category_master WHERE catid in (SELECT category_id FROM `tbl_product_category_mapping` WHERE `product_id`=".$row1['id']." ) ORDER BY p_catid DESC";
                    $cres = $this->query($csql);
                    if($this->numRows($cres)>0) {
                        $row2=array();
                        while ($crow = $this->fetchData($cres)) {
                            array_push($row2, $crow);
                        }
                        $row1['category']=$row2;
                    }
                }
                if ($catid == 10000) {
                    $price= $row1['price'];
                    $priceb2b= $row1['b2b_price'];
                }
                if ($catid == 10001) {
                    $price1 = ceil($row1['price']);
                    $price = $this->IND_money_format($price1);
                }
                if ($catid == 10002)
                {
                    $purity=$row1['gold_purity'];
                    $metal=strtolower($row1['metal']);
                    $weight=$row1['gold_weight'];

                    $metalRate=$silverRate;

                    if($metal=='gold')
                    {
                        $metalRate=$goldRate;
                        $rate = ((($metalRate/995)*$purity)/10)*$weight;
                        //$metalRate=$goldRate;
                        //$finalRate=($metalRate/10)*($purity/995);
                        $price1 = ceil($rate);
                        $price = $this->IND_money_format($price1);
                    }
                    else if($metal=='silver')
                    {
                        $metalRate = $silverRate;
                        $finalRate = ($metalRate/1000)*($purity/999);
                        $price1 = ceil($finalRate*$weight);
                        $price = $this->IND_money_format($price1);
                    }
                }
                $arr1[$j]['id']=$row1['id'];
                $arr1[$j]['color']=$row1['color'];
                $arr1[$j]['carat']=$row1['carat'];
                $arr1[$j]['shape']=$row1['shape'];
                $arr1[$j]['cert']=$row1['cert'];
                $arr1[$j]['clarity']=$row1['clarity'];
                $arr1[$j]['shape']=$row1['shape'];
                $arr1[$j]['metal']=$row1['metal'];
                $arr1[$j]['lotref']=$row1['lotref'];
                $arr1[$j]['gold_weight']=$row1['gold_weight'];
                $arr1[$j]['dwt']=$row1['dwt'];
                $arr1[$j]['type']=$row1['type'];
                $arr1[$j]['gold_purity']=$row1['gold_purity'];
                $arr1[$j]['gold_weight']=$row1['gold_weight'];
                $arr1[$j]['update_time']=$row1['update_time'];
                $arr1[$j]['active_flag']=$row1['active_flag'];
                $arr1[$j]['price']=$price;
                $arr1[$j]['b2b_price']=$priceb2b;
                $arr1[$j]['product_name']=$row1['product_name'];
                $arr1[$j]['barcode']=$row1['barcode'];
                $arr1[$j]['bullion_design']=$row1['bullion_design'];


                $j++;
            }
            $arr = array('total_products' => $total_products, 'total_pages' => $total_pages, 'products' => $arr1);
            $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
        }
        else
        {
            $arr=array('total_products' => $total_products, 'total_pages' => $total_pages);
            $err = array('Code' => 0, 'Msg' => 'No Match Found');
        }

     }
    else
    {
        $arr = array('total_products' => $total_products, 'total_pages' => $total_pages);
        $err = array('Code' => 0, 'Msg' => 'No Match Found');
    }

        $result = array('results' => $arr,'error' => $err);
        return $result;
    }

    public function IND_money_format($money)
    {
        $len = strlen($money);
        $m = '';
        $money = strrev($money);
        for($i=0;$i<$len;$i++)
        {
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i != $len)
            {
                $m .=',';
            }
            $m .=$money[$i];
        }
        return strrev($m);
    }


    public function updateProductInfo($params)
    {
        $sql = "UPDATE tbl_vendor_product_mapping SET vendor_price=".$params['vp'].",
                vendor_quantity=".$params['vq'].",updatedby='vendor',
                active_flag=".$params['af']." WHERE vendor_id=".$params['vid']." AND
                product_id=".$params['pid']."";
        $res=$this->query($sql);

        if($res)
        {
            $arr="Vendor Product Map table updated";
            $err = array('Code' => 0, 'Msg' => 'Details updated successfully');
        }
        else
        {
            $arr=array();
            $err = array('Code' => 1, 'Msg' => 'Update unsuccessfull');
        }
        $result = array('results'=>$arr,'error'=>$err);
        return $result;
    }

    public function getVDetailByVidPid($params)
    {
        $page   = ($params['page'] ? $params['page'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 15);

        $sql1="SELECT * FROM tbl_vendor_product_mapping where product_id=".$params['pid']." AND vendor_id=".$params['vid']." AND active_flag=1 ORDER BY date_time ASC";
        if (!empty($page))
        {
            $start = ($page * $limit) - $limit;
            $sql1.=" LIMIT " . $start . ",$limit";
        }
        $res1=$this->query($sql1);
        $chkcnt=$this->numRows($res1);
        if($chkcnt>0)
        {
            while($row=$this->fetchData($res1))
            {
                $vdet['vendor_price']=$row['vendor_price'];
                $vdet['vendor_quantity']=$row['vendor_quantity'];
                $vdet['vendor_currency']=$row['vendor_currency'];
                $vdet['vendor_remarks']=$row['vendor_remarks'];
                $vdet['vendor_city']=$row['city'];
                $vdetls[]=$vdet;
            }
            $sql2="SELECT
                            orgName as OrganisationName,
                            fulladdress,
                            postal_code,
                            telephones,
                            alt_email,
                            officecity as office_city,
                            officecountry as office_country,
                            contact_person,
                            position,
                            contact_mobile,
                            email,
                            memship_Cert as Membership_Certificate,
                            bdbc as bharat_Diamond_Bource_Certificate,
                            other_bdbc as other_Bharat_Diamond_Bource_Certificate,
                            vatno as Vat_Number,
                            landline,
                            mdbw as membership_of_other_diamond_bourses_around_world,
                            website,
                            banker as bankers,
                            pancard,
                            turnover,
                            lat as latitude,
                            lng as longitude

                    FROM
                                tbl_vendor_master
                    WHERE
                                vendor_id =".$params['vid']."";
            if (!empty($page))
            {
            $start = ($page * $limit) - $limit;
            $sql2.=" LIMIT " . $start . ",$limit";
            }

            $res2=$this->query($sql2);
            if($this->numRows($res2)>0)
            {
                while ($row1=$this->fetchData($res2))
                {
                    $vresult[] = $row1;
                }
                $arr=array('Vendor-Detail'=>$vresult,'Vendor-Product'=>$vdetls);
                $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
            }
            else
            {
                $arr=array();
                $err = array('Code' => 1, 'Msg' => 'No Match Found');
            }
        }
        else
        {
            $arr=array();
            $err = array('Code' => 0, 'Msg' => 'No Match Found');
        }
        $result = array('results'=>$arr,'error'=>$err);
        return $result;
    }

    public function getVDetailByPid($params)
    {
        $page   = ($params['page'] ? $params['page'] : 1);
        $limit  = ($params['limit'] ? $params['limit'] : 15);

        $sql1="SELECT * FROM tbl_vendor_product_mapping where product_id=".$params['pid']." active_flag=1 and ORDER BY date_time ASC";
        if (!empty($page))
        {
            $start = ($page * $limit) - $limit;
            $sql1.=" LIMIT " . $start . ",$limit";
        }
        $res1=$this->query($sql1);
        $chkcnt=$this->numRows($res1);
        if($chkcnt>0)
        {
            while($row=$this->fetchData($res1))
            {
                $vid[] = $row['vendor_id'];
                $vdet['vendor_id'] = $row['vendor_id'];
                $vdet['vendor_price']=$row['vendor_price'];
                $vdet['vendor_quantity']=$row['vendor_quantity'];
                $vdet['vendor_currency']=$row['vendor_currency'];
                $vdet['vendor_remarks']=$row['vendor_remarks'];
                $vdet['vendor_city']=$row['city'];
                $vdetls[]=$vdet;
            }

            $vid = implode(',',$vid);

            $sql2="SELECT
                            vendor_id AS vid,
                            orgName as OrganisationName,
                            fulladdress,
                            postal_code,
                            telephones,
                            alt_email,
                            officecity as office_city,
                            officecountry as office_country,
                            contact_person,
                            position,
                            contact_mobile,
                            email,
                            memship_Cert as Membership_Certificate,
                            bdbc as bharat_Diamond_Bource_Certificate,
                            other_bdbc as other_Bharat_Diamond_Bource_Certificate,
                            vatno as Vat_Number,
                            landline,
                            mdbw as membership_of_other_diamond_bourses_around_world,
                            website,
                            banker as bankers,
                            pancard,
                            turnover,
                            lat as latitude,
                            lng as longitude

                    FROM
                                tbl_vendor_master
                    WHERE
                                vendor_id IN(".$vid.")
                    ORDER BY
                                field(vendor_id,".$vid.")";
            if (!empty($page))
            {
            $start = ($page * $limit) - $limit;
            $sql2.=" LIMIT " . $start . ",$limit";
            }

            $res2=$this->query($sql2);
            if($this->numRows($res2) > 0)
            {
                while ($row2=$this->fetchData($res2))
                {
                    $arr1[$row2['vid']] = $row2;
                }
                $arr=array('Vendor-Detail'=>$arr1,'Vendor-Product'=>$vdetls);
                $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
            }
            else
            {
                $arr=array();
                $err = array('Code' => 1, 'Msg' => 'No Match Found');
            }
        }
        else
        {
            $arr=array();
            $err = array('Code' => 0, 'Msg' => 'No Match Found');
        }
        $result = array('results'=>$arr,'error'=>$err);
        return $result;
    }

    public function getVProductsByCatid($params)
    {
        global $comm;
        $page = ($params['page'] ? $params['page'] : 1);
        $catid = ($params['catid'] ? $params['catid'] : 10000);
        $limit = ($params['limit'] ? $params['limit'] : 15);
        $total_pages = $chkcnt = $total_products = 0;
        $sql="SELECT "
                . "silver_rate, "
                . "gold_rate, "
                . "dollar_rate "
                . "FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res=$this->query($sql);
        if ($res)
        {
            $rates = $this->fetchData($res);

            $dollarValue=dollarValue;
            if(!empty($rates['dollar_rate']) && $rates['dollar_rate']!='0.00')
            {
                $dollarValue = $rates['dollar_rate'];
            }

            $goldRate=goldRate;
            if(!empty($rates['gold_rate']) && $rates['gold_rate']!='0.00')
            {
                $goldRate = $rates['gold_rate'];
            }

            $silverRate=silverRate;
            if(!empty($rates['silver_rate']) && $rates['silver_rate']!='0.00')
            {
                $silverRate = $rates['silver_rate'];
            }
        }
       // $cate_ids=  $this->getSubCat($catid);
        $psql='';
        if($catid == 10000)
        {
            $psql='d.color, d.carat, d.shape, d.certified AS cert, d.clarity, d.b2b_price';
        }
        else if($catid == 10001)
        {
            $psql='d.shape,d.metal,c.lotref,d.gold_weight,d.dwt,d.gold_purity,d.certified as cert';
        }
        else if($catid == 10002)
        {
            $psql='d.type, d.metal, d.gold_purity, d.gold_weight,d.bullion_design';
        }

        $sql = "select
                                    DISTINCT a.product_id
                AS id,
                                    c.product_name,
                                    a.vendor_price
                AS price,
                                    c.barcode,
                                    c.update_time,
                                    c.active_flag,
                                    ".$psql."
                FROM
                                    tbl_vendor_product_mapping
                AS a,
                                    tbl_product_category_mapping
                AS b,
                                    tbl_product_master
                AS c,
                                    tbl_product_search
                AS d
                where
                                    a.product_id=b.product_id
                AND
                                    b.product_id=c.product_id
                AND
                                    c.product_id=d.product_id
                AND
                                    b.category_id = " . $catid . "
                AND
                                    a.vendor_id=" . $params['vid'] . "
                AND
                                    a.active_flag <> 2
                ORDER BY
                                    a.date_time
                DESC ";
        $res = $this->query($sql);

        $total_products = $this->numRows($res);
        if (!empty($params['page']))
        {
            $start = ($page * $limit) - $limit;
            $total_pages = ceil($total_products/$limit);
            $sql.=" LIMIT " . $start . ",$limit";
            $res = $this->query($sql);
            $chkcnt = $this->numRows($res);
        }
        if ($chkcnt > 0)
        {
            while ($row = $this->fetchData($res))
            {
                if ($catid == 10001)
                {
                    $csql=" SELECT
                                  p_catid,
                                  cat_name
                            FROM
                                  tbl_category_master
                            WHERE
                                  catid IN (SELECT category_id FROM `tbl_product_category_mapping` WHERE `product_id`=".$row['id'].")
                            ORDER BY
                                  p_catid
                            DESC";
                    $cres = $this->query($csql);
                    if($this->numRows($cres)>0)
                    {
                        $row1=array();
                        while ($crow = $this->fetchData($cres))
                        {
                            array_push($row1, $crow);
                        }
                        $row['category']=$row1;
                    }
                }
                if ($catid == 10000)
                {
                    $row['price']= $row['price'];
                }
                if ($catid == 10002)
                {
                    $purity=$row['gold_purity'];
                    $metal=strtolower($row['metal']);
                    $weight=$row['gold_weight'];

                    $metalRate=$silverRate;

                    if($metal=='gold')
                    {
                        $metalRate=$goldRate;
                        $rate = ((($metalRate/995)*$purity)/10)*$weight;
                        //$metalRate=$goldRate;
                        //$finalRate=($metalRate/10)*($purity/995);
                        $price = ceil($rate);
                        $row['price'] = $this->IND_money_format($price);
                    }
                    else if($metal=='silver')
                    {
                        $metalRate=$silverRate;
                        $finalRate=($metalRate/1000)*($purity/999);
                        $price=ceil($finalRate*$weight);
                        $row['price'] = $this->IND_money_format($price);
                    }
                }
                if($catid == 10001)
                {
                    $price = ceil($row['price']);
                    $row['price'] = $this->IND_money_format($price);
                }
                        $arr1[] = $row;
             }
            $arr = array('total_products' => $total_products, 'total_pages' => $total_pages, 'products' => $arr1);
            $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
        }
        else
        {
            $arr = array('total_products' => $total_products, 'total_pages' => $total_pages);
            $err = array('Code' => 1, 'Msg' => 'No Match Found');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function getVPendingProducts($params)
    {
      echo "HERE";
        global $comm;
        $page = ($params['page'] ? $params['page'] : 1);
        $catid = ($params['catid'] ? $params['catid'] : 10000);
        $limit = ($params['limit'] ? $params['limit'] : 15);
        $total_pages = $chkcnt = $total_products = 0;

        $sql="SELECT
                    silver_rate,
                    gold_rate,
                    dollar_rate
               FROM
                    tbl_vendor_master
               WHERE
                    vendor_id='".$params['vid']."'";

        $res=$this->query($sql,1);
        if ($res)
        {
            $rates = $this->fetchData($res);
            $dollarValue=dollarValue;

            if(!empty($rates['dollar_rate']) && $rates['dollar_rate']!='0.00')
            {
                $dollarValue = $rates['dollar_rate'];
            }
            $goldRate=goldRate;

            if(!empty($rates['gold_rate']) && $rates['gold_rate']!='0.00')
            {
                $goldRate = $rates['gold_rate'];
            }
            $silverRate=silverRate;

            if(!empty($rates['silver_rate']) && $rates['silver_rate']!='0.00')
            {
                $silverRate = $rates['silver_rate'];
            }
        }
       // $cate_ids=  $this->getSubCat($catid);
        $psql='';

        if($catid == 10000)
        {
            $psql='d.color, d.carat, d.shape, d.certified AS cert, d.clarity, d.b2b_price';
        }
        else if($catid == 10001)
        {
            $psql='d.shape,d.metal,c.lotref,d.gold_weight,d.dwt,d.gold_purity,d.certified as cert';
        }
        else if($catid == 10002)
        {
            $psql='d.type, d.metal, d.gold_purity, d.gold_weight,d.bullion_design';
        }

        $sql = "select
                        DISTINCT a.product_id
                AS id,
                        c.product_name,
                        a.vendor_price
                AS price,
                        c.barcode,
                        c.update_time,
                        c.active_flag,
                        ".$psql."
                FROM
                        tbl_vendor_product_mapping AS a,
                        tbl_product_category_mapping AS b,
                        tbl_product_master AS c,
                        tbl_product_search AS d
                where
                        a.product_id=b.product_id
                AND
                        b.product_id=c.product_id
                AND
                        c.product_id=d.product_id
                AND
                        b.category_id = " . $catid . "
                AND
                        a.vendor_id=" . $params['vid'] . "
                AND
                        a.active_flag <> 2
                ORDER BY
                        a.date_time
                DESC ";
        $res = $this->query($sql);

        $total_products = $this->numRows($res);
        if (!empty($params['page']))
        {
            $start = ($page * $limit) - $limit;
            $total_pages = ceil($total_products/$limit);
            $sql.=" LIMIT " . $start . ",$limit";
            $res = $this->query($sql);
            $chkcnt = $this->numRows($res);
        }
        if ($chkcnt > 0)
        {
            while ($row = $this->fetchData($res))
            {
                if ($catid == 10001)
                {
                    $csql=" SELECT
                                  p_catid,
                                  cat_name
                            FROM
                                  tbl_category_master
                            WHERE
                                  catid IN (SELECT category_id FROM `tbl_product_category_mapping` WHERE `product_id`=".$row['id'].")
                            ORDER BY
                                  p_catid
                            DESC";
                    $cres = $this->query($csql);

                    if($this->numRows($cres)>0)
                    {
                        $row1=array();
                        while ($crow = $this->fetchData($cres))
                        {
                            array_push($row1, $crow);
                        }
                        $row['category']=$row1;
                    }
                }
                if ($catid == 10000)
                {
                    $row['price']= $row['price'];
                    $price = ceil($row['price']);
                    $row['price'] = $this->IND_money_format($price);
                }
                if ($catid == 10002)
                {
                    $purity=$row['gold_purity'];
                    $metal=strtolower($row['metal']);
                    $weight=$row['gold_weight'];
                    $metalRate=$silverRate;

                    if($metal=='gold')
                    {
                        $metalRate=$goldRate;
                        $rate = ((($metalRate/995)*$purity)/10)*$weight;
                        $price = ceil($rate);
                        $row['price'] = $this->IND_money_format($price);
                    }
                    else if($metal=='silver')
                    {
                        $metalRate=$silverRate;
                        $finalRate=($metalRate/1000)*($purity/999);
                        $price=ceil($finalRate*$weight);
                        $row['price'] = $this->IND_money_format($price);
                    }
                }
                if($catid == '10000')
                {
                    if(empty($row['shape']) || empty($row['color']) || empty($row['clarity']) || empty($row['cut']) || empty($row['symmetry']) || empty($row['polish']) || empty($row['fluo']) || empty($row['certified']) || empty($row['cno']) || empty($row['certificate_url']) || empty($row['carat']) || empty($row['measurement']) || empty($row['tabl']) || empty($row['cr_ang']) || empty($row['girdle']) || empty($row['base']) || empty($row['p_disc']) || empty($row['p_discb2b']) || empty($row['b2b_price']))
                    {
                        $arr1[] = $row;
                        $cntDiamond++;
                    }
                }
                if($catid == '10002')
                {
                    if(empty($row['shape'])
                    {
                        $arr1[] = $row;
                        $cntBullion++;
                    }
                    else
                    {
                        if($row['shape'] == 'gCoins' || $row['shape'] == 'gBars')
                        {
                            if(empty($row['bullion_design'] || empty($row['gold_purity'] || empty($row['gold_weight'])
                            {
                                $arr2[] = $row;
                            }
                        }
                        else if($row['shape'] == 'sCoins' || $row['shape'] == 'sBars')
                        {
                            if(empty($row['bullion_design'] || empty($row['gold_purity'] || empty($row['gold_weight'])
                            {
                                $arr2[] = $row;
                            }
                        }
                        $cntBullion++;
                    }
                }
                $arr = array('total_products' => $total_products, 'total_pages' => $total_pages, 'Dproducts' => $arr1,'Bproducts' => $arr2,'Dcnt' => $cntDiamond,'Bcnt' => $cntBullion++;);
                $err = array('code' => 0, 'msg' => 'Details fetched successfully');
             }
        }
        else
        {
            $arr = array('total_products' => $total_products, 'total_pages' => $total_pages);
            $err = array('code' => 1, 'msg' => 'No Match Found');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function bulkInsertProducts($params)
    {
        $vid=$params['vid'];
        $data=$params['data'];
        $type=$params['type'];

        $defaultColNames = array
                                (
                                    "Stock #",
                                    "Availability",
                                    "Shape",
                                    "Weight",
                                    "Color",
                                    "Clarity",
                                    "Cut Grade",
                                    "Polish",
                                    "Symmetry",
                                    "Fluorescence Intensity",
                                    "Fluorescence Color",
                                    "Measurements",
                                    "Lab",
                                    "Report #",
                                    "Treatment",
                                    "Rapnet Price",
                                    "Rapnet  Discount %",
                                    "Cash Price",
                                    "Cash Price Discount %",
                                    "Fancy Color",
                                    "Fancy Color Intensity",
                                    "Fancy Color Overtone",
                                    "Depth %",
                                    "Table %",
                                    "Girdle Thin",
                                    "Girdle Thick",
                                    "Girdle %",
                                    "Girdle Condition",
                                    "Culet Size",
                                    "Culet Condition",
                                    "Crown Height",
                                    "Crown Angle",
                                    "Pavilion Depth",
                                    "Pavilion Angle",
                                    "Laser Inscription",
                                    "Cert Comment",
                                    "Country",
                                    "State",
                                    "City",
                                    "Is Matched Pair Separable",
                                    "Pair Stock #",
                                    "Allow RapLink Feed",
                                    "Parcel Stones",
                                    "Report Filename",
                                    "Diamond Image",
                                    "Sarine Loupe",
                                    "Trade Show",
                                    "Key to symbols",
                                    "Shade ",
                                    "Star Length",
                                    "Center Inclusion",
                                    "Black Inclusion",
                                    "Milky",
                                    "Member Comment",
                                    "Report Issue Date",
                                    "Report Type",
                                    "Lab Location",
                                    "Brand"
                                );

        $sql="
                SELECT
                        city
                FROM
                        tbl_vendor_master
                WHERE
                        vendor_id=\"".$vid."\"";

        $res=$this->query($sql);

        $row=$this->fetchData($res);

        $city=$row['city'];

        if($type=='csv')
        {
            $rdv = explode("\n", $data);
            $colName = explode(",", $rdv[0]);
            $len = count($rdv) - 1;
        }
        else
        {
            $rdv=$data;
            $colName=$data[0];
            $len = count($rdv);
        }

        $validFormat=TRUE;
        if (count($colName) == count($defaultColNames))
        {
            for ($i = 0; $i < count($defaultColNames); $i++)
            {
                if (strtoupper(trim($defaultColNames[$i])) != strtoupper(trim($colName[$i])))
                {
                    $validFormat = FALSE;
                }
            }
        }
        else
        {
            $validFormat = FALSE;
        }

        $i = $totlIns = 0;
        if ($validFormat)
        {
            while ($i < $len)
            {
                if($type=='csv')
                {
                    $value = explode(",", $rdv[$i]);
                }
                else
                {
                    $value=$rdv[$i];
                }

                if ($i != 0)
                {
                    $ts = date('Y-m-d H:i');
                    $query = "
                                INSERT
                                INTO
                                        `tbl_productid_generator`
                                        (
                                            `product_name`,
                                            date_time
                                        )
                                VALUES
                                        (
                                            'Diamond',
                                            '" . $ts . "'
                                        )";

                    $res = $this->query($query);
                    if ($res)
                    {

                        $b2b_price = $value[8]*(ltrim($value[12],'-')/100);

                        $pro_id = mysql_insert_id();
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_category_mapping`
                                            (
                                                product_id,
                                                category_id,
                                                price,
                                                b2bprice,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                     '10000',
                                                '" . $value[9] . "',
                                                '" . $b2b_price . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_master`
                                            (
                                                product_id,
                                                barcode,
                                                lotref,
                                                lotno,
                                                prd_price,
                                                b2bprice,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $value[0] . "',
                                                '" . $value[1] . "',
                                                '" . $value[2] . "',
                                                '" . $value[9] . "',
                                                '" . $b2b_price . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_vendor_product_mapping`
                                            (
                                                product_id,
                                                vendor_id,
                                                vendor_price,
                                                b2bprice,
                                                city,
                                                vendor_currency,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $vid . "',
                                                '" . $value[9] . "',
                                                '" . $b2b_price . "',
                                                '" . $city . "',
                                                    'USD',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);
                        $srch_val = "'" . $pro_id . "', ";
                        for ($j = 3; $j < count($value); $j++)
                        {
                            if($j==4)
                            {
                                $shape=$value[$j];
                                $value[$j]=$this->getAbbrValue($value[13]);
                            }

                            if($j>=13 && $j<=16)
                            {
                                $value[$j]=$this->getAbbrValue($value[$j]);
                            }

                            if($j == 11)
                            {
                                $value[$j] = str_replace('-', '', $value[$j]);
                            }

                            if($j == 12)
                            {
                                $value[$j] = str_replace('-', '', $value[$j]);
                            }

                            $srch_val .= "'" . $value[$j] . "', ";
                        }

                        $srch_val .="'".$shape."'";
                        $srch_val .=",'".$b2b_price."'";

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_search`
                                            (
                                                product_id,
                                                certified,
                                                cut,
                                                carat,
                                                color,
                                                clarity,
                                                base,
                                                price,
                                                value,
                                                p_disc,
                                                p_discb2b,
                                                prop,
                                                polish,
                                                symmetry,
                                                fluo,
                                                td,
                                                tabl,
                                                measurement,
                                                cno,
                                                pa,
                                                cr_hgt,
                                                cr_ang,
                                                girdle,
                                                pd,
                                                shape,
                                                b2b_price
                                            )
                                    VALUES
                                            (" . rtrim($srch_val, ', ') . ")";

                        $res = $this->query($sql);
                        $totlIns++;
                    }
                }
                $i++;
            }
            if ($res)
            {
                $totlRecrds = $len - 1;
                $arr = array
                            (
                                'suc' => $totlIns,
                                'fail' => $totlRecrds - $totlIns
                            );
                $err = array
                            (
                                'Code' => 0,
                                'Msg' => 'Products are updated Successfully'
                            );
            }
            else
            {
                $arr = array();
                $err = array
                            (
                                'Code' => 1,
                                'Msg' => 'Products are Failed to Update'
                            );
            }
        }
        else
        {
            $arr = array();
            $err = array
                        (
                            'Code' => 1,
                            'Msg' => 'Invalid File Format'
                        );
        }
        $result = array
                        (
                            'results' => $arr,
                            'error' => $err
                        );
        return $result;
    }

    public function uploadJewelleryProducts($params)
    {
        $vid=$params['vid'];
        $data=$params['data'];
        $type=$params['type'];
        $defaultColNames = array
                                (
                                    "Product details",
                                    "Design No",
                                    "Quantity",
                                    "Metal",
                                    "Metal Color",
                                    "Purity",
                                    "Gross Wt",
                                    "Net Wt",
                                    "Metal Amount",
                                    "Color Stone type",
                                    "Color Stone Wt",
                                    "Color stone value",
                                    "Diamonds",
                                    "Category",
                                    "Clarity",
                                    "Colour",
                                    "Carat",
                                    "No Of Diamond",
                                    "Value of Diamonds",
                                    "Labour",
                                    "Other",
                                    "Hallmark",
                                    "Certificate",
                                    "Collection Name",
                                    "Price"
                                );

        if($type=='csv')
        {
            $rdv = explode("\n", $data);
            $colName = explode(",", $rdv[0]);
            $len = count($rdv) - 1;
        }
        else
        {
            $rdv=$data;
            $colName=$data[0];
            $len = count($rdv);
        }
        $validFormat=TRUE;
        if(count($colName) == count($defaultColNames))
        {
            for ($i = 0; $i < count($defaultColNames); $i++)
            {
                if (strtoupper(trim($defaultColNames[$i])) != strtoupper(trim($colName[$i])))
                {
                    $validFormat = FALSE;
                }
            }
        }
        else
        {
            $validFormat = FALSE;
        }

        $i = $totlIns = 0;
        if ($validFormat)
        {
            while ($i < $len)
            {
                if($type=='csv')
                {
                    $value = explode(",", $rdv[$i]);
                }
                else
                {
                    $value=$rdv[$i];
                }
                if ($i != 0)
                {
                    $ts = date('Y-m-d H:i');
                    $query = "
                                INSERT
                                INTO
                                        `tbl_productid_generator`
                                        (
                                            `product_name`,
                                            date_time
                                        )
                                VALUES
                                        (
                                            'Jewellery',
                                            '" . $ts . "'
                                        )";

                    $res = $this->query($query);
                    if ($res)
                    {
                        $b2b_price = $value[8];
                        $pro_id = mysql_insert_id();
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_category_mapping`
                                            (
                                                product_id,
                                                category_id,
                                                price,
                                                b2bprice,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '10001',
                                                '" . $value[24] . "',
                                                '" . $b2b_price . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_vendor_product_mapping`
                                            (
                                                product_id,
                                                vendor_id,
                                                vendor_price,
                                                b2bprice,
                                                city,
                                                vendor_currency,
                                                vendor_quantity,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $vid . "',
                                                '" . $value[24] . "',
                                                '" . $b2b_price . "',
                                                '" . $city . "',
                                                'USD',
                                                '".$value[2]."',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_master`
                                            (
                                                product_id,
                                                prd_price,
                                                b2bprice,
                                                barcode,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $value[24] . "',
                                                '" . $b2b_price . "',
                                                '".$value[1]."',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_more_info`
                                    SET
                                            product_id          =   '".$pro_id."',
                                            product_details     =   '".$value[0]."',
                                            design_no           =   '".$value[1]."',
                                            quantity            =   '".$value[2]."',
                                            metal_color         =   '".$value[4]."',
                                            net_weight          =   '".$value[7]."',
                                            metal_amount        =   '".$value[8]."',
                                            color_stone_value   =   '".$value[11]."',
                                            diamonds            =   '".$value[12]."',
                                            no_of_diamonds      =   '".$value[17]."',
                                            value_of_diamonds   =   '".$value[18]."',
                                            labour_charge       =   '".$value[19]."',
                                            other               =   '".$value[20]."',
                                            hallmark            =   '".$value[21]."',
                                            collection_name     =   '".$value[23]."'";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_search`
                                    SET
                                            product_id      =   '".$pro_id."',
                                            metal           =   '".$value[3]."',
                                            gold_purity     =   '".$value[5]."',
                                            gold_weight     =   '".$value[6]."',
                                            gemstone_type   =   '".$value[9]."',
                                            gemwt           =   '".$value[10]."',
                                            shape           =   '".$value[13]."',
                                            clarity         =   '".$value[14]."',
                                            color           =   '".$value[15]."',
                                            carat           =   '".$value[16]."',
                                            certified       =   '".$value[22]."',
                                            price           =   '".$value[24]."'";

                        $res = $this->query($sql);
                        $totlIns++;
                    }
                }
                $i++;
            }
            if ($res)
            {
                $totlRecrds = $len - 1;
                $arr = array
                            (
                                'suc' => $totlIns,
                                'fail' => $totlRecrds - $totlIns
                            );
                $err = array
                            (
                                'Code' => 0,
                                'Msg' => 'Products are updated Successfully'
                            );
            }
            else
            {
                $arr = array();
                $err = array
                            (
                                'Code' => 1,
                                'Msg' => 'Products are Failed to Update'
                            );
            }
        }
        else
        {
            $arr = array();
            $err = array
                        (
                            'Code' => 1,
                            'Msg' => 'Invalid File Format'
                        );
        }
        $result = array
                        (
                            'results' => $arr,
                            'error' => $err
                        );
        return $result;
    }

    public function uploadBullionProducts($params)
    {
        $vid=$params['vid'];
        $city=$params['city'];
        $data=$params['data'];
        $type=$params['type'];
        $defaultColNames = array
                                (
                                    "Product Name",
                                    "Type",
                                    "Metal",
                                    "Gold Purity",
                                    "Gold Weight",
                                    "Silver Purity",
                                    "Silver Weight",
                                    "Bullion Design",
                                    "Quantity",
                                    "Design Number"
                                );

        if($type=='csv')
        {
            $rdv = explode("\n", $data);
            $colName = explode(",", $rdv[0]);
            $len = count($rdv) - 1;
        }
        else
        {
            $rdv=$data;
            $colName=$data[0];
            $len = count($rdv);
        }

        $validFormat=TRUE;

        if(count($colName) == count($defaultColNames))
        {
            for ($i = 0; $i < count($defaultColNames); $i++)
            {
                if (strtoupper(trim($defaultColNames[$i])) != strtoupper(trim($colName[$i])))
                {
                    $validFormat = FALSE;
                }
            }
        }
        else
        {
            $validFormat = FALSE;
        }

        $i = $totlIns = 0;
        if($validFormat)
        {
            while ($i < $len)
            {
                if($type=='csv')
                {
                    $value = explode(",", $rdv[$i]);
                }
                else
                {
                    $value=$rdv[$i];
                }
                if ($i != 0)
                {
                    $ts = date('Y-m-d H:i');
                    $query = "
                                INSERT
                                INTO
                                        `tbl_productid_generator`
                                        (
                                            `product_name`,
                                            date_time
                                        )
                                VALUES
                                        (
                                            'Bullion',
                                            '" . $ts . "'
                                        )";

                    $res = $this->query($query);
                    if ($res)
                    {
                        $pro_id = mysql_insert_id();
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_category_mapping`
                                            (
                                                product_id,
                                                category_id,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                     '10002',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);


                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_master`
                                            (
                                                product_id,
                                                barcode,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '".  $value[9]."',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_vendor_product_mapping`
                                            (
                                                product_id,
                                                vendor_id,
                                                city,
                                                vendor_currency,
                                                vendor_quantity,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $vid . "',
                                                '" . $city . "',
                                                     'USD',
                                                '".$value[8]."',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_master`
                                            (
                                                product_id,
                                                product_display_name,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $value[0] . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        if($value[1] == 'Coin' && $value[2] == 'Gold')
                        {
                            $shape = 'g'.$value[1].'s';
                            $value[3] = $value[3];
                            $value[4] = $value[4];

                        }
                        else if($value[1] == 'Coin' && $value[2] == 'Silver')
                        {
                             $sape = 's'.$value[1] .'s';
                             $value[3] = $value[5];
                             $value[4] = $value[6];

                        }
                        else if($value[1] == 'Bar' && $value[2] == 'Gold')
                        {
                             $sape = 'g'.$value[1] .'s';
                             $value[3] = $value[3];
                             $value[4] = $value[4];
                        }
                        else if($value[1] == 'Bar' && $value[2] == 'Silver')
                        {
                             $sape = 's'.$value[1] .'s';
                             $value[3] = $value[5];
                             $value[4] = $value[6];
                        }

                        $sql = "
                                    INSERT
                                    INTO
                                            tbl_product_search
                                    SET
                                            product_id      =   '".$pro_id."',
                                            type            =   '".$value[1]."',
                                            metal           =   '".$value[2]."',
                                            gold_purity     =   '".$value[3]."',
                                            gold_weight     =   '".$value[4]."',
                                            bullion_design  =   '".$value[7]."',
                                            shape           =   '".$shape."'";
                        $res = $this->query($sql);
                        $totlIns++;
                    }
                }
                $i++;
            }
            if ($res)
            {
                $totlRecrds = $len - 1;
                $arr = array
                            (
                                'suc' => $totlIns,
                                'fail' => $totlRecrds - $totlIns
                            );
                $err = array
                            (
                                'Code' => 0,
                                'Msg' => 'Products are updated Successfully'
                            );
            }
            else
            {
                $arr = array();
                $err = array
                            (
                                'Code' => 1,
                                'Msg' => 'Products are Failed to Update'
                            );
            }
        }
        else
        {
            $arr = array();
            $err = array
                        (
                            'Code' => 1,
                            'Msg' => 'Invalid File Format'
                        );
        }
        $result = array
                        (
                            'results' => $arr,
                            'error' => $err
                        );
        return $result;
    }

    public function uploadDiamondProducts($params) {
        $vid=$params['vid'];
        $data=$params['data'];
        $type=$params['type'];
        $defaultColNames = array
                                (
                                    'Stock #',
                                    'Availability',
                                    'Shape',
                                    'Weight',
                                    'Color',
                                    'Clarity',
                                    'Cut Grade',
                                    'Polish',
                                    'Symmetry',
                                    'Fluorescence Intensity',
                                    'Fluorescence Color',
                                    'Measurements',
                                    'Lab',
                                    'Report #',
                                    'Treatment',
                                    'Rapnet Price',
                                    'Rapnet  Discount %',
                                    'Cash Price',
                                    'Cash Price Discount %',
                                    'Fancy Color',
                                    'Fancy Color Intensity',
                                    'Fancy Color Overtone',
                                    'Depth %',
                                    'Table %',
                                    'Girdle Thin',
                                    'Girdle Thick',
                                    'Girdle %',
                                    'Girdle Condition',
                                    'Culet Size',
                                    'Culet Condition',
                                    'Crown Height',
                                    'Crown Angle',
                                    'Pavilion Depth',
                                    'Pavilion Angle',
                                    'Laser Inscription',
                                    'Cert Comment',
                                    'Country',
                                    'State',
                                    'City',
                                    'Is Matched Pair Separable',
                                    'Pair Stock #',
                                    "Allow RapLink Feed",
                                    "Parcel Stones",
                                    'Report Filename',
                                    'Diamond Image',
                                    'Sarine Loupe',
                                    'Trade Show',
                                    'Key to symbols',
                                    'Shade',
                                    'Star Length',
                                    'Center Inclusion',
                                    'Black Inclusion',
                                    'Milky',
                                    'Member Comment',
                                    'Report Issue Date',
                                    'Report Type',
                                    'Lab Location',
                                    'Brand',
                                    'b2b_price'
                                );

        if($type=='csv')
        {
            $rdv = explode("\n", $data);
            $colName = explode(",", $rdv[0]);
            $len = count($rdv) - 1;
        }
        else
        {
            $rdv=$data;
            $colName=$data[0];
            $len = count($rdv);
        }

        $validFormat=TRUE;
        if (count($colName) == count($defaultColNames))
        {
            for ($i = 0; $i < count($defaultColNames); $i++)
            {
                if (strtoupper(trim($defaultColNames[$i])) != strtoupper(trim($colName[$i])))
                {
                    $validFormat = FALSE;
                }
            }
        }
        else
        {
            $validFormat = FALSE;
        }
        $i = $totlIns = 0;
        if ($validFormat)
        {
            while ($i < $len)
            {
                if($type=='csv')
                {
                    $value = explode(",", $rdv[$i]);
                }
                else
                {
                    $value=$rdv[$i];
                }
                if ($i != 0)
                {
                    $ts = date('Y-m-d H:i');
                    $query = "  INSERT
                                INTO
                                        `tbl_productid_generator`
                                        (
                                            `product_name`,
                                            date_time
                                        )
                                VALUES
                                        (
                                            'Jewellery',
                                            '" . $ts . "'
                                        )";

                    $res = $this->query($query);
                    if ($res)
                    {
                        $b2b_price = $value[17] * (ltrim($value[18], '-') / 100);
                        $pro_id = mysql_insert_id();
                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_product_category_mapping`
                                            (
                                                product_id,
                                                category_id,
                                                price,
                                                b2bprice,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                    '10000',
                                                '" . $value[17] . "',
                                                '" . $value[15] . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "
                                    INSERT
                                    INTO
                                            `tbl_vendor_product_mapping`
                                            (
                                                product_id,
                                                vendor_id,
                                                vendor_price,
                                                b2bprice,
                                                city,
                                                vendor_currency,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $vid . "',
                                                '" . $value[17] . "',
                                                '" . $value[15] . "',
                                                '" . $city . "',
                                                    'USD',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "    INSERT
                                    INTO
                                            `tbl_product_master`
                                            (
                                                product_id,
                                                barcode,
                                                prd_price,
                                                b2bprice,
                                                date_time
                                            )
                                    VALUES
                                            (
                                                '" . $pro_id . "',
                                                '" . $value[13]."',
                                                '" . $value[17] . "',
                                                '" . $value[15] . "',
                                                '" . $ts . "'
                                            )";

                        $res = $this->query($sql);

                        $sql = "    INSERT
                                    INTO
                                            `tbl_product_search`
                                    SET
                                            product_id  = ".$pro_id.",
                                            shape       = '".$this->getShapeAbbrValue($value[2])."',
                                            carat       = '".$value[3]."',
                                            certified   = '".$value[12]."',
                                            color       = '".$value[4]."',
                                            clarity     = '".$value[5]."',
                                            cut         = '".$this->getAbbrValue($value[6])."',
                                            girdle      = '".$value[26]."',
                                            fluo        = '".$this->getAbbrValue($value[9])."',
                                            polish      = '".$this->getAbbrValue($value[7])."',
                                            symmetry    = '".$this->getAbbrValue($value[8])."',
                                            measurement = '".$value[11]."',
                                            tabl        = '".$value[23]."',
                                            cr_hgt      = '".$value[30]."',
                                            cr_ang      = '".$value[31]."',
                                            pd          = '".$value[32]."',
                                            pa          = '".$value[33]."',
                                            price       = '".$value[15]."',
                                            base        = '".$value[15]."',
                                            b2b_price   = '".$value[17]."',
                                            p_disc      = '".$value[16]."',
                                            p_discb2b   = '".$value[18]."'
                                ";

                        $res = $this->query($sql);


                        $sql = "    INSERT
                                    INTO
                                            `tbl_product_more_info`
                                    SET
                                            product_id              = ".$pro_id.",
                                            stock_no                = '".$value[0]."',
                                            availability            = '".$value[1]."',
                                            fluorescence_intensity  = '".$value[9]."',
                                            fluorescence_color      = '".$value[10]."',
                                            lab                     = '".$value[12]."',
                                            report_no               = '".$value[13]."',
                                            treatment               = '".$value[14]."',
                                            rapnet_price            = '".$value[15]."',
                                            rapnet_discount_percent = '".$value[16]."',
                                            cash_price              = '".$value[17]."',
                                            cash_price_discount_percent     = '".$value[18]."',
                                            fancy_color             = '".$value[19]."',
                                            fancy_color_intensity   = '".$value[20]."',
                                            fancy_color_overtone    = '".$value[21]."',
                                            depth_percent           = '".$value[22]."',
                                            girdle_thin             = '".$value[24]."',
                                            girdle_thick            = '".$value[25]."',
                                            girdle_percent          = '".$value[26]."',
                                            girdle_condition        = '".$value[27]."',
                                            culet_size              = '".$value[28]."',
                                            culet_condition         = '".$value[29]."',
                                            laser_inscription       = '".$value[34]."',
                                            certified               = '".$value[35]."',
                                            country                 = '".$value[36]."',
                                            state                   = '".$value[37]."',
                                            city                    = '".$value[38]."',
                                            matched_pair            = '".$value[39]."',
                                            pair_stock              = '".$value[40]."',
                                            allow_raplink_feed      = '".$value[41]."',
                                            parcel_stones           = '".$value[42]."',
                                            report_filename         = '".$value[43]."',
                                            diamond_image           = '".$value[44]."',
                                            sarine_loupe            = '".$value[45]."',
                                            trade_show              = '".$value[46]."',
                                            key_to_symbols          = '".$value[47]."',
                                            shade                   = '".$value[48]."',
                                            star_length             = '".$value[49]."',
                                            center_inclusion        = '".$value[50]."',
                                            black_inclusion         = '".$value[51]."',
                                            milky                   = '".$value[52]."',
                                            member_comment          = '".$value[53]."',
                                            report_date             = '".$value[54]."',
                                            report_type             = '".$value[55]."',
                                            lab_location            = '".$value[56]."',
                                            brand                   = '".$value[57]."'
                            ";

                        $res = $this->query($sql);
                        $totlIns++;
                    }
                }
                $i++;
            }
            if ($res) {
                $totlRecrds = $len - 1;
                $arr = array
                            (
                                'suc' => $totlIns,
                                'fail' => $totlRecrds - $totlIns
                            );

                $err = array
                            (
                                'Code' => 0,
                                'Msg' => 'Products are updated Successfully'
                            );
            }
            else
            {
                $arr = array();
                $err = array
                            (
                                'Code' => 1,
                                'Msg' => 'Products are Failed to Update'
                            );
            }
        }
        else
        {
            $arr = array();
            $err = array
                        (
                            'Code' => 1,
                            'Msg' => 'Invalid File Format'
                        );
        }
        $result = array
                        (
                            'results' => $arr,
                            'error' => $err
                        );
        return $result;
    }

    private function getSubCat($catid, $catarr = array()) {
        $sql = "SELECT p_catid, catid FROM tbl_category_master where p_catid in (" . $catid . ") order by catid ASC";

        $res = $this->query($sql);
        if ($res) {
            while ($row = $this->fetchData($res)) {
                if ($row['p_catid'] != 0) {
                    $catid .= ','.$this->getSubCat($row['catid']);
                }
            }
        }
        return $catid;
    }

    public function deletePrd($params)
    {

        $sql1 = "UPDATE tbl_vendor_product_mapping SET active_flag=2 WHERE product_id=" . $params['prdid']." AND vendor_id=" . $params['vid'];
        $res = $this->query($sql1);

        $sql2 = "UPDATE tbl_product_search SET active_flag=2 WHERE product_id=" . $params['prdid'];
        $res1 = $this->query($sql2);

        $sql3 = "UPDATE tbl_product_master SET active_flag=2 WHERE product_id=" . $params['prdid'];
        $res2 = $this->query($sql3);

        $sql4 = "UPDATE tbl_productid_generator SET active_flag=2 WHERE product_id=" . $params['prdid'];
        $res3 = $this->query($sql4);

        $sql5 = "UPDATE tbl_product_category_mapping SET display_flag=2 WHERE product_id=" . $params['prdid'];
        $res4 = $this->query($sql5);

        $sql6 = "UPDATE tbl_product_enquiry SET active_flag=2 WHERE product_id=" . $params['prdid'];
        $res5 = $this->query($sql6);

        $upsql="UPDATE tbl_wishlist SET wf=2 WHERE pid=".$params['prdid']."";
        $upres=$this->query($upsql);

        if ($res5) {
            $arr = array();
            $err = array('Code' => 0, 'Msg' => 'Product deleted successfully!');
        } else {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Error in fetching data');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

        public function toggleactive($params)
        {
            $sql = "SELECT active_flag from tbl_vendor_master WHERE vendor_id=".$params['vid'];
            $res = $this->query($sql);
            $cntres = $this->numRows($res);
            if($cntres > 0)
            {
                $rows = $this->fetchData($res);
                $flag = $rows['active_flag'];
            }
            if($params['flag'] == 1)
            {
                $sql1 = "UPDATE tbl_vendor_product_mapping SET active_flag=".$flag." WHERE product_id=" . $params['prdid']." AND vendor_id=" . $params['vid'];
                $res = $this->query($sql1);

                $sql2 = "UPDATE tbl_product_search SET active_flag=".$flag." WHERE product_id=" . $params['prdid'];
                $res1 = $this->query($sql2);

                $sql3 = "UPDATE tbl_product_master SET active_flag=".$flag." WHERE product_id=" . $params['prdid'];
                $res2 = $this->query($sql3);

                $sql4 = "UPDATE tbl_productid_generator SET active_flag=".$flag." WHERE product_id=" . $params['prdid'];
                $res3 = $this->query($sql4);

                $sql5 = "UPDATE tbl_product_category_mapping SET display_flag=".$flag." WHERE product_id=" . $params['prdid'];
                $res4 = $this->query($sql5);

                $sql6 = "UPDATE tbl_designer_product_mapping SET active_flag=".$flag." WHERE product_id=".$params['prdid'];
                $res5 = $this->query($sql6);

                $sql7 = "UPDATE tbl_product_enquiry SET active_flag=".$flag." WHERE product_id=".$params['prdid'];
                $res7 = $this->query($sql7);

            }
            else
            {
                $sql1 = "UPDATE tbl_vendor_product_mapping SET active_flag=".$params['flag']." WHERE product_id=" . $params['prdid']." AND vendor_id=" . $params['vid'];
                $res = $this->query($sql1);

                $sql2 = "UPDATE tbl_product_search SET active_flag=".$params['flag']." WHERE product_id=" . $params['prdid'];
                $res2 = $this->query($sql2);

                $sql3 = "UPDATE tbl_product_master SET active_flag=".$params['flag']." WHERE product_id=" . $params['prdid'];
                $res3 = $this->query($sql3);

                $sql4 = "UPDATE tbl_productid_generator SET active_flag=".$params['flag']." WHERE product_id=" . $params['prdid'];
                $res4 = $this->query($sql4);

                $sql5 = "UPDATE tbl_product_category_mapping SET display_flag=".$params['flag']." WHERE product_id=" . $params['prdid'];
                $res5 = $this->query($sql5);

                $sql6 = "UPDATE tbl_designer_product_mapping SET active_flag=".$params['flag']." WHERE product_id=".$params['prdid'];
                $res6 = $this->query($sql6);

                $sql7 = "UPDATE tbl_product_enquiry SET active_flag=".$params['flag']." WHERE product_id=".$params['prdid'];
                $res7 = $this->query($sql7);
            }

            if($res7)
            {
                $arr = array();
                $err = array('Code' => 0, 'Msg' => 'Product status changed!');
            }
            else
            {
                $arr = array();
                $err = array('code' => 1, 'msg' => 'Error in fetching data');
            }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function getVPrdByCatid($params)
        {

			$page   = ($params['page'] ? $params['page'] : 1);
			$limit  = ($params['limit'] ? $params['limit'] : 15);

			$sql = "select cat_name as name from tbl_category_master where catid=\"".$params['catid']."\"";
			$res = $this->query($sql);
			if($res)
			{
				$row = $this->fetchData($res);
				$catname = $row['name'];
			}

                        if(!empty($params['ilist']))
			{
				$ilist = str_replace('|@|',',',$params['ilist']);
				$where = " WHERE category_id in (".$ilist.") ";
			}
			else if(!empty($params['jlist']))
			{
				$expd = explode('|@|',$params['jlist']);
				foreach($expd as $key => $val)
				{
					$exd = explode('_',$val);
					$ids[] = $exd[0];
				}
				$ilist = implode(',',$ids);
				$where = " WHERE category_id in (".$ilist.") ";
			}
			else
			{
				$where = " WHERE category_id in (".$params['catid'].") ";
			}

			$sql = "SELECT
						count(distinct product_id) as cnt
					FROM
						tbl_product_category_mapping
					".$where."
					AND
                                                display_flag<>2
                                ";


			$res = $this->query($sql);
			if($res)
			{
				$row = $this->fetchData($res);
				$total = $row['cnt'];
			}

			$sql = "SELECT
						distinct product_id as pid,
						price
					FROM
						tbl_product_category_mapping
					".$where."
					AND display_flag<>2";

			switch($params['sortby'])
			{
				case 'pasc':
					$sql.="
							ORDER BY
								price ASC
						";
				break;

				case 'pdesc':
					$sql.="
							ORDER BY
								price DESC
						";
				break;

				case 'rate':
					$sql.="
							ORDER BY
								rating DESC
						";
				break;

				default:
					$sql.="
							ORDER BY
								product_id ASC
						";
				break;
			}

			$res = $this->query($sql);
			$cres=$this->numRows($res);
			if($cres>0)
			{
				while ($row = $this->fetchData($res))
				{
					$pid[] = $row['pid'];
				}



				if(!empty($params['slist']))
				{
					$sarr = explode('|@|',$params['slist']);
					$extn = " AND shape in ('".implode("','",$sarr)."') ";
				}

				if(!empty($params['tlist']))
				{
					$sarr = explode('|$|',$params['tlist']);
					foreach($sarr as $key => $val)
					{
						$expd = explode('|~|',$val);
						$exd = explode(';',$expd[1]);
						if($expd[0] == 'priceRange' && $params['catid'] == 10000)
						{
							$exd[0] = $exd[0]/dollarValue;
							$exd[1] = $exd[1]/dollarValue;
						}
						$extn .= " AND ".str_replace('Range','',$expd[0])." between \"".$exd[0]."\" AND \"".$exd[1]."\"";
					}
					//$extn = " AND shape in ('".implode("','",$sarr)."') ";
				}

				if(!empty($params['clist']))
				{
					$sarr = explode('|$|',$params['clist']);

					foreach($sarr as $key => $val)
					{
						$expd = explode('|~|',$val);
						$exd = explode('|@|',$expd[1]);
						$inarr = array();
						foreach($exd as $ky => $vl)
						{
							$ex = explode('_',$vl);
							$inarr[] = $ex[count($ex)-1];
							unset($ex[count($ex)-1]);
							$field = implode('_',$ex);
						}
						$extn .= " AND ".$field." in ('".implode("','",$inarr)."') ";
					}
				}

				$allpids = $pid = implode(',',$pid);

				if($params['ctid'])
				{
					$sqlct = "SELECT cityname
							FROM tbl_city_master
							WHERE cityid = ".$params['ctid'];
					$resct = $this->query($sqlct);
					if($resct)
					{
						$rowct = $this->fetchData($resct);

						$sqlpct = "
									SELECT
										product_id as pid,
                                                                                vendor_id as vid

									FROM
										tbl_vendor_product_mapping
									WHERE
										product_id in (".$allpids.")
									AND
                                                                                active_flag=1 OR active_flag=0
										city=\"".$rowct['cityname']."\"";
						$respct = $this->query($sqlpct);
						if($respct)
						{
							while ($rowpct = $this->fetchData($respct))
							{
								$pids[] = $rowpct['pid'];
                                                                $vid=$rowpct['vid'];
							}
							$allpids = $pid = implode(',',$pids);
						}
					}
				}

				$page   = ($params['page'] ? $params['page'] : 1);
				$limit  = ($params['limit'] ? $params['limit'] : 15);

				if($pid)
				{

					$sql = "SELECT
								count(distinct product_id) as cnt
							FROM
								tbl_product_search
							WHERE
								product_id IN(".$pid.")
                                                        AND
                                                                active_flag<>2
                                                        ".$extn."
							";
					$res = $this->query($sql);
					if($res)
					{
						$row = $this->fetchData($res);
						$total = $row['cnt'];
					}

					$patsql="
							SELECT
								distinct product_id,
								carat,
								color,
								certified,
								shape,
								clarity,
								price,
								polish,
								symmetry,
								cno,
								gold_weight,
								type,
								metal,
                                                                bullion_design
							FROM
								tbl_product_search
							WHERE
								product_id IN(".$pid.")
                                                        AND
                                                                active_flag<>2
                                                        ".$extn."
							ORDER BY
								field(product_id,".$pid.")
							";

					if (!empty($page))
					{
						$start = ($page * $limit) - $limit;
						$patsql.=" LIMIT " . $start . ",$limit";
					}

					$patres=$this->query($patsql);

					while($row2=$this->fetchData($patres))
					{
						$prodid[] = $row2['product_id'];
						$pid = $row2['product_id'];
						unset($row2['product_id']);
						$attr[$pid]['attributes']=$row2;
					}

					if(!empty($prodid))
					{
						$pid = $pids = implode(',',$prodid);
					}
					else
					{
						$pid = $pids = '';
					}

					$psql = "
							SELECT
								product_id as pid,
								barcode as pcode,
								product_name as pname,
								product_display_name as pdname,
								product_model as pmodel,
								product_brand as pbrand,
								prd_price as pprice,
								product_currency as pcur,
								prd_img as pimg
							FROM
								tbl_product_master
							WHERE
								product_id IN(".$pid.")
                                                        AND
                                                                active_flag<>2
                                                        ORDER BY
								field(product_id,".$pid.");
							";
					$pres=$this->query($psql);
					while($row1=$this->fetchData($pres))
					{
						$pid = $row1['pid'];
						$arr1[$pid]=$row1;
						$arr1[$pid]['attributes'] = $attr[$pid]['attributes'];
					}

					/* For filters */

					$sql = "
						SELECT
							attribute_id,
							attr_values,
							attr_range,
							attr_unit,
							attr_unit_pos
						FROM
							tbl_attribute_category_mapping
						WHERE
							category_id=".$params['catid']."
						AND
							attr_filter_flag=1
						ORDER BY
							attr_filter_position ASC";
					$res = $this->query($sql);
					if($res)
					{
						while($row 		= $this->fetchData($res))
						{
							$attrid[] = $row['attribute_id'];
							$attrmap[$row['attribute_id']] = $row;
						}
						$attrids 	= implode(',',$attrid);
					}

					//echo "<pre>";print_r($attrmap);die;

					$sql="
						SELECT
							attr_id,
							attr_name,
							attr_display_name,
							attr_type_flag
						FROM
							tbl_attribute_master
						WHERE
							attr_id IN(".$attrids.")
						ORDER BY
							attr_display_name ASC";

					$res = $this->query($sql);

					if($res)
					{
						$i=0;
						while($row = $this->fetchData($res))
						{
							switch ($row['attr_type_flag'])
							{
								case 6: //$pids
									$qry = "SELECT
												MIN(".$row['attr_name'].") AS minval,
												MAX(".$row['attr_name'].") AS maxval
											FROM
												tbl_product_search
											WHERE
												product_id IN(".$allpids.")
                                                                                        AND
                                                                                                active_flag<>2
                                                                                        ";
									$res1 = $this->query($qry);
									if($res1)
									{
										$row1 = $this->fetchData($res1);
										$data[$i]['range']['id'] 		= $row['attr_id'];
										$data[$i]['range']['name'] 		= $row['attr_name'];
										$data[$i]['range']['dname'] 	= $row['attr_display_name'];
										$data[$i]['range']['value'] 	= $row1['minval'].';'.$row1['maxval'];
										$data[$i]['range']['ovalue'] 	= $attrmap[$row['attr_id']]['attr_range'];
										$i++;
									}
								break;

								case 7:

									$qry = "SELECT
												group_concat(DISTINCT ".$row['attr_name'].") as name
											FROM
												tbl_product_search
											WHERE
												product_id IN(".$allpids.")
                                                                                        AND
                                                                                                active_flag<>2
											";
									$res1 = $this->query($qry);
									if($res1)
									{
										$arr = array();
										$row1 = $this->fetchData($res1);
										$expd = explode(',',$attrmap[$row['attr_id']]['attr_values']);
										$expd1 = explode(',',$row1['name']);
										foreach($expd1 as $key=>$val)
										{
											$arr[array_search($val,$expd)] = $val;
										}
										ksort($arr);
										$arr = array_values($arr);
										$data[$i]['checkbox']['id'] 	= $row['attr_id'];
										$data[$i]['checkbox']['name'] 	= $row['attr_name'];
										$data[$i]['checkbox']['dname'] 	= $row['attr_display_name'];
										$data[$i]['checkbox']['value'] 	= implode(',',$arr);
										$data[$i]['checkbox']['ovalue'] = $attrmap[$row['attr_id']]['attr_values'];
										$i++;
									}
								break;

								case 8:
								break;
							}
						}
					}

					/* *********** */

					$tmp_arr1 = (!empty($arr1)) ? (array_values($arr1)) : null;
					$arr1 = array('filters'=>$data,'products'=>$tmp_arr1,'total'=>$total,'getdata'=>$params,'catname'=>$catname,'vid'=>$vid);
					$err = array('errCode'=>0,'errMsg'=>'Details fetched successfully');
				}
				else
				{
					$arr1 = array();
					$err = array('errCode'=>1,'errMsg'=>'No records found');
				}
			}
			else
			{
				$arr1 = array();
				$err = array('errCode'=>1,'errMsg'=>'No records found');
			}
            $result = array('results'=>$arr1,'error'=>$err);
            return $result;
        }

    public function updateDollerRate($params)
    {
        $rates = $this->getAllRatesByVID($params['vid']);
        $emailsql = "SELECT email from tbl_registration WHERE user_id =".$params['vid'];
        $emailres = $this->query($emailsql);
        $emailcnt = $this->numRows($emailres);
        if($emailcnt > 0 )
        {
           $row = $this->fetchData($emailres);
        }

        $prevsql = "SELECT dollar_rate from tbl_vendor_master WHERE vendor_id =".$params['vid'];
        $prevres = $this->query($prevsql);
        $prevrow = $this->fetchData($prevres);

        if(floatval($params['dolRate']) !== floatval($prevrow['dollar_rate']))
        {
            $sql="UPDATE tbl_vendor_master SET dollar_rate=".$params['dolRate']." WHERE vendor_id=".$params['vid'];
            $res=$this->query($sql);
            if ($res)
            {
                $temp = array('rate'=>'dollar_rate','vid'=>$params['vid'],'type'=>'Dollar','prevRate'=>floatval($prevrow['dollar_rate']),'to'=>$row['email']);
                $mail = $this->sendRateMail($temp);
                if($mail == 1)
                {
                    $arr = array();
                    $err = array('code' => 0, 'msg' => 'Dollar Rate Updated Successfully!');
                }
                else
                {
                    $arr = array();
                    $err = array('code' => 1, 'msg' => 'Error in Updating Dollar Rate');
                }
            }
            else
            {
                $arr = array();
                $err = array('code' => 1, 'msg' => 'Error in Updating Dollar Rate');
            }
        }
        else
        {
            $arr = array('There is no change in the rate');
            $err = array('code' => 0, 'msg' => 'Dollar Rate Updated successfully!');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

    public function getDollerRate($params) {
        $sql="SELECT dollar_rate FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res=$this->query($sql);
        if ($res) {
            $row = $this->fetchData($res);
            $arr = $row;
            $err = array('code' => 0, 'msg' => 'Dollar Rate Updated successfully!');
        } else {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Error in Updating Dollar Rate');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
    }

        public function updateSilverRate($params)
        {
            $emailsql = "SELECT email from tbl_registration WHERE user_id =".$params['vid'];
            $emailres = $this->query($emailsql);
            $emailcnt = $this->numRows($emailres);
            if($emailcnt > 0 )
            {
                $row = $this->fetchData($emailres);
            }

            $prevsql = "SELECT silver_rate from tbl_vendor_master WHERE vendor_id =".$params['vid'];
            $prevres = $this->query($prevsql);
            $prevrow = $this->fetchData($prevres);

            if(floatval($params['silRate']) !== floatval($prevrow['silver_rate']))
            {
                $sql="UPDATE tbl_vendor_master SET silver_rate=".$params['silRate']." WHERE vendor_id=".$params['vid'];
                $res=$this->query($sql);
                if($res)
                {
                    $temp = array('rate'=>'silver_rate','vid'=>$params['vid'],'type'=>'Silver','prevRate'=>$prevrow['silver_rate'],'to'=>$row['email']);
                    $mail = $this->sendRateMail($temp);
                    if($mail == 1)
                    {
                        $arr = array();
                        $err = array('code' => 0, 'msg' => 'Silver Rate Updated successfully!');
                    }
                    else
                    {
                        $arr = array();
                        $err = array('code' => 1, 'msg' => 'Error in Updating Silver Rate');
                    }
                }
                else
                {
                    $arr = array();
                    $err = array('code' => 1, 'msg' => 'Error in Updating Silver Rate');
                }
            }
            else
            {
                $arr = array('There is no change in the rate');
                $err = array('code' => 0, 'msg' => 'Silver Rate Updated successfully!');
            }
            $result = array('results' => $arr, 'error' => $err);
            return $result;
        }

        public function getSilverRate($params) {
        $sql="SELECT silver_rate FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res=$this->query($sql);
        if ($res) {
            $row = $this->fetchData($res);
            $arr = $row;
            $err = array('code' => 0, 'msg' => 'Silver Rate Updated successfully!');
        } else {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Error in Updating Silver Rate');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
        }

        public function updateGoldRate($params)
        {
            $emailsql = "SELECT email from tbl_registration WHERE user_id =".$params['vid'];
            $emailres = $this->query($emailsql);
            $emailcnt = $this->numRows($emailres);
            $row = $this->fetchData($emailres);

            $prevsql = "SELECT gold_rate from tbl_vendor_master WHERE vendor_id =".$params['vid'];
            $prevres = $this->query($prevsql);
            $prevrow = $this->fetchData($prevres);


            $temp = array('rate'=>'gold_rate','vid'=>$params['vid'],'type'=>'Gold','prevRate'=>$prevrow['gold_rate'],'to'=>$row['email']);

            if(floatval($params['goldRate']) !== floatval($prevrow['gold_rate']))
            {
                $sql="  UPDATE
                                    tbl_vendor_master
                        SET
                                    gold_rate=".$params['goldRate']."
                        WHERE
                                    vendor_id=".$params['vid'];
                $res=$this->query($sql);
                if ($res)
                {
                    $mail = $this->sendRateMail($temp);
                    if($mail == 1)
                    {
                        $arr = array();
                        $err = array('code' => 0, 'msg' => 'Gold Rate Updated Successfully!');
                    }
                    else
                    {
                        $arr = array();
                        $err = array('code' => 1, 'msg' => 'Error in Updating Gold Rate');
                    }
                }
                else
                {
                    $arr = array();
                    $err = array('code' => 1, 'msg' => 'Error in Updating Gold Rate');
                }
            }
            else
            {
                $arr = array('There is no change in the rate');
                $err = array('code' => 0, 'msg' => 'Gold Rate Updated Successfully!');
            }
            $result = array('results' => $arr, 'error' => $err);
            return $result;
        }

        public function getGoldRate($params) {
        $sql="SELECT gold_rate,silver_rate FROM tbl_vendor_master WHERE vendor_id=".$params['vid'];
        $res=$this->query($sql);
        if ($res) {
            $row = $this->fetchData($res);
            $arr = $row;
            $err = array('code' => 0, 'msg' => 'Gold Rate Updated successfully!');
        } else {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Error in Updating Gold Rate');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
        }

        public function getAllRatesByVID($params) {
        $sql="SELECT silver_rate, gold_rate, dollar_rate FROM tbl_vendor_master WHERE vendor_id='".$params['vid']."'";
        $res=$this->query($sql);
        if ($res) {
            $row = $this->fetchData($res);
            $arr = $row;
            $err = array('code' => 0, 'msg' => 'Rates fetched successfully!');
        } else {
            $arr = array();
            $err = array('code' => 1, 'msg' => 'Error in fetching Rates');
        }
        $result = array('results' => $arr, 'error' => $err);
        return $result;
        }

        public function Vpactive($params) {

        $vprds="SELECT product_id from tbl_vendor_product_mapping where vendor_id=".$params['vid'];
        $vprdsres=$this->query($vprds);
        $cntvpres=$this->numRows($vprdsres);

        if($cntvpres>0)
        {
            while($chkrow=$this->fetchData($chkactres))
            {
                $pid[] = $chkrow['product_id'];
            }
            $prid=implode(',',$pid);

            $sql1 = "UPDATE tbl_vendor_product_mapping SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND vendor_id=".$params['vid']." AND active_flag NOT IN(2,3)";
            $res = $this->query($sql1);

            $sql2 = "UPDATE tbl_product_search SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND active_flag NOT IN(2,3)";
            $res1 = $this->query($sql2);

            $sql3 = "UPDATE tbl_product_master SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND active_flag NOT IN(2,3)";
            $res2 = $this->query($sql3);

            $sql4 = "UPDATE tbl_productid_generator SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND active_flag NOT IN(2,3)";
            $res3 = $this->query($sql4);

            $sql5 = "UPDATE tbl_product_category_mapping SET display_flag=".$params['af']." WHERE product_id IN(".$prid.") AND display_flag NOT IN(2,3)";
            $res4 = $this->query($sql5);

            $sql6 = "UPDATE tbl_designer_product_mapping SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND active_flag NOT IN(2,3)";
            $res5 = $this->query($sql6);

            $sql7 = "UPDATE tbl_product_enquiry SET active_flag=".$params['af']." WHERE product_id IN(".$prid.") AND active_flag NOT IN(2,3)";
            $res6 = $this->query($sql7);

            $arr=array();
            $err=array('code'=>0,'msg'=>'Product status changed too');
        }
        else
        {
            $arr=array();
            $err=array('code'=>0,'msg'=>'Product status changed too');
        }
        $result=array('result'=>$arr,'error'=>$err);
        return $result;
        }

        private function getAbbrValue($val)
        {
            $propValArr=array('EX'=>'Excellent','VG'=>'Very Good','GD'=>"Good",'FAIR'=>'Fair','NO'=>'None','NN'=>'None','MED'=>'Medium','FNT'=>'Faint','STG'=>'Strong','VSTG'=>'Very Strong');
            return $propValArr[$val];
        }

        private function getShapeAbbrValue($val)
        {
            $propValArr=array('MQ'=>'Marquise','Marquise'=>'Marquise','Asscher'=>'Asscher','Round'=>'Round','RO' => 'Round','RBC' => 'Round','BR'=> 'Round','RD'=> 'Round','RND'=> 'Round','B'=> 'Round','RB'=> 'Round','PRN' => 'Princess','Princess'=>'Princess','PR'=> 'Princess','PRIN'=> 'Princess','PN'=> 'Princess','PC'=> 'Princess','MDSQB'=> 'Princess','SMB'=> 'Princess','PS' => 'Pear','Pear'=>'Pear','PSH'=> 'Pear','PB'=> 'Pear','PM'=> 'Pear','HS' => 'Heart','Heart'=>'Heart','HT'=> 'Heart','MHRC'=> 'Heart','OV' => 'Oval','Oval'=>'Oval','OMB' => 'Oval','EM' => 'Emerald','EC' => 'Emerald','Radiant'=>'Radiant','Cushion' => 'Cushion','CUBR' => 'CUSHION','CUMOD' => 'CUSHION');
            return $propValArr[$val];
        }

        public function sendRateMail($params)
        {
            global $comm;

            $sql = "SELECT
                            orgName,
                            contact_person,
                            ".$params['rate']."
                    FROM
                            tbl_vendor_master
                    WHERE
                            vendor_id = ".$params['vid'];
            $res = $this->query($sql);
            $chkV = $this->numRows($res);
            if($chkV == 1)
            {
                while($row = $this->fetchData($res))
                {
                    $vDet['org_name'] = $row['orgName'];
                    $vDet['C_person'] = $row['contact_person'];
                    $vDet['cur_rate'] = $row[$params['rate']];
                }
                $subject = $params['type'].' Rate for IFtoSI';
                $message = 'Dear ' . $vDet['org_name'] . ',';
                $message .= "\r\n";
                $message .= "Your ". strtolower($params['type'])." rate has changed from Previous rate : ". $params['prevRate']. " to Current rate : ". $vDet['cur_rate'];
                $message .= "\r\n \r\n";
                $message .= "Team IFtoSI";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: <info@iftosi.com>' . "\r\n";
                if(!empty($params['to']))
                {
                    mail($params['to'], $subject, $message, $headers);
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }

    }
}

?>
