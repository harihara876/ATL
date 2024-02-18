<?php include 'header.php';?>
<?php include  'sidebar.php';?>
<?php include 'widget.php';?>



<?php
$id= $_SESSION['id'];
 $editimageshandel="SELECT * FROM admin WHERE `admin_id`='$id' LIMIT 1 ";
$gethandel=mysqli_query($conn, $editimageshandel);
$resulthandel=mysqli_fetch_array($gethandel,MYSQLI_ASSOC);
//print_r($resulthandel);
$imagehandel=$resulthandel['currency'];
$tax1=$resulthandel['tax'];
$ship=$resulthandel['shipping'];
$store_name=$resulthandel['store_name'];
$address=$resulthandel['address'];
$phone1=$resulthandel['phone'];

?>





    <!-- Modal content-->
    <div class="modal-content ">
      <div class="modal-header">


        <h4 class="modal-title"> <i class="fa fa-gift fa-2" aria-hidden="true"></i>Shop settings </li></h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal" method="post">
              <div class="box-body">

               <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Currency:</label>

                  <div class="col-sm-10">
           <select name="currency"  class="selectpicker"   style="color: #fff;" title="Choose here..."id="place" required>


		  <option value="<?php echo $imagehandel;?> " SELECTED="YES"><?php echo $imagehandel;?></option>
<?php if($imagehandel=='USD'){?>
                  <option value="INR">INR</option><?php }else{?><option value="USD">USD</option><?php } ?>



		</select>
                  </div>
                </div>
 <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Tax(%):</label>
                  <div class="col-sm-10">
                        <input type="number" step="0.01" name="tax" value="<?php echo $tax1;?>">
                                                       </div>
</div><div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Shipping Charges:</label>
                  <div class="col-sm-10">
                        <input type="number"  name="ship" value="<?php echo $ship;?>">
                                                       </div>
</div>


<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Store name:</label>
                  <div class="col-sm-10">
                       <textarea name="store_name">  <?php echo $store_name;?></textarea>
                                                       </div>
</div>
<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Store address:</label>
                  <div class="col-sm-10">
                       <textarea name="address">  <?php echo $address;?></textarea>
                                                       </div>
</div><div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Store Contact No:</label>
                  <div class="col-sm-10">
                        <input type="number"  name="phone" value="<?php echo $phone1;?>">
                                                       </div>
</div>

               <div class="box-footer">
                   <input type="submit" class="btn btn-default pull-right" value="Update" name="update">

              </div>

              </form>
            </div>


      <?php include 'scriptfooter.php'; ?>


      <?php
if(isset($_POST['update']))
{

$phone=$_POST['phone'];

$imagehandel1=$_POST['currency'];
$tax=$_POST['tax'];
$ship1=$_POST['ship'];
$store_name1=$_POST['store_name'];
        $remove[] = "'";
        $remove[] = '"';
        $remove[] = "-"; // just as another example
        $store_name1 = str_replace( $remove, "", $store_name1 );

     $address1=$_POST['address'];
        $remove[] = "'";
        $remove[] = '"';
        $remove[] = "-"; // just as another example
        $address1 = str_replace( $remove, "", $address1 );


$editimageshandel="UPDATE admin set currency='".$imagehandel1."',tax='".$tax."',shipping='".$ship1."',store_name='".$store_name1."',address='".$address1."' ,phone='".$phone."' WHERE `admin_id`='$id' ";

$gethandel=mysqli_query($conn, $editimageshandel);

if($gethandel=='true');
{
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Shop settings updated ",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "currency_settings.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php

}
}
  ?>




























