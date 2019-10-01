<?php

namespace linnoxlewis\paySystem;

/**
 * Trait ParamsPaySystemTrait
 *
 * Трейт параметров
 *
 * @property string $successUrl
 * @property string $failUrl
 * @property int $currencyId
 * @property int|string $merchantId
 * @property string $key
 *
 * @package common\components\paySystem
 */
trait ParamsPaySystemTrait
{
    /**
     * Переход на url успешной оплаты
     *
     * @var string
     */
    protected $succsessUrl;

    /**
     * Переход на url неуспешной оплаты
     *
     * @var string
     */
    protected $failUrl;

    /**
     * Код валюты
     *
     * @var int
     */
    protected $currencyId = 643;

    /**
     * Ключ магазина
     *
     * @var int|string
     */
    protected $merchantId;

    /**
     * Секретный ключ
     *
     * @var int|string
     */
    protected $key;

    /**
     * Получение значения
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Установка значения
     *
     * @param string $value
     * @return $this
     */
    public function setMerchantId($value)
    {
        $this->merchantId = $value;

        return $this;
    }

    /**
     * Получение значения
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Установка значения
     *
     * @param string $value
     * @return $this
     */
    public function setKey($value)
    {
        $this->key = $value;

        return $this;
    }

    /**
     * Получение значения
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->getCurrencyId();
    }

    /**
     * Установка значения
     *
     * @param int $value
     * @return $this
     */
    public function setCurrencyId($value)
    {
        $this->currencyId = $value;

        return $this;
    }

    /**
     * Получение значения
     *
     * @return string
     */
    public function getSuccsessUrl()
    {
        return $this->succsessUrl;
    }

    /**
     * Установка значения
     *
     * @param string $value
     * @return $this
     */
    public function setSuccsessUrl($value)
    {
        $this->succsessUrl = $value;

        return $this;
    }

    /**
     * Получение значения
     *
     * @return string
     */
    public function getFailUrl()
    {
        return $this->failUrl;
    }

    /**
     * Установка значения
     *
     * @param string $value
     * @return $this
     */
    public function setFailUrl($value)
    {
        $this->failUrl = $value;

        return $this;
    }
}
