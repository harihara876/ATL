<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
<?php include("lib/t3storelib.php");
$app = new t3storeLib();
?>

    <!-- Modal content-->
    <div class="modal-content ">
    <div class="modal-header">
      <h4 class="modal-title"> <i class="fa fa-list-ol fa-5" aria-hidden="true"></i>Sub Category
    </li>
    </h4>
    </div>
    <div class="modal-body">
	  <?php if($_SESSION['type_app']  == 'ADMIN'){  ?>
      <button class="btn btn-primary pull-left" data-toggle="modal" data-target="#myModal">Add Sub Category</button>
      <hr>
      <?php } ?>
      <br>
      <section class="content">
      <div class="row">
        <div class="col-xs-12">
              <div class="box">
            <div class="box-header">
                  <h3 class="box-title">All Sub Category List</h3>
                </div>
            <!-- /.box-header -->
            <div class="box-body">
               <table id="employee_data" class="table table-bordered table-hover">
                <thead>
                      <tr>
                    <th>Category</th>
                    <th>Sub Category Name</th>
                    <th>Image</th>
                    <th >Action</th>
                  </tr>
                    </thead>
                <?php
					  $result = $app->getSubCategories($_SESSION['id'],$_SESSION['type_app']);
					  if($_SESSION['type_app']  == 'ADMIN'){
						  foreach ($result as $k) {
						    if(isset($k['Sub_Category_Id']) ? : 0){
							 echo '<tr>
								   <td>'.$k['category_name'].'</td>
								   <td>'.$k['Sub_Category_Name'].'</td>
								   <td><img src="'. $k['Image'].'" height="100" width="100"></td>
								   <td><a href="editSubCategory.php?id='.base64_encode($k['Sub_Category_Id']).'"><button class=" btn btn-primary btn-warning" >Edit<i class="fa fa-pencil" aria-hidden="true"></i></button></a>
								  <a href="deleteSubCategory.php?id='.base64_encode($k['Sub_Category_Id']).'" class="btn btn-social-icon btn-google" onClick="return checkDelete()" ><i class="fa fa fa-trash-o"></i></a></td> </tr>';
							}
						   }
					  }else {
						  foreach ($result as $k) {
							if($k['Sub_Category_Id']){
							 echo '<tr>
								   <td>'.$k['category_name'].'</td>
								   <td>'.$k['Sub_Category_Name'].'</td>
								   <td><img src="'. $k['Image'].'" height="100" width="100"></td>
								   <td></td> </tr>';
							}
						   }
					  }
   ?>
                  </tbody>
              </table>
                </div>
          </div>
            </div>
      </div>
      </section>
     </div>
  </div>

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
        <h4 class="modal-title">Add New Sub Category</h4>
      </div>
      <div class="modal-body">
        <form role="form" action="add-subcat.php" method="POST" enctype="multipart/form-data">
          <div class="box-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Category Name</label>
              <br/>
              <?php
			    $result = $app->getCategories($_SESSION['id'],$_SESSION['type_app']);
				foreach ($result as $row) { 
                if (isset($row['category_name'])) { ?>
                  <input name="chk" value="<?php echo $row['cat_id']; ?>" type="radio">
                <?php
                 echo $row['category_name']; ?>&nbsp;
              <?php } } ?>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Sub Category Name</label>
              <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Sub Category Name" name="catname" required="required">
            </div>
            <div class="form-group">
              <label for="exampleInputFile">Sub Category Image</label>
              <input type="file" id="exampleInputFile" name="catimg" required>
              <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.</p>
            </div>
          </div>
          <input type="submit" class="btn btn-success" value="Add Sub Category" name="addcat">
        </form>
      </div>
    </div>
  </div>
</div>

<script language="JavaScript" type="text/javascript">
	function checkDelete(){
		return confirm('Are you sure you want to delete?');
	}
</script>