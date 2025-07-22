<?php

    namespace App\Services\Manager;

    use App\Http\Resources\Manager\ManagerResource;
    use App\Repositories\Manager\ManagerRepository;
    use App\Repositories\Auth\AuthRepository;
    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class ManagerService
    {
        private $managerRepository;
        private $authRepository;

        /**
         * --------------------------------------------------------------
         * handdle constructor for manager service to call nedeed repositories
         * --------------------------------------------------------------
         * @param ManagerRepository $managerRepository
         * @param AuthRepository $authRepository
         */
        public function __construct(ManagerRepository $managerRepository, AuthRepository $authRepository)
        {
            $this->managerRepository = $managerRepository;
            $this->authRepository = $authRepository;
        }

        /**
         * ------------------------------------------------------------
         * function for creating a new manager associate to an user
         * ------------------------------------------------------------
         * @param Request $request
         * 
         * @return [type]
         */
        public function store(Request $request)
        {

            try {
                    $userData = $request->only([
                        'email',
                        'password',
                        'lastname',
                        'firstname',
                        'phone',
                    ]);
                    $userData['type'] = 'manager';
                    $user = $this->authRepository->create($userData); //store user data
                    $user->assignRole('manager'); //assign role manager to user
                    
                    $managerData = $request->only([
                        'address',
                    ]);
                    $managerData['user_id'] = $user->id;

                    //verify the user data isn't null
                    if(is_null($user)){
                        return response()->json([
                            'message' => "Une erreur est survenue.",
                        ],400);
                    }
                    $manager = $this->managerRepository->create($managerData); //store admin data   
                } 
                catch (Exception $exception) {
                    return response()->json([
                        'message' => "Une erreur est survenue lors de la création du compte.",
                        'error' => $exception->getMessage(),
                    ],500);
                }

                //return data store
                return response()->json([
                    'message' => 'Compte créer avec succès.',
                    'user' => $user,
                    'manager' => $manager,
                ],201);
        }

        /**
         * -------------------------------------------------
         * find a specific admin by id
         * -------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function find($id)
        {
            $manager = $this->findManager($id); //find manager

            //return retrieve manager
            return response()->json([
                'message' => "Résultat correspondant :",
                'manager' => $manager,
            ],200);
        }

        /**
         * ---------------------------------------------------
         * function for updating a specific manager info
         * ---------------------------------------------------
         * @param mixed $id
         * @param Request $request
         * 
         * @return [type]
         */
        public function update(Request $request)
        {
            try {
                    $user = Auth::user();
                    $findManager = $this->findManager($user->id); //find manager to update

                    $userData = $request->only([
                        'email',
                        'password',
                        'lastname',
                        'firstname',
                        'phone',
                    ]);
                    $this->authRepository->update($findManager->user_id,$userData); //update user data

                    $managerData = $request->only([
                        'address',
                    ]);
                    $this->managerRepository->update($findManager->id, $managerData); //update manager data

                    $data = new ManagerResource($findManager); //create new ManagerResource
                } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la mise à jour des informations du compte.",
                    'error' => $exception->getMessage(),
                ],500);
                }

            //return data updated as resource
            return response()->json([
                'message' => "Informations mise à  avec succès.",
                'data' => $data,
            ],200);
        }

        /**
         * ----------------------------------------------------
         * function for deleting a specific manager account
         * ----------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function delete($id)
        {
            $findManager = $this->findManager($id); //find manager to delete

            $this->authRepository->delete($findManager->user_id); //delete userManager

            //return success delete message
            return response()->json([
                'message' => "Le compte a été supprimé avec succès.",
            ],200);
        }

        /**
         * ----------------------------------------------------------
         * get all manager liste
         * ----------------------------------------------------------
         * @return [type]
         */
        public function getAll()
        {
            $manager = $this->managerRepository->all(); //get all manager in database to show

            //return list
            return response()->json([
                'message' => "Liste des tous les managers :",
                'managers' => $manager,
            ],200);
        }

        /**
         * ------------------------------------------------
         * private function to find manager
         * ------------------------------------------------
         * @param int $id
         * 
         * @return [type]
         */
        private function findManager(int $id)
        {
            $manager = $this->managerRepository->find($id); //find manager

            //check that manager exist
            if(!$manager){
                throw new Exception('Cet utilisateur n\'existe pas'); //throw error if doesn't esxist
            }
            return $manager;
        }
    }