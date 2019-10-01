<?php

namespace linnoxlewis\paySystem\payModels;

use linnoxlewis\paySystem\ParamsPaySystemTrait;
use yii\base\Model;
use linnoxlewis\paySystem\PaySystemException;

/**
 * Оплата WalletOne
 *
 * Class WalletOne
 *
 * @package common\components\paySystem
 */
class WalletOne extends Model implements PaySystemInterface
{
    use ParamsPaySystemTrait;

    /**
     * Ответ когда все норм
     *
     * @var string
     */
    const VALIDATE_OK = 'WMI_RESULT=OK';

    /**
     * Ответ зафейлинной валидации
     *
     * @var string
     */
    const VALIDATE_FAIL = 'RETRY';

    /**
     * Статус подтвержденного заказа
     *
     * @var string
     */
    const STATUS_OK = 'ACCEPTED';

    /**
     * Статус зафейленного заказа
     *
     * @var string
     */
    const STATUS_FAIL = 'RETRY';

    /**
     * Форма оплаты
     *
     * @var array
     */
    protected $payForm = [];

    /**
     * Форма результата
     *
     * @var array
     */
    protected $callbackForm = [];

    /**
     * url оплаты
     *
     * @var string
     */
    protected $payUrl = 'https://wl.walletone.com/checkout/checkout/Index';

    /**
     * Сеттер для формы
     *
     * @param array $value форма
     *
     * @return $this
     */
    public function setPayForm($value)
    {
        $this->payForm = $value;

        return $this;
    }

    /**
     * Гетер для формы
     *
     * @return array
     */
    public function getPayForm()
    {
        return $this->payForm;
    }

    /**
     * Правила
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                ['merchantId', 'key'], 'required'
            ],
            [
                ['merchantId', 'key'], 'safe'
            ],
            [
                ['succsessUrl', 'failUrl'], 'string'
            ]
        ];
    }

    /**
     * Генерация параметров для отправки
     *
     * @param string $payId id закака
     * @param int $amount сумма
     * @param string $description описание
     *
     * @throws PaySystemException
     * @return void
     */
    public function generateParams($payId, $amount, $description): void
    {
        if (!$this->validate() || !is_int($amount)) {
            throw new PaySystemException('Невалидные данные');
        }
        $this->SetParam('WMI_MERCHANT_ID', $this->getMerchantId());
        $this->SetParam('WMI_PAYMENT_AMOUNT', $amount);
        $this->SetParam('WMI_CURRENCY_ID', $this->currencyId);
        $this->SetParam('WMI_PAYMENT_NO', $payId);
        $this->SetParam('WMI_DESCRIPTION', $description);
        $this->SetParam('WMI_SUCCESS_URL', $this->succsessUrl);
        $this->SetParam('WMI_FAIL_URL', $this->failUrl);
        $this->sortParams();
        $signature = $this->GenerateSignature();
        $this->SetParam('WMI_SIGNATURE', $signature);
    }

    /**
     * Установка параметра
     *
     * @param int|string $name ключ
     * @param int|string $value значение
     *
     * @return void
     */
    public function setParam($name, $value): void
    {
        $this->payForm[$name] = $value;
    }

    /**
     * Сортировка параметров
     *
     * @return void
     */
    protected function sortParams(): void
    {
        $fields = [];
        foreach ($this->payForm as $name => $val) {
            if (is_array($val)) {
                usort($val, "strcasecmp");
            }
            $fields[$name] = $val;
        }
        uksort($fields, "strcasecmp");
        $this->payForm = $fields;
    }

    /**
     * Генерация сигнатуры
     *
     * @return string
     */
    protected function generateSignature(): string
    {
        $fieldValues = "";

        foreach ($this->payForm as $value) {
            if (is_array($value))
                foreach ($value as $v) {
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= $v;
                }
            else {
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }
        $signature = base64_encode(pack("H*", md5($fieldValues . $this->key)));
        return $signature;
    }

    /**
     * Генерация формы
     *
     * @param bool $send отправка сразу или через форму
     *
     * @return string
     */
    public function form($send = false): string
    {
        $form = "<form action="  . "'$this->payUrl'" . "accept-charset='UTF-8' method='POST'>";
        foreach ($this->payForm as $key => $val) {
            $form .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }

        if ($send) {
            $form .= '<script>document.getElementsByTagName("form")[0].submit();</script></form>';
        } else {
            $form .= "<input type='submit'>";
        }
        return $form;
    }

    /**
     * Статус успеха
     *
     * @param string $description описание
     *
     * @return string
     */
    public function getStatusOk($description = null): string
    {
        $result = static::VALIDATE_OK;
        if (!empty($description)) {
            $result .= '&WMI_DESCRIPTION=' . $description;
        }
        return $result;
    }

    /**
     * Статус провала
     *
     * @param string $description описание
     *
     * @return string
     */
    public function getStatusFail($description = null): string
    {
        $result = static::VALIDATE_FAIL;
        if (!empty($description)) {
            $result .= '&WMI_DESCRIPTION=' . $description;
        }
        return $result;
    }

    /**
     * Проверка ответа от платежки
     *
     * @param array $response ответ от платежки
     *
     * @return string
     */
    public function validateResponse(array $response) : string
    {
        if (!isset($response['WMI_SIGNATURE'])) {
            $result = $this->getStatusFail('Отсутствует параметр WMI_SIGNATURE');
            return $result;
        }
        if (!isset($response['WMI_PAYMENT_NO'])) {
            $result = $this->getStatusFail('Отсутствует параметр WMI_PAYMENT_NO');
            return $result;
        }
        if (!isset($response['WMI_ORDER_STATE'])) {
            $result = $this->getStatusFail('Отсутствует параметр WMI_ORDER_STATE');
            return $result;
        }
        foreach ($response as $name => $value) {
            if ($name !== 'WMI_SIGNATURE') $this->SetParam($name, $value);
        }
        $this->sortParams();
        $signature = $this->GenerateSignature();
        if ($signature == $response['WMI_SIGNATURE']) {
            if (strtoupper($response['WMI_ORDER_STATE']) == strtoupper(static::STATUS_OK)) {
                $result = $this->getStatusOk();
            } else {
                $result = $this->getStatusFail('Неверное состояние ' . $response['WMI_ORDER_STATE']);
            }
        } else {
            $result = $this->getStatusFail('Неверная подпись ' . $response['WMI_SIGNATURE']);
        }
        return $result;
    }
}
