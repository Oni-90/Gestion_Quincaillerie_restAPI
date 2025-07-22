<?php

    namespace App\Http\Requests\Client;

    use Illuminate\Foundation\Http\FormRequest;

    class ClientRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         */
        public function authorize(): bool
        {
            return true;
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */
        public function rules(): array
        {
            $id = $this->route('id');
            return [
                
                /**
                 * user data validation
                 */
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$&*?!%]/',
                'firstname' => 'required|string|max:80',
                'lastname' => 'required|string|max:80',
                'phone' => 'required|string|unique:users,phone|min:8|max:8',

                /**
                 * manager data validation
                 */
                'address' => 'required|string',
            ];
        }

        /**
         * get message to show for any rule violation
         * @return [type]
         */
        public function messages()
        {
            return [
                'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
                'password.required' => 'Le mot de passe est requis',
                // 'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'firstname.required' => 'Le prénom est requis.',
                'lastname.required' => 'Le  nom est requis.',
                'phone.min' => 'Le numéro de télephone doit contenir au moins 8 chiffres.',
                'phone.max' => 'Le numéro de télephone doit pas excéder 8 chiffres.',
                'phone.unique' => 'Cet numéro  de télephone est déjà utilisé.',
                'email.unique' => 'Cet email est déja utlisé.',
                'email.required' => 'L\'email est requis.'
            ];
        }
    }
