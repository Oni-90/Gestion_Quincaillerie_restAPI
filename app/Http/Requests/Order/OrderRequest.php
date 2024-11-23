<?php

    namespace App\Http\Requests\Order;

    use Illuminate\Foundation\Http\FormRequest;

    class OrderRequest extends FormRequest
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
            return [
                //order data
                'supplier_id' => 'required|integer|exists:suppliers,id',
                'total_amount' => 'required|decimal:0,4',
                'order_date' => 'required|date|before_or_equal:now',
                'payment_status' => 'required|string',

                //product_order data
                'products' => 'array|required',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity_ordered' => 'required|decimal:0,4|min:1',
            ];
        }

        /**
         * ---------------------------------------------
         * message to show when one rule is violate
         * ---------------------------------------------
         * @return [type]
         */
        public function messages()
        {
            return[
                'order_date.before_or_equal' => "La date doit être antérieure ou égale à aujourd'hui.",
                'total_amount.required' => "Le montant total est requis.",
                'order_date.required' => 'La date de la commande est requise.',
                'payment_status.required' => 'Le statut du paiment est requis',
                'products.required' => "Vous devez choisir au moins un produit afin de créer la commande.",
                'products.*.product_id.exists' => "Vous avez selectioné produit qui est introuvable.",
                'products.*.quantity_ordered.required' => "La quantité commandée est requise.",

            ];
        }
    }
