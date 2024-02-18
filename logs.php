<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>  
<?php include("lib/t3storelib.php");
$app = new t3storeLib();
?> 
<!-- Modal content-->
<div class="modal-content ">
  <div class="modal-header">
    <h4 class="modal-title"> <i class="fa fa-list-ol fa-5" aria-hidden="true"></i>Login Details</li></h4>
  </div>
  <div class="modal-body">
      <br>
            
<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">All Logged in List</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="employee_data" class="table table-bordered table-hover">
                <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Role</th>
                      <th>Last Login</th>
                      <th >Action</th>
                    </tr>
                </thead>
             <?php        
			   $result = $app->getLogs($_SESSION['id'],$_SESSION['type_app']);
			   foreach($result as $k){   
			   if($k['id']){ 
                echo '<tr>
						 <td>'.$k['name'].'</td>
						 <td>'.$k['email'].'</td>
						 <td>'.$k['phone'].'</td>
						 <td>'.$k['type_appstatus'].'</td>
						 <td>'.$k['timestap'].'</td>
						 <td>
						 <a href="deletelog.php?id='.base64_encode($k['id']).'" class="btn btn-social-icon btn-google" onClick="return checkDelete()" ><i class="fa fa fa-trash-o"></i></a></td> 
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
</script>
<script>  
 $(document).ready(function(){  
     $('#employee_data').DataTable({ 
	   "scrollX": true,
	   'paging'      : true,
	   "processing": true,
	   'searching'   : true, 
	   'ordering'    : true,
       'info'        : true,
       'autoWidth'   : false   
     });  
 });  
</script> 
 
<?php include 'scriptfooter.php'; ?>