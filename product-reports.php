<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>  
<?php include("lib/t3storelib.php");
error_reporting(0);
$app = new t3storeLib();
if($_GET['from_date'] && $_GET['to_date'] && $_GET['submit']=="Search"){
   $result = $app->getProductReports($_SESSION['id'],$_SESSION['type_app'],$_GET['from_date'],$_GET['to_date'],$_GET['storeid']);
   $from_date = $_GET['from_date'];
   $to_date = $_GET['to_date'];
   $storeid = $_GET['storeid'];
}else{
   $result = array();
}
$users = $app->getUsers();
?> 
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
</style>
<!-- Modal content-->
<div class="modal-content ">
  <div class="modal-header">
    <h4 class="modal-title"> <i class="fa fa-list-ol fa-5" aria-hidden="true"></i> Product Report</li></h4>
  </div>
  <div class="modal-body">
      <br>
            
<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
             <form method="get" action="" name="report">
                <div class="col-md-2">     
                <label> Store Admin </label>
                <select name="storeid" id="storeid" class="form-control">
                  <option value="0">All</option>
                  <?php 
                  if($_SESSION['type_app']  == 'ADMIN'){
                   foreach($users as $u){
					  if($storeid==$u['admin_id']){
					    echo "<option value=".$u['admin_id']." selected>".$u['name']."</option>";
					  }else{
						echo "<option value=".$u['admin_id'].">".$u['name']."</option>";
					  }
				   }
                  }
				  ?>
                </select>
                </div>
                
                <div class="col-md-2">    
                <label>From </label>
                <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="form-control">
                </div>
                
                <div class="col-md-2">     
                 <label>To </label>
                 <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="form-control">
                </div>
                
                <div class="col-md-2">  
                <label>&nbsp; </label>  
                 <input type="submit" value="Search" name="submit" class="btn btn-primary form-control">
                </div>
                
                 <div class="col-md-2">  
                <label>&nbsp; </label>  
                 <input type="submit" value="Reset" name="submit" class="btn btn-default form-control">
                </div>
                
                <div class="col-md-2">  
                <label>&nbsp; </label>  
                 <input type="button" value="Print" onclick="printDiv('printableArea')" class="btn btn-danger form-control">
                </div>
                
             </form>
            </div>
            <!-- /.box-header -->
            <div class="box-body" id="printableArea">
              <table id="employee_data" class="table table-bordered table-hover">
                <thead>
                    <tr>
                      <th>Product ID</th>
                      <th>Product Name</th>
                      <th>UPC</th>
                      <th>Quantity</th>
                      <th>Regular Price</th>
                      <th>Sell Price</th>
                      <th>Date</th>
                      <th>Store Admin</th>
                      <th class="no-print">View</th>
                    </tr>
                </thead>
             <?php        
			  
			   foreach($result as $k){   
			   if($k['id']){ 
                echo '<tr>
						 <td>'.$k['product_id'].'</td>
						 <td>'.$k['product_name'].'</td>
						 <td>'.$k['UPC'].'</td>
						 <td>'.$k['quantity'].'</td>
						 <td>'.$k['Regular_Price'].'</td>
						 <td>'.$k['sellprice'].'</td>
						 <td>'.$k['Date_Created'].'</td>
						 <td>'.$k['name'].'</td>
						 <td class="no-print"><a href="viewproductsdetails.php?id='.base64_encode($k["id"]).'" target="_blank"><span class="label"><button class="btn btn-primary">View <i class="fa fa-eye" aria-hidden="true"></i></button></span></a>
						</td>
					  </tr>';
			   }
               }?>
              </tbody>
              </table>
            </div></div></div></div>
</section></div>
</div>

<script language="JavaScript" type="text/javascript">
  function checkDelete(){
    return confirm('Are you sure you want to delete?');
  }
  function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
  }
</script> 
 
<?php include 'scriptfooter.php'; ?>