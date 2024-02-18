$(document).ready(function () {
    loadSidebarStats();
    loadOrderStats();
});

function loadSidebarStats() {
    $.ajax({
        type: "GET",
        url: "webapi/sidebarstats.php",
        dataType: 'json',
        beforeSend: function () {
            // $('#loader').removeClass('hidden')
        },
        success: function (data) {
            $('#usersCount').html(data.data.usersCount);
            $('#categoriesCount').html(data.data.categoriesCount);
            $('#subcategoriesCount').html(data.data.subcategoriesCount);
            $('#subSubcategoriesCount').html(data.data.subSubcategoriesCount);
            $('#productsCount').html(data.data.productsCount);
            $('#ordersCount').html(data.data.ordersCount);
            $('#newProductsCount').html(data.data.newProductsCount);

            $('#ordersCount2').html(data.data.ordersCount);
            $('#categoriesCount2').html(data.data.categoriesCount);
            $('#productsCount2').html(data.data.productsCount);
            $('#usersCount2').html(data.data.usersCount);
        },
    });
}

function loadOrderStats() {
    $.ajax({
        type: "GET",
        url: "webapi/orderstats.php",
        dataType: 'json',
        beforeSend: function () {
            // $('#loader').removeClass('hidden')
        },
        success: function (data) {
            $('#totalRevenue').html(data.data.totalRevenue);
            $('#ordersCompletedCount').html(data.data.ordersCompletedCount);
            $('#ordersInProcessingCount').html(data.data.ordersInProcessingCount);
            $('#ordersCancelledCount').html(data.data.ordersCancelledCount);
        },
    });
}