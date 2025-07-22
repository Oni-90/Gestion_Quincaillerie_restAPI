<?php

    namespace App\Services\Client;

    use App\Http\Resources\Client\ClientResource;
    use App\Repositories\Client\ClientRepository;
    use App\Repositories\Auth\AuthRepository;
    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class ClientService 
    {
        private $clientRepository;
        private $authRepository;

        /**
         * ----------------------------------------------
         * constructor to call clientRepository
         * ----------------------------------------------
         * @param ClientRepository $clientRepository
         * @param AuthRepository $authRepository
         */
        public function __construct(ClientRepository $clientRepository, AuthRepository $authRepository)
        {
            $this->clientRepository = $clientRepository;
            $this->authRepository = $authRepository;
        }

        /**
         * ----------------------------------------------------------------
         * function for stored a new client in dataBase
         * ----------------------------------------------------------------
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
                    'firstname',
                    'lastname',
                    'phone',
                ]);
                $userData['type'] = 'client';
                $user = $this->authRepository->create($userData); //save user data
                $user->assignRole('client'); //assing role client to user

                $clientData = $request->only([
                    'address',
                ]);
                $clientData['user_id'] = $user->id;

                //verify if user is not null before create client
                if(is_null($user)){
                    return response()->json([
                        'erreur' => "Une erreur est survenue.",
                    ],400);
                }
                $client = $this->clientRepository->create($clientData); //save client data
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la création du compte.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return data stored
            return response()->json([
                'message' => 'Compte créer avec succès.',
                'user' => $user,
                'client' => $client,
            ],201);
        }

        /**
         * --------------------------------------------------------
         * function retrieving a specific client
         * --------------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function find($id)
        {
            $client = $this->findClient($id); //find client

            //return retrieve client
            return response()->json([
                'message' => "Résultat correspondant :",
                'client' => $client,
            ],200);
        }

        /**
         * ------------------------------------------------------------------
         * function for updating user data and client data
         * ------------------------------------------------------------------
         * @param mixed $id
         * @param Request $request
         * 
         * @return [type]
         */
        public function update(Request $request)
        {
            try {
                    $user = Auth::user(); 
                    $findClient = $this->findClient($user->id); //find client to update

                    $userData = $request->only([
                        'email',
                        'phone',
                        'password',
                        'firstname',
                        'lastname',
                    ]);
                    $this->authRepository->update($findClient->user_id,$userData); //update user data

                    $clientData = $request->only([
                        'address',
                    ]);
                    $this->clientRepository->update($findClient->id,$clientData); //update client data

                    $data = new ClientResource($findClient); //create new ClientResource
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la mise des informations.",
                    'error' => $exception->getMessage(),
                ],500);
            }

            //return updated data as ressource
            return response()->json([
                'message' => "Informations mise à jour avec succès.",
                'data' => $data,
            ],200);
        }

        /**
         * ------------------------------------------------------
         * function for deleting specific userClient
         * ------------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function delete($id)
        {
            $findClient = $this->findClient($id); //find client to delete

            $this->authRepository->delete($findClient->user_id); //delete userClient

            return response()->json([
                'message' => "Le compte a été supprimé avec succès.",
            ],200);
        }

        /**
         * ---------------------------------------------------------
         * retrieve all client list in dataBase
         * ---------------------------------------------------------
         * @return [type]
         */
        public function getAll()
        {
            $client = $this->clientRepository->all(); //retrieve all client in db

            //return list
            return response()->json([
                'message' => "Liste de tous les clients :",
                'clients' => $client,
            ],200);
        }

        /**
         * -------------------------------------------------------
         * private function to find client 
         * -------------------------------------------------------
         * @param int $id
         * 
         * @return [type]
         */
        private function findClient(int $id)
        {
            $client = $this->clientRepository->find($id); //find client

            //check if client exist
            if(!$client){
                throw new Exception('Cet utilisateur n\'existe pas.'); //throw error if doesn't exist
            }
            return $client;
        }
    }