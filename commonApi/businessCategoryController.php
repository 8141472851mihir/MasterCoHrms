<?php
include_once 'lib.php';

if(isset($_POST) && !empty($_POST)){

    if ($key==$keydb ) {
    $response = array();
    extract(array_map("test_input" , $_POST));

    	if($_POST['getCatgory']=="getCatgory"){

                $q=$d->select("business_categories","","group by category_industry" );
                if(mysqli_num_rows($q)>0){
                    $response["category"] = array();

                    while($data=mysqli_fetch_array($q)) {

                            $category = array(); 
                            $category["category_id"]=$data['category_id'];
                            $category["category_industry"]=html_entity_decode($data['category_industry']);
                            
                            $category["sub_category"] = array();
                            $categoryIndustryEsc = $d->escapeSqlString($data['category_industry']);
                            $q3=$d->select("business_categories","category_industry='$categoryIndustryEsc'","");
					        while ($subData=mysqli_fetch_array($q3)) {
					        	$sub_category = array();
                                $sub_category["category_name"]=html_entity_decode($subData['category_name']);
                                array_push($category["sub_category"], $sub_category); 
					        }

                            array_push($response["category"], $category);


                    }
                    $response["message"]="Get Category Success.";
                    $response["status"]="200";
                    echo json_encode($response);

                }else{

                    $response["message"]="No Categoty Found.";
                    $response["status"]="201";
                    echo json_encode($response);

                }

            }else if($_POST['getSubCatgory']=="getSubCatgory"){

                    $category_industry = $d->escapeSqlString($category_industry ?? '');
                    $q3=$d->select("business_categories","category_industry='$category_industry'","");
                    if(mysqli_num_rows($q3)>0){ 
                        
                       $response["sub_category"] = array();

                       while($data=mysqli_fetch_array($q3)) {
                            $sub_category = array();
                            $sub_category["category_name"]=html_entity_decode($data['category_name']);
                            array_push($response["sub_category"], $sub_category);
                        }

                        $response["message"]="Get Category Success.";
                        $response["status"]="200";
                        echo json_encode($response);

                    } else {
                        $response["message"]="No Categoty Found.";
                        $response["status"]="201";
                        echo json_encode($response);
                    }

               

            }else{
                $response["message"]="wrong tag.";
                $response["status"]="201";
                echo json_encode($response);

            }


	}else{

         $response["message"]="wrong api key.";
        $response["status"]="201";
        echo json_encode($response);

    }

}?>
