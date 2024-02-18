<?php
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
require_once("lib/t3storelib.php");

$app = new t3storeLib();
?>
<!-- Modal content-->
<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title"> <i class="fa fa-dropbox fa-5" aria-hidden="true"></i>All Products</h4>
    </div>
    <div class="modal-body">
        <a href="add-product.php">
            <button class="btn btn-primary pull-left"><i class="fa fa-plus-circle fa-6" aria-hidden="true"></i> Add Product</button>
        </a>
        <hr>
        <script language="JavaScript" type="text/javascript">
        function checkDelete() {
            return confirm("Are you sure you want to delete?");
        }
        </script>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Manage Product</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="employee_data" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th style="width:15%">Product Name</th>
                                        <th>UPC</th>
                                        <th>Image</th>
                                        <th>In-Stock</th>
                                        <th>Regular Price</th>
                                        <th>Sell Price</th>
                                        <th>Action</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>

$(document).ready(function(){
    $.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
        console.log(message);
    };
    $.fn.dataTable.ext.errMode = "none";
    $('#employee_data').DataTable({
        'processing': true,
        'serverSide': true,
        'ajax': {
            'type': 'POST',
            'url': 'lib/ajaxlib.php',
        },
        'columns': [
            { data: 'id' },
            { data: 'product_name' },
            { data: 'UPC' },
            { data: 'image' },
            { data: 'quantity' },
            { data: 'Regular_Price' },
            { data: 'sellprice' },
            { data: 'Action' },
            { data: 'Manage' },
        ],
        'error': function(jqXHR, exception) {
            alert("Something went wrong.");
            console.log(exception);
        },
    });
});
</script>

<?php
require_once("scriptfooter.php");
?>