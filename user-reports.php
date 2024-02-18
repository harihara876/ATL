<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>  
<?php include("lib/t3storelib.php");
error_reporting(0);
$app = new t3storeLib();
if($_GET['from_date'] && $_GET['to_date'] && $_GET['submit']=="Search"){
   $result = $app->getUserReports($_SESSION['id'],$_SESSION['type_app'],$_GET['from_date'],$_GET['to_date'],$_GET['storeid']);
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
    <h4 class="modal-title"> <i class="fa fa-list-ol fa-5" aria-hidden="true"></i> Users Report</li></h4>
  </div>
  <div class="modal-body">
      <br>
            
<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
             <form method="get" action="" name="report">
                <div class="col-md-2" style="display:none;">     
                <label> Store Admin </label>
                <select name="storeid" id="storeid" class="form-control">
                  <option value="0">All</option>
                  <?php 
                  foreach($users as $u){
					  if($storeid==$u['admin_id']){
					    echo "<option value=".$u['admin_id']." selected>".$u['name']."</option>";
					  }else{
						echo "<option value=".$u['admin_id'].">".$u['name']."</option>";
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
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Date</th>
                      <th class="no-print">View</th>
                    </tr>
                </thead>
             <?php        
			  
			   foreach($result as $k){   
			   if($k['id']){ 
			    $id = $k["id"];
				if($k['type']=="StoreAdmin"){
					$url = "view-storeadmin.php?id=".$id;
				}else{
					$url = "view-user.php?id=".$id;
				}
				
                echo '<tr>
						 <td>'.$k['id'].'</td>
						 <td>'.$k['name'].'</td>
						 <td>'.$k['email'].'</td>
						 <td>'.$k['type'].'</td>
						 <td>'.$k['created_on'].'</td>
						 <td class="no-print"><a href="'.$url.'" target="_blank"><span class="label"><button class="btn btn-primary">View <i class="fa fa-eye" aria-hidden="true"></i></button></span></a>
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