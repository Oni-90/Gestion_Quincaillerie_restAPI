<?php

    namespace App\Services\Admin;

    use App\Http\Resources\Admin\AdminResource;
    use App\Repositories\Admin\AdminRepository;
    use App\Repositories\Auth\AuthRepository;
    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class AdminService
    {
        private $adminRepository;
        private $authRepository;

        /**
         * -----------------------------------------------------------
         * function for handdle a constructor to call a repository
         * -----------------------------------------------------------
         * @param AdminRepository $adminRepository
         * @param AuthRepository $authRepository
         */
        public function __construct( AdminRepository $adminRepository, AuthRepository $authRepository)
        {
            $this->adminRepository = $adminRepository;
            $this->authRepository = $authRepository;

        }

        /**
         * ------------------------------------------------------
         * function for creating a new user admin
         * ------------------------------------------------------
         * @param Request $request
         * 
         * @return [type]
         */
        public function store(Request $request)
        {
            try {
                $userData = $request->only([
                    'email',
                    'lastname',
                    'firstname',
                    'password',
                    'phone',
                ]);
                $userData['type'] = 'admin';
                $user = $this->authRepository->create($userData); //store user data
                $user->assignRole('admin'); //assign role admin to user
    
                $adminData = $request->only([
                    'username',
                ]);
                $adminData['user_id'] = $user->id;

                if(is_null($user)){
                    return response()->json([
                        'erreur' => "Une erreur est survenue",
                    ],400);
                }
                $admin = $this->adminRepository->create($adminData); //store admin data
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la création du compte.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return stored data
            return response()->json([
                'message' => "Compte admin créer avec succès.",
                'user' => $user,
                'admin' => $admin,
            ],201);    
        }

        /**
         * ----------------------------------------------------
         * find a specific admin by id
         * ----------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function find($id)
        {
            $admin = $this->findAdmin($id); //find admin

            //return retrieve admin
            return response()->json([
                'message' => "Résultat correspondant :",
                'admin' => $admin,
            ],200);
        }

        /**
         * --------------------------------------------------------------
         * function to update admin's data
         * --------------------------------------------------------------
         * @param mixed $id
         * @param Request $request
         * 
         * @return [type]
         */
        public function update(Request $request)
        {
            try {
                    $user = Auth::user();
                    $findAdmin = $this->findAdmin($user->id); //find admin to update
                    
                    $userData = $request->only([
                        'email',
                        'lastname',
                        'firstname',
                        'password',
                        'phone',
                    ]);
                    $this->authRepository->update($findAdmin->user_id,$userData); //update user data

                    $adminData = $request->only([
                        'username',
                    ]);
                    $this->adminRepository->update($findAdmin->id,$adminData); //update admin data

                    $data = new AdminResource($findAdmin); //create new adminResource
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la mise à des informations du comtpe.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return updated data as resource
            return response()->json([
                'message' => "Informations mise à jour avec succès.",
                'data' => $data,
            ],200);
        }

        /**
         * ----------------------------------------------
         * function for deleting a specific admin
         * ----------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function delete($id)
        {
            $findAdmin = $this->findAdmin($id); //find admin to delete

            $this->authRepository->delete($findAdmin->user_id); //delete userAdmin

            //retrun success message
            return response()->json([
                'message' => "Le compte a été supprimé avec succès."
            ],200);
        }

        /**
         * --------------------------------------------------
         * get all admin liste
         * --------------------------------------------------
         * @return [type]
         */
        /**
         * @return [type]
         */
        public function getAll()
        {
           $admin = $this->adminRepository->all(); //retrieve all admin existing in db

           //return list
           return response()->json([
                'message' => "Liste de tous les admins :",
                'admins' => $admin,
           ],200);
        } 

        /**
         * ----------------------------------------------------------------
         * private function to find user Admin
         * ----------------------------------------------------------------
         * @param int $id
         * 
         * @return [type]
         */
        private function findAdmin(int $id)
        {
            $admin = $this->adminRepository->find($id); //find admin

            //check if admin exist
            if(!$admin){
                throw new Exception('Cet utilisateur n\'existe pas.'); //throw error if admin doesn't exist
            }
            return $admin;
        }
    }