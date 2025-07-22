<?php

    namespace App\Services\Auth;

    use App\Http\Requests\Auth\AuthRequest;
    use App\Repositories\Auth\AuthRepository;
    use Exception;
    use Illuminate\Support\Facades\Auth;

    class AuthService
    {
        private $authRepository;

        /**
         * --------------------------------------------
         * handdle constructor to call the repository
         * --------------------------------------------
         * @param AuthRepository $authRepository
         */
        public function __construct(AuthRepository $authRepository)
        {
            $this->authRepository = $authRepository;
        }

        /**
         * --------------------------------------------
         * define login method for user
         * --------------------------------------------
         * @param AuthRequest $request
         * 
         * @return [type]
         */
        public function login(AuthRequest $request)
        {
            $userForLogin = $request->validated();

            try {
                if (! $token = Auth::attempt($userForLogin)) {
                    return response()->json(['error' => 'Email ou Mot de passe incorrecte.'], 401);
                }
            } 
            catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la connexion.",
                    'error' => $exception->getMessage(),
                ],400);
            }

            //return authenticated user
            return response()->json([
                'token' => $token,
                'auth' => Auth::user(),
                'message' => "Utilisateur connecté avec succès.",
                // 'cookie' => $cookie,
            ]);
        }

        /**
         * ----------------------------------------------
         * logout method handdling
         * ----------------------------------------------
         * @param mixed $request
         * 
         * @return [type]
         */
        public function logout()
        {
            try {
                Auth::logout();
            } catch (Exception $exception) {
                return response()->json([
                    'message' => "Une erreur est survenue lors de la déconnexion.",
                    'error' => $exception->getMessage()
                ],404);
            }
            return response()->json([
                'message' => "Déconnexion réeussi.",
                'success' => true,
            ],200);
        }
    }