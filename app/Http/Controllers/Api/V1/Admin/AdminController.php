<?php

    namespace App\Http\Controllers\Api\V1\Admin;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Admin\AdminRequest;
    use App\Http\Requests\Admin\UpdateAdminRequest;
    use App\Services\Admin\AdminService;

    class AdminController extends Controller
    {
        private $adminService;

        /**
         * ------------------------------------------------
         * handdle constructor for call services
         * ------------------------------------------------
         * @param AdminService $adminService
         */
        public function __construct(AdminService $adminService)
        {
            $this->adminService = $adminService;
        }

        /**
         * ----------------------------------------------
         * store new admin 
         * ----------------------------------------------
         * @param AdminRequest $request
         * 
         * @return [type]
         */
        public function store(AdminRequest $request)
        {
            return $this->adminService->store($request);
        }

        /**
         * -------------------------------------------------
         * update a userAdmin
         * -------------------------------------------------
         * @param mixed $id
         * @param AdminRequest $request
         * 
         * @return [type]
         */
        public function update(UpdateAdminRequest $request)
        {
            return $this->adminService->update($request);
        }

        /**
         * ----------------------------------------------
         *show a userAdmin
         * ----------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function show($id)
        {
            return $this->adminService->find($id);
        }

        /*
        *----------------------------------------------
         *destroy a userAdmin
         *---------------------------------------------
         * @param mixed $id
         * 
         * @return [type]
         */
        public function destroy($id)
        {
            return $this->adminService->delete($id);
        }

        /**
         * ----------------------------------------------
         * get all admins
         * ----------------------------------------------
         * @return [type]
         */
        public function index()
        {
            return $this->adminService->getAll();
        }
    }
