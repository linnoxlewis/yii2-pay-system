<?php

namespace linnoxlewis\paySystem;

use yii\base\Exception;

/**
 * Exception Платежки
 *
 * Class PaySystemException
 * @package common\components
 */
class PaySystemException extends Exception
{
    /**
     * Генерация ошибки
     *
     * @return array
     */
    public function generateMessage() : array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage()
        ];
    }
}
