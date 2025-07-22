<?php

    namespace App\Http\Controllers\Api\V1\Client;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Client\ClientRequest;
    use App\Http\Requests\Client\UpdateClientRequest;
    use App\Services\Client\ClientService;

    class ClientController extends Controller
    {
        private $clientService;

        /**
         * ----------------------------------------------------
         * define constructor to call services
         * ----------------------------------------------------
         * @param ClientService $clientService
         */
        public function __construct(ClientService $clientService)
        {
            $this->clientService = $clientService;
        }

        /**
         * ----------------------------------------------------
         * store new client 
         * ----------------------------------------------------
         * @param ClientRequest $request
         * 
         * @return [type]
         */
        public function store(ClientRequest $request)
        {
            return $this->clientService->store($request);
        }

        /**
         * -----------------------------------------------------
         * update a userClient 
         * -----------------------------------------------------
         * @param mixed $id
         * @param ClientRequest $request
         * 
         * @return [type]
         */
        public function update(UpdateClientRequest $request)
        {
            return $this->clientService->update($request);
        }

        /**
         * -----------------------------------------------------
         * show a client
         * -----------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function show($id)
        {
            return $this->clientService->find($id);
        }

        /**
         * --------------------------------------------------
         * destroy a userClient
         * --------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function destroy($id)
        {
            return $this->clientService->delete($id);
        }

        /**
         * ---------------------------------------------------
         * get all clients
         * ---------------------------------------------------
         * @return [type]
         */
        public function index()
        {
            return $this->clientService->getAll();
        }
    }
