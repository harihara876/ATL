<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
 
    <!-- Modal content-->
    <div class="modal-content ">
      <div class="modal-header">
       
        <h4 class="modal-title"> <i class="fa fa-list-ol fa-5" aria-hidden="true"></i>Sub SubCategory</li></h4>
      </div>
      <div class="modal-body">
      
             <?php if($_SESSION['type_app']  == 'ADMIN'){  ?>
            
             <button class="btn btn-primary pull-left" data-toggle="modal" data-target="#myModal">Add Sub SubCategory</button>
             <hr>
             <?php } ?>
                 <br>
            <!--<h1>hello</h1>-->
            <?php
if($_SESSION['type_app']  == 'ADMIN'){

           $sql="SELECT * FROM `subsubcategories` order by Sub_Sub_Category_Id DESC ";
           $check= mysqli_query($conn, $sql);
  }else{
           $sql="SELECT * FROM `subsubcategories` order by Sub_Sub_Category_Id DESC ";
           $check= mysqli_query($conn, $sql);
  }    

 ?>

<script language="JavaScript" type="text/javascript">
function checkDelete(){
    return confirm('Are you sure you want to delete?');
}
</script>


<section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">All Sub SubCategory List</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="employee_data" class="table table-bordered table-hover">
                <thead>
                <tr>
                  
                  <th>SubCategory</th>
                  <th>Sub SubCategory Name</th>
                  <th>Description</th>
                  <th>Image</th>
                  <th >Action</th>

                </tr></thead>
   <?php            
while($k= mysqli_fetch_array($check,MYSQLI_BOTH)) {


  if($_SESSION['type_app']  == 'ADMIN'){
		$sql1 = "SELECT `Sub_Category_Id`,`Sub_Category_Name`  FROM `subcategories` where `Sub_Category_Id`={$k['Sub_Category_Id']} ";
		$check1 = mysqli_query($conn, $sql1);
		$resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
  }else{
	$sql1 = "SELECT `Sub_Category_Id`,`Sub_Category_Name`  FROM `subcategories` where `storeadmin_id`='{$_SESSION['id']}' and `Sub_Category_Id`={$k['Sub_Category_Id']} ";
	$check1 = mysqli_query($conn, $sql1);
	$resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
  } 

  foreach ($check1 as $row) {    
            echo '<tr>
				   <td>'.$row['Sub_Category_Name'].'</td>
				   <td>'.$k['Sub_Sub_Category_Name'].'</td>
				   <td>'.$k['Description'].'</td>
                  <td><img src="'. $k['Image'].'" height="100" width="100"></td>
                  <td><a href="editSubSubCategory.php?id='.base64_encode($k['Sub_Sub_Category_Id']).'"><button class=" btn btn-primary btn-warning" >Edit<i class="fa fa-pencil" aria-hidden="true"></i></button></a>
                  <a href="deleteSubSub_Category.php?id='.base64_encode($k['Sub_Sub_Category_Id']).'" class="btn btn-social-icon btn-google" onClick="return checkDelete()" ><i class="fa fa fa-trash-o"></i></a></td> </tr>';
   }
 }

   ?>
               
              </tbody></table>
            </div></div></div></div></div>
</div></section>
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
            
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add New Sub SubCategory</h4>
      </div>
      <div class="modal-body">
        
          <form role="form" action="add-subsubcat.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                 <div class="form-group">
                  <label for="exampleInputEmail1">SubCategory Name</label><br/>         
				<?php
                 if($_SESSION['type_app']  == 'ADMIN'){
                    $sql1 = "SELECT * FROM `subcategories` ";
                    $check1 = mysqli_query($conn, $sql1);
                    $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
                 }else{
                    $sql1 = "SELECT * FROM `appuser_subcategories` where `storeadmin_id`='{$_SESSION['id']}' and `type_app_admin`='{$_SESSION['type_app']}'  ";
                    $check1 = mysqli_query($conn, $sql1);
                    $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
                
                  } 
                
                foreach($check1 as $row) {
                ?>                
                 <input name="chk" value="<?php echo $row['Sub_Category_Id']; ?>" type="radio"> <?php echo $row['Sub_Category_Name']; ?>&nbsp;
                <?php } ?>               
                 
                </div>        

                <div class="form-group">
                  <label for="exampleInputEmail1">Sub SubCategory Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Sub SubCategory Name" name="catname" required="required">
                </div>

                <div class="form-group">
                  <label for="exampleInputEmail1">Sub SubCategory Description</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" name="Description" required="required">
                </div>
                
                <div class="form-group">
                  <label for="exampleInputFile">Sub SubCategory Image</label>
                  <input type="file" id="exampleInputFile" name="catimg" required>

                  <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.</p>
                </div>
                
              </div>
        
      <div class="modal-footer">
          <input type="submit" class="btn btn-success" value="Add Sub SubCategory" name="addcat">
      </form>
</div>
      </div>
    </div>

  </div>
</div>      