<?php include 'header.php';?>
<?php include  'sidebar.php';?>
  <?php include 'widget.php';?>
      <?php  
$sql="SELECT * FROM `products` order by id DESC ";
         
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
         
 ?>
        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bell"></i> Push Notification</h3>
            </div><br><center><h3>Choose Notification Type</h3>
            <!-- /.box-header -->
            <!-- form start -->
   <select id='purpose' class="selectpicker" title="Choose Type">
   
<option value="1">Simple Notification</option>
<option value="2">Custom Notification</option>

</select></br></br>
          
            </center>
            </div>
           <br><br><br> <div style='display:none;' id='simple'>
            
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Simple Notification Panel</h3>

      
            <!-- form start -->
            <form class="form-horizontal" action="send.php" method="post" enctype="multipart/form-data">
              <div class="box-body">
                 
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Title</label>

                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputEmail3" placeholder="Title" name="title" required >
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Message</label>

                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputPassword3" placeholder="Message"  name="msg" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Product</label>
                  
                  <div class="col-sm-10">
<select name="place"  class="selectpicker" data-live-search="true"  style="color: #fff;" title="Choose here..."id="place" required >

<?php foreach($check as $pageid){?>

     

<option value="<?php echo $pageid['product_id'];?>" id="<?php echo $pageid['product_id'];?>"><?php echo $pageid['product_name']; ?></option>
  


<?php } ?></select></div></div>
       <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                  
                  <div class="col-sm-10">
                  <script>
                  $(document).ready(function(){
// code to get all records from table via select box
$("#place").change(function() {
var defaultvari='loading.gif'
var id = $(this).find(":selected").val();
var dataString = 'placeid='+ id;
console.log(id);
$.ajax({
url: 'getimg.php',
dataType: "json",
data: dataString,
cache: false,
beforeSend: function(){
        $('#imm').show();
    },
success: function(d){
//alert(d.image); //will alert ok
   $('#imm').attr('src', d.image);
   var imageurl=d.image;
   $.post("send.php",{"imageurl":imageurl});
   //alert(imageurl);
  }
  

});
})
});
                  
                  </script>
                <center><br><img id = "imm" src="complete-details-icon.png" height="200" width="150"> </center> <br> 
                 </div>
</div>

<div class="form-group">
                 
   <center>
<input type="submit" class="btn btn-primary " value="Send Notification" name="simple">
    </center>
    
    </div>
                
             
            </form>
            
            
              </div></div></div></div>


   

<div style='display:none;' id='custom'>
            
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Custom Notification Panel</h3>

      
            <!-- form start -->
            <form class="form-horizontal" action="send.php" method="post" enctype="multipart/form-data">
              <div class="box-body">
                 
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Title</label>

                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputEmail3" placeholder="Title" name="title" required >
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Message</label>

                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputPassword3" placeholder="Message"  name="msg" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Product</label>
                  
                  <div class="col-sm-10">
<select name="place"  class="selectpicker" data-live-search="true"  style="color: #fff;" title="Choose here..."id="place" required >

<?php foreach($check as $pageid){?>

     

<option value="<?php echo $pageid['product_id'];?>" id="<?php echo $pageid['product_id'];?>"><?php echo $pageid['product_name']; ?></option>
  


<?php } ?></select></div></div>
                
       <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Choose Image</label>
                   <input type="file" id="exampleInputFile" class="btn " name="catimg" required>

                  
                  </div>
                
                 

<div class="form-group">
                 
   <div class="col-sm-10">
<input type="submit" class="btn btn-primary pull-right" value="Send Notification" name="custom">
    </div>
    
    </div>
                
             
            </form>
            
            
            </div><br><br><br>



<script>$(document).ready(function(){
    $('#purpose').on('change', function() {
       
      if ( this.value == '1')
      {
        $("#simple").show();
      }
      else
      {
        $("#simple").hide();
      }
       if ( this.value == '2')
      {
        $("#custom").show();
      }
      else
      {
        $("#custom").hide();
      }
    });
});</script>  

<?php include 'scriptfooter.php'; ?>


      

             
            





 

 

  
 















