<?php

return [
    'auth' => [
        'errors' => [
            'register' => 'Não foi possível completar o registro',
            'login' => 'Não foi possível realizar a autenticacão, tente novamente em alguns instantes.',
        ],
    ],
    'user' => [
        'errors' => [
            'wallet' => [
                'blocked' => 'Ação não autorizada pois sua carteira está bloqueada.',
            ],
        ],
    ],
    'deposit' => [
        'errors' => [
            'fail' => 'Falha ao efetuar o depósito, tente novamente em alguns instantes.',
            'numeric' => 'O valor informado para deposito deve ser númerico.',
            'min' => 'O valor mínimo para depositos é de R$ :amount',
            'max' => 'O valor limite para depositos é de R$ :amount',
        ],
    ],
    'transfer' => [
        'errors' => [
            'fail' => 'Falha ao efetuar a transferência, tente novamente em alguns instantes.',
            'numeric' => 'O valor informado para deposito deve ser númerico.',
            'min' => 'O valor mínimo para depositos é de R$ :amount',
            'max' => 'O valor limite para depositos é de R$ :amount',
            'not_enough_balance' => 'Carteira não possui saldo suficiente para efetuar a transferência.',
        ],
    ],

];
