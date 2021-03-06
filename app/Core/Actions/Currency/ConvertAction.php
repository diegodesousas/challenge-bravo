<?php

namespace App\Core\Actions\Currency;

use App\Core\Actions\BaseAction;
use App\Core\Rules\Currency\CurrencySupported;
use App\Models\Currency;

class ConvertAction extends BaseAction
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'from' => [
                'required',
                'exists:currencies,code',
            ],
            'to' => [
                'required',
                'exists:currencies,code'
            ],
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        $fail('Amount must be greater than zero.');
                    }
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'to.exists' => 'Currency not supported.',
            'from.exists' => 'Currency not supported.'
        ];
    }

    /**
     * @param string $code
     * @return Currency
     */
    private function findCurrency(string $code): Currency
    {
        return Currency::findByCode($code);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $amount = $this->get('amount');
        $codeFrom = $this->get('from');
        $codeTo = $this->get('to');

        $to = $this->findCurrency($codeTo);
        $from = $this->findCurrency($codeFrom);

        $convertedAmount = $from->convert($to, $amount);

        return [
            'converted_amount' => round($convertedAmount, 5)
        ];
    }
}