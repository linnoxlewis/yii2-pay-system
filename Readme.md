Yii2 .

Установка: composer require "linnoxlewis/yii2-pay-system"

Конфигурация:
```
use linnoxlewis\paySystem\PaySystem;
return [
 'components' => [ 
        // ...,
    'paySystem' => [ 
        'class' => PaySytem::class, 
        'key' => '1234', 
        'merchantId' =>'test', 
        'successUrl' => 'test.com/successs'
        'failUrl' => 'test.com/fail'
    ]
  ],
]
```
Пример использования :
```
\Yii::$app->paySystem
   ->setPayId(234)
   ->setDescription('test order')
   ->setAmount(500)
   ->generateForm())
```