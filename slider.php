<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
 
      
      
      <!-- Modal content-->
    <div class="modal-content ">
      
      <div class="modal-body">
      
     
  
            
            <?php  $sql="SELECT  count(id) FROM `slider` "; 
             $check= mysqli_query($conn, $sql);
             //var_dump($check);
           $row = mysqli_fetch_array($check,MYSQLI_BOTH);

$total = $row[0];
//echo "Total rows: " . $total;?>
            <?php if($total>=0 && $total<5){?>
             <button class="btn btn-primary " class="pull-left" data-toggle="modal" data-target="#myModal">Add Banner</button>
             <?php } else { ?> <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i>Banner limit reached!</h4>
                Please delete existing banner to add new one.
              </div><?php } ?>


             <hr>
                 <br>
            <!--<h1>hello</h1>-->
            
<div class="box">
            <div class="box-header">
              <h3 class="box-title"><i class="fa fa-list-ol fa-5" aria-hidden="true"></i>All Banners</h3>

              <div class="box-tools">
                  
                <div class="input-group input-group-sm" style="width: 150px;">
                  


                  <div class="input-group-btn">
                      
                  </div>
                  
                </div>
                     
              </div>
            </div>


            <!-- /.box-header -->

<div class="box-body table-responsive no-padding">
              <table id="employee_data" class="table table-bordered table-hover">
                <thead><tr>
                  
                  <th>Product-Name</th>
                  <th>Image</th>
                  <th >Action</th>

                </tr></thead>
               
                <?php  $sql1="SELECT  * FROM `slider` "; 
             $check1= mysqli_query($conn, $sql1);
             //var_dump($check);
           $rowdata = mysqli_fetch_array($check1,MYSQLI_BOTH);
          
           
           foreach($check1 as $row1){





                
                
                ?>
                <tr>
                

                                   <td><?php echo $row1['product_name']; ?></td>

                  <td><img src="<?php echo $row1['image'];?>" height="100" width="250"></td>
                   <td><a href="deleteslider.php?id=<?php echo base64_encode($row1['id']);?>" class="btn btn-social-icon btn-google" onClick="return checkDelete()" ><i class="fa fa fa-trash-o"></i></a>
                   
                   
 <?php   } ?>
                </tr>
              </tbody></table></div></div>
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
 </script


      ><?php include 'scriptfooter.php'; ?>
      
      
      
     <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add New Banner</h4>
      </div>
      <div class="modal-body">
        
          <form role="form" action="add-slide.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Product Link</label><br>
                  <select name="product_id" class="selectpicker" data-live-search="true" id="news" style="color: #fff;" title="Choose product" required >
										
										<?php
								$cats = mysqli_query($conn,"SELECT * FROM `app_productsmain`");
								while($rows = mysqli_fetch_array($cats)){
										?>

										<option  class="text-dark" value="<?php echo $rows['product_id']; ?>" id="<?php echo $rows['product_id'];?>"><?php echo ucfirst($rows['product_name']); ?></option>
								<?php } ?>
									</select>
                </div>
                
                
                
                
                <div class="form-group">
                  <label for="exampleInputFile">Banner Image</label>
                  <input type="file" id="exampleInputFile" name="catimg" required>

                  <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.</p>
                </div>
                
              </div>
        
        
        
      
      <div class="modal-footer">
          <input type="submit" class="btn btn-success" value="Add Banner" name="addcat">
      </form>
</div>
      </div>
    </div>

  </div>
</div>