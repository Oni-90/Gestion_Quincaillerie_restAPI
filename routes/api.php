<?php

    use App\Http\Controllers\Api\V1\Admin\AdminController;
    use App\Http\Controllers\Api\V1\Auth\AuthController;
    use App\Http\Controllers\Api\V1\Category\CategoryController;
    use App\Http\Controllers\Api\V1\Client\ClientController;
    use App\Http\Controllers\Api\v1\Manager\ManagerController;
    use App\Http\Controllers\Api\V1\Order\OrderController;
    use App\Http\Controllers\Api\V1\Product\ProductController;
    use App\Http\Controllers\Api\V1\RolePermission\RolePermissionController;
    use App\Http\Controllers\Api\V1\Sale\SaleController;
    use App\Http\Controllers\Api\V1\Supplier\SupplierController;
    use App\Http\Controllers\Api\V1\Notification\NotificationController;
    use Illuminate\Support\Facades\Route;


    //login route
    Route::controller(AuthController::class)->group(function(){
        Route::post('login', 'login');
    });

    //routes for client storing
    Route::controller(ClientController::class)->group(function(){
        Route::post('clients', 'store');
    });

    /**
     * ----------------------------
     * authentificate routes
     * ----------------------------
     */
    Route::group(['middleware' => 'auth','prefix' => 'auth'],function(){

        //all routes only admin can manipulate
        Route::middleware(['role:admin'])->group(function(){

            //routes for manager manipulations
            Route::controller(ManagerController::class)->group(function(){
                Route::post('managers', 'store');
                Route::put('managers', 'update');
                Route::get('managers/{id}', 'show');
                Route::get('managers', 'index');
                Route::delete('managers/{id}', 'destroy');
            });

            //routes for admin manipulations
            Route::controller(AdminController::class)->group(function(){
                Route::post('admins', 'store');
                Route::put('admins', 'update');
                Route::get('admins/{id}', 'show');
                Route::get('admins', 'index');
                Route::delete('admins/{id}', 'destroy');
            });

            //routes for roles and permissions getting
            Route::controller(RolePermissionController::class)->group(function(){
                Route::get('roles','getAllRoles');
                Route::get('permissions','getAllPermissions');
            });

            //other destroying routes
            Route::delete('categories/{id}', [CategoryController::class,'destroy']);
            Route::delete('products/{id}', [ProductController::class,'destroy']);
            Route::delete('orders/{id}', [OrderController::class,'destroy']);
            Route::delete('suppliers/{id}', [SupplierController::class,'destroy']);
            Route::delete('sales/{id}', [SaleController::class,'destroy']);
        });

        //routes for client manipulations
        Route::controller(ClientController::class)->middleware(['role:admin|client|manager'])->group(function(){
            Route::put('clients', 'update');
            Route::get('clients/{id}', 'show');
            Route::get('clients', 'index');
            Route::delete('clients/{id}', 'destroy');
        });

        //routes for category manipulations
        Route::controller(CategoryController::class)->middleware(['role:admin|manager'])->group(function(){
            Route::post('categories', 'store');
            Route::put('categories/{id}','update');
            Route::get('categories/{id}', 'show');
            Route::get('categories', 'index');
        });

        //routes for notification manipulations
        Route::controller(NotificationController::class)->middleware(['role:admin|manager|client'])->group(function(){
            Route::get('notifications', 'index');
            Route::put('notifications/{id}', 'markAsRead');
        });

        //routes for product manipulations
        Route::controller(ProductController::class)->middleware(['role:admin|manager'])->group(function(){
            Route::post('products', 'store');
            Route::put('products/{id}','update');
            Route::get('products/{id}', 'show');
            Route::get('products', 'index');
        });

        //routes for order manipulations
        Route::controller(OrderController::class)->middleware(['role:admin|manager'])->group(function(){
            Route::post('orders','store');
            Route::put('orders/{id}','update');
            Route::get('orders/{id}', 'show');
            Route::get('orders', 'index');
        });

        //routes for supplier manipulations
        Route::controller(SupplierController::class)->middleware(['role:admin|manager'])->group(function(){
            Route::post('suppliers','store');
            Route::put('suppliers/{id}','update');
            Route::get('suppliers/{id}', 'show');
            Route::get('suppliers', 'index');
        });

        //routes for sale mainpulations
        Route::controller(SaleController::class)->middleware(['role:admin|manager'])->group(function(){
            Route::post('sales','store');
            Route::put('sales/{id}','update');
            Route::get('sales/{id}', 'show');
            Route::get('sales', 'index');
        });

        //logout route
        Route::controller(AuthController::class)->middleware(['role:admin|manager|client'])->group(function(){
            Route::post('logout', 'logout');
        });
    });
   
   

  

  

   


