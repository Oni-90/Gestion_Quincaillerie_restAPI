<?php

    namespace App\Http\Controllers\Api\v1\Manager;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Manager\ManagerRequest;
    use App\Http\Requests\Manager\UpdateMangerRequest;
    use App\Services\Manager\ManagerService;

    class ManagerController extends Controller
    {
        private $managerService;

        /**
         * ---------------------------------------------
         * handdle constructor to call services
         * ---------------------------------------------
         * @param ManagerService $managerService
         */
        public function __construct(ManagerService $managerService)
        {
            $this->managerService = $managerService;
        }

        /**
         * ---------------------------------------------
         * store new manager
         * ---------------------------------------------
         * @param ManagerRequest $request
         * 
         * @return [type]
         */
        public function store(ManagerRequest $request)
        {
            return $this->managerService->store($request);
        }

        /**
         * ------------------------------------------------
         * update userManger Data
         * ------------------------------------------------
         * @param mixed $id
         * @param ManagerRequest $request
         * 
         * @return [type]
         */
        public function update(UpdateMangerRequest $request)
        {
            return $this->managerService->update($request);
        }

        /**
         * -------------------------------------------------
         * show a userManager 
         * -------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function show($id)
        {
            return $this->managerService->find($id);
        }

        /**
         * -------------------------------------------------
         * destroy userManager
         * -------------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function destroy($id)
        {
            return $this->managerService->delete($id);
        }

        /**
         * ---------------------------------------------------
         * get all managers 
         * ---------------------------------------------------
         * @return [type]
         */
        public function index()
        {
            return $this->managerService->getAll();
        }
    }
