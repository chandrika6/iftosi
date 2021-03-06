<?php

include APICLUDE.'common/db.class.php';
class admin extends DB
{

    function __construct($db)
    {
        parent::DB($db);
    }

    public function getProdList($params)
    {
        $page = (!empty($params['page']) ? $params['page'] : 1);
        $limit = (!empty($params['limit']) ? $params['limit'] : 15);

        $subQuery = "SELECT
                product_id AS id,
                count(product_id) AS total_img,
                sum(active_flag = 0) as pend_img,
                sum(active_flag = 1) as appr_img,
                sum(active_flag = 3) as rej_img
            FROM
                tbl_product_image_mapping
            group by product_id
            order by upload_date DESC";

        $cntRes = $this->query($subQuery);
        $total_products = $this->numRows($cntRes);
        if (!empty($page)) {
            $start = ($page * $limit) - $limit;
            $subQuery.=" LIMIT " . $start . ",$limit";
        }
        $subQueryRes = $this->query($subQuery);
        if($this->numRows($subQueryRes)>0) {
            while ($row = $this->fetchData($subQueryRes)) {
                $productIDs[] = $row['product_id'];
                $isql = "SELECT a.product_id,a.barcode,a.update_time,b.shape,b.type,b.metal,b.carat,b.color,b.gold_purity,b.gold_weight,b.cut,b.clarity FROM tbl_product_master as a,tbl_product_search as b WHERE a.product_id = ".$row['id'];
                $ires = $this->query($isql);
                if( $this->numRows($ires)>0) {
                    $irow = $this->fetchData($ires);
                    $rowp[]=$row['product_id'];
                    $row['barcode']=$irow['barcode'];
                    $row['update_time']=$irow['update_time'];
                    $row['shape'] = $irow['shape'];
                    $row['type'] = $irow['type'];
                    $row['metal'] = $irow['metal'];
                    $row['purity'] = $irow['gold_purity'];
                    $row['weight'] = number_format($irow['gold_weight'],0);
                    $row['carat'] = $irow['carat'];
                    $row['color'] = $irow['color'];
                    $row['cut'] = $irow['cut'];
                    $row['clarity'] = $irow['clarity'];
                    $row['certified'] = $irow['clarity'];
                }
                else
                {
                    $row['barcode']='N-A';
                    $row['update_time']='N-A';
                }

                $isqlV = "SELECT a.orgName FROM tbl_vendor_master as a,tbl_vendor_product_mapping as b WHERE a.vendor_id = b.vendor_id AND product_id = ".$row['id'];
                $iresV = $this->query($isqlV);
                if( $this->numRows($iresV)>0) {
                    $irowV = $this->fetchData($iresV);
                    $row['orgName']=$irowV['orgName'];
                }
                else
                {
                    $row['orgName']='N/A';
                }
                 $rowCd = array();

                $isqlCid = "SELECT category_id FROM tbl_product_category_mapping WHERE product_id = ".$row['id'];
                $iresCid = $this->query($isqlCid);
                while($irowV = $this->fetchData($iresCid)) {
                   $rowCd[]=$irowV['category_id'];
                }

               $rowCid =  implode(',',$rowCd);

                $isqlC = "SELECT cat_name FROM tbl_category_master WHERE catid IN (".$rowCid.")";
                $iresC = $this->query($isqlC);
                $category = array();
                while($irowC = $this->fetchData($iresC)) {

                    $category[]=$irowC['cat_name'];
                }
                $row['category'] = implode(',',$category);
                $result[]=$row;
            }
            $results=array('products'=>$result,"total_products"=>$total_products);
            $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
        } else {
            $results = '';
            $err = array('Code' => 1, 'Msg' => 'No records found');
        }
        $result=array('results'=>$results,'error'=>$err);
        return $result;
    }

    public function getImgByProd($params)
    {
        $query="SELECT * FROM tbl_product_image_mapping WHERE product_id = ".$params['pid']." ORDER BY image_sequence ";
        $res = $this->query($query);
        $total=$this->numRows($res);
        if($total>0) {
            while ($row = $this->fetchData($res)) {

                $result[]=$row;
            }
            $results=array('imgs'=>$result,"total_imgs"=>$total);
            $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
        } else {
            $results = '';
            $err = array('Code' => 1, 'Msg' => 'Error in fetching data');
        }
        $result=array('results'=>$results,'error'=>$err);
        return $result;
    }
    public function updateImageData($params)
    {
        $query="UPDATE tbl_product_image_mapping SET ";

        if(!empty($params['seq'])) {
            $query .="image_sequence='".$params['seq']."', ";
        }
        if(!empty($params['flag'])) {
            $query .="active_flag='".$params['flag']."', ";
        }
        if(!empty($params['rea'])) {
            $query .="reason='".$params['rea']."', ";
        }
        $query .=" update_date=NOW() WHERE id = ".$params['id'];
        $res = $this->query($query);
        if($res) {
            $results=array();
            $err = array('Code' => 0, 'Msg' => 'Updated successfully');
        } else {
            $results=array();
            $err = array('Code' => 1, 'Msg' => 'Error in Updating data');
        }
        $result=array('results'=>$results,'error'=>$err);
        return $result;
    }

    public function customerList($params)
    {
        $page = (!empty($params['pgno'])) ? $params['pgno'] : 1;
        $limit = (!empty($params['limit'])) ? $params['limit'] : 50;

        if(empty($params['srchTxt']))
        {
          $ssql="SELECT
                        user_id AS uid,
                        user_name AS name,
                        email,
                        logmobile AS mobile,
                        city,
                        city AS cty,
                        (SELECT state FROM tbl_area_master WHERE city=cty ORDER BY city DESC LIMIT 1) AS state,
                        date_format(date_time,'%d %M, %Y') AS joinDate
                FROM
                        tbl_registration
                WHERE
                        is_vendor=0
                AND
                        is_active=1
                ORDER BY date_time DESC";
        }
        else
        {
          $ssql="SELECT
                        user_id AS uid,
                        user_name AS name,
                        email,
                        logmobile AS mobile,
                        city,
                        city AS cty,
                        (SELECT state FROM tbl_area_master WHERE city=cty ORDER BY city DESC LIMIT 1) AS state,
                        date_format(date_time,'%d %M, %Y') AS joinDate
                FROM
                        tbl_registration
                WHERE
                        (
                            user_name LIKE '" . urldecode($params['srchTxt']) . "%'
                OR
                            email LIKE '" . urldecode($params['srchTxt']) . "%'
                OR
                            logmobile LIKE '" . urlencode($params['srchTxt']) . "%'
                OR
                            city LIKE '" . urlencode($params['srchTxt']) . "%'
                        )
                AND
                        is_vendor=0
                AND
                        is_active=1
                ORDER BY date_time DESC";
        }

            $res = $this->query($ssql);
            $cont = $this->numRows($res);
            if (!empty($page))
            {
                $start = ($page * $limit) - $limit;
                $ssql.=" LIMIT " . $start . ",$limit";
            }

            $res = $this->query($ssql);

            if($res)
            {
                while ($row = $this->fetchData($res))
                {
                    if(empty($row['city']))
                    {
                        $row['city'] = 'N/A';
                    }
                    if(empty($row['state']))
                    {
                        $row['state'] = 'N/A';
                    }
                    if(empty($row['mobile']))
                    {
                        $row['mobile'] = 'N/A';
                    }
                    if(!empty($row['name']))
                    {
                        $row['name'] = ucwords($row['name']);
                    }
                    $result[]=$row;
                }
                $err = array('Code' => 0, 'Msg' => 'Details fetched successfully');
            }
            else
            {
                $result = array();
                $err = array('Code' => 1, 'Msg' => 'Error in fetching data');
            }
            $results=array('results'=>$result,'error'=>$err,'total_customers'=>$cont);
            return $results;
    }


}
?>
