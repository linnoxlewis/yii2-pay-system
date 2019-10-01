<?php

namespace linnoxlewis\paySystem\payModels;

/**
 * Оплата WalletOne
 *
 * Class WalletOne
 *
 * @package common\components\paySystem
 */
interface PaySystemInterface
{
    /**
     * Генерация формы
     *
     * @return string
     */
    public function form(): string;

    /**
     * Статус успеха
     *
     * @param string $description описание
     *
     * @return string
     */
    public function getStatusOk($description = null): string ;

    /**
     * Статус провала
     *
     * @param string $description описание
     *
     * @return string
     */
    public function getStatusFail($description = null): string ;


    /**
     * Проверка ответа от платежки
     *
     * @param array $response ответ от платежки
     *
     * @return string
     */
    public function validateResponse(array $response) : string ;
}
