<?php

require_once("config.php");

$country=intval($_POST['cat_id']);


if($_SESSION['type_app']  == 'ADMIN'){

 $selectSQL = "SELECT
             `Sub_Category_Id` AS `sub_cat_id`,
             `Sub_Category_Name` AS `sub_cat_name`,
             `cat_id`
         FROM `subcategories` WHERE cat_id='$country'
         ORDER BY `sub_cat_name` ASC";
}
else{
	 $selectSQL = "SELECT
             `Sub_Category_Id` AS `sub_cat_id`,
             `Sub_Category_Name` AS `sub_cat_name`,
             `cat_id`
         FROM `subcategories` where cat_id='$country'
         ORDER BY `sub_cat_name` ASC";
}


     $result = mysqli_query($conn, $selectSQL);
?>
<select name="sub-category">
<option>Select Sub Category</option>
<?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { ?>
<option value=<?php echo $row['sub_cat_id']?>><?php echo $row['sub_cat_name']?></option>
<?php } ?>
</select>

<?php 
// Include the database config file 
// require_once("config.php");
 
// if(!empty($_POST["country_id"])){ 
//     // Fetch state data based on the specific country 
//     $selectSQL = "SELECT
//              `Sub_Category_Id` AS `sub_cat_id`,
//              `Sub_Category_Name` AS `sub_cat_name`,
//              `cat_id`
//          FROM `subcategories` WHERE cat_id='$country'
//          ORDER BY `sub_cat_name` ASC";
//      $result = mysqli_query($conn, $selectSQL);
//      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
//       if($row > 0 ) {

//             echo '<option value="'.$row['sub_cat_id'].'">'.$row['sub_cat_name'].'</option>'; 
//         }
//             else{
//         echo '<option value="">State not available</option>'; 

//         }
//     }

//     // $query = "SELECT * FROM subcategories WHERE cat_id = ".$_POST['country_id']." ORDER BY `sub_cat_name` ASC"; 
//     // $result = $db->query($query); 
     
//     // // Generate HTML of state options list 
//     // if($result->num_rows > 0){ 
//     //     echo '<option value="">Select State</option>'; 
//     //     while($row = $result->fetch_assoc()){  
//     //         echo '<option value="'.$row['sub_cat_id'].'">'.$row['sub_cat_name'].'</option>'; 
//     //     } 
//     // }else{ 
//     //     echo '<option value="">State not available</option>'; 
//     // } 
// }
?>