<?php
    namespace App\Services\Sale;

    use App\Http\Resources\Sale\SaleResource;
    use App\Repositories\Sale\SaleRepository;
    use App\Repositories\Product\ProductRepository;
    use Exception;
    use Illuminate\Http\Request;
    use App\Notifications\Product\ProductAlertNotification;
    use App\Repositories\Auth\AuthRepository;
    use Spatie\Permission\Models\Role;
    use App\Models\User;

    class SaleService
    {
        private $saleRepository;
        private $productRepository;
        private $authRepository;

        /**
         * -----------------------------------------------------
         * handdle construct for callind repositories
         * -----------------------------------------------------
         * @param ProductRepository $productRepository
         * @param SaleRepository $saleRepository
         */
        public function __construct(ProductRepository $productRepository,SaleRepository $saleRepository,AuthRepository $authRepository)
        {
            $this->saleRepository = $saleRepository;
            $this->productRepository = $productRepository;
            $this->authRepository = $authRepository;
        }

        /**
         * --------------------------------------------------
         * function for storing new sale in db 
         * --------------------------------------------------
         * @param Request $request
         * 
         * @return [type]
         */
        public function store(Request $request)
        {
            try {
                $saleData = $request->only([
                    'payment_status',
                    'client_id',
                ]);
                $saleData['total_amount'] = $this->calculateSaleTotalAmount($request['products']); //call total amount calculate method
                $sale = $this->saleRepository->create($saleData); //add new sale

                //iterate over the product array to attach product to sale
                foreach ($request['products'] as $productSale) {
                    $product = $this->productRepository->find($productSale['product_id']); //find product to associate to sale
                    
                    //check available quantity 
                    if($product->quantity < $productSale['quantity_sold']){
                        return response()->json([
                            'message' => "La quantité en stock est insuffisante pour le produit {$product->name}. Disponible:{$product->quantity}, Demandé:{$productSale['quantity_sold']} "
                        ],400);
                    }
                    $product->quantity -= $productSale['quantity_sold']; //subtract quantity sold from available quantity
                    $product->save(); //save it

                    $user = User::role(['admin', 'manager'])->get(); //retrieve user with role admin or manager
                    //check alert threshold
                    if($product->quantity <= $product->alert_threshold){
                        foreach ($user as $user) {
                            $user->notify(new ProductAlertNotification($product)); //send notification to user
                        }
                    }

                    //attach product to created sale 
                    $this->saleRepository->attachProductTosale($sale,$product,[
                        'quantity_sold'=> $productSale['quantity_sold'],
                        'sale_amount' => $product->price * $productSale['quantity_sold']
                        ]
                    );
                }
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la création de la vente.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return the created data with pivot
            return response()->json([
                'message' => "Vente créée avec succès.",
                'sale' => $sale->load('products'),
            ],201);
        }

        /**
         * -------------------------------------------------
         * function for showing a specific sale
         * -------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function find($id)
        {
            $sale = $this->findSale($id); //find sale to return

            //return finded data 
            return response()->json([
                'Message' => "Résultat correspondant :",
                'sale' => $sale,
            ],200);
        }

        /**
         * ----------------------------------------------------
         * function for updating a specific sale data
         * ----------------------------------------------------
         * @param mixed $id
         * @param Request $request
         * 
         * @return [type]
         */
        public function update($id,Request $request)
        {
            try {
                    $findSale = $this->findSale($id); //find sale to update

                    $saleData = $request->only([
                        'payment_status',
                        'client_id',
                    ]);
                    $saleData['total_amount'] = $this->calculateSaleTotalAmount($request['products']); //call total amount calculate methode
                    $this->saleRepository->update($findSale->id,$saleData); //update sale

                    //iterate over the product array to attach product to sale
                    foreach ($request['products'] as $productSale) {
                        $product = $this->productRepository->find($productSale['product_id']); //find product to associate to sale
                        
                        //check available quantity
                        if($product->quantity < $productSale['quantity_sold']){
                            return response()->json([
                                'message' => "La quantité en stock est insuffisante pour le produit {$product->name}. Disponible:{$product->quantity}, Demandé:{$productSale['quantity_sold']} "
                            ],400);
                        }
                        $product->quantity -= $productSale['quantity_sold']; //subtract quantity sold from available quantity
                        $product->save(); //save it

                        $user = User::role(['admin', 'manager'])->get(); //retrieve user with role admin or manager
                        //check alert threshold
                        if($product->quantity <= $product->alert_threshold){
                            foreach ($user as $user) {
                                $user->notify(new ProductAlertNotification($product)); //send notification to user
                            }
                        }

                        //attach product to updated sale 
                        $this->saleRepository->attachProductTosale($findSale,$product,[
                            'quantity_sold'=> $productSale['quantity_sold'],
                            'sale_amount' => $product->price * $productSale['quantity_sold']
                            ]
                        );
                    }
                    $sale = new SaleResource($findSale); //create new sale resource
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la mise à jour des informations de la vente.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return the updated data with pivot as resource
            return response()->json([
                'message' => "Informations mise à jour avec avec succès.",
                'sale' => $sale,
            ],200);   
        }

        /**
         * -------------------------------------------------
         * function for deleting a specific sale from db
         * -------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function delete($id)
        {
            $findSale = $this->findSale($id); //find sale to delete

            $this->saleRepository->delete($findSale->id); //delete finded sale

            //return success deleting message
            return response()->json([
                'message' => "La vente numéro {$findSale->sale_number} a été supprmée avec succès.",
            ],200);
        }

        /**
         * -----------------------------------------------
         * function for retrieving all sales list in db
         * -----------------------------------------------
         * @return [type]
         */
        public function getAll()
        {
            $sale = $this->saleRepository->all(); //get all sales

            //return retrieve list
            return response()->json([
                'message' => "Liste de toutes les ventes :",
                'sale' => $sale,
            ],200);
        }

        /**
         * ----------------------------------------------------
         * private function for finding a sale
         * --------------------------------------------------- 
         * @param mixed $id
         * 
         * @return [type]
         */
        private function findSale($id)
        {
            $sale = $this->saleRepository->find($id); //find sale

            //ensure that sale existe before return it
            if(!$sale){
                throw new Exception('Cette n\'existe pas.'); //throw error if doesn't exist
            }
            return $sale;
        }

        /**
         * ------------------------------------------------------
         * private methode for calculating sale total amount
         * ------------------------------------------------------
         * @param array $products
         * 
         * @return [type]
         */
        private function calculateSaleTotalAmount(array $products)
        {
            $totalAmount = 0; //initialize total amount var

            //iterate over product array to calculate total amount
            foreach ($products as $productSale) {
                $product = $this->productRepository->find($productSale['product_id']); //find product to calculte his amount

                $totalAmount += $product->price * $productSale['quantity_sold']; //calculate any product sale amount and add them to get total amount
            }
            return $totalAmount;
        }

        /**
         * ----------------------------------------------------
         * private function to send product threshold alert 
         * to all user with role admin or manager
         * -----------------------------------------------------
         * @param mixed $product
         * 
         * @return [type]
         */
        private function sendProductAlertNotification($product)
        {
            $users = $this->authRepository->retrieveUserWithRoleAdminOrManager(); //retrieve user with role admin or manager

            //iterate over user array and send notification to all
            foreach ($users as $user) {
                $user->notify(new ProductAlertNotification($product));
            }

            //return success sending message
            return response()->json([
                'message' => "Alerte envoyée avec succès.",
            ],200);
        }
    }