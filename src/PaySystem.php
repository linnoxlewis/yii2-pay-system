<?php

namespace linnoxlewis\paySystem;

use yii\base\Component;

/**
 * Class PaySystemComponent
 *
 * Компонент платежки. Пример:
 * \Yii::$app->paySystem
 *  ->setPayId(234)
 *  ->setDescription('test order')
 *  ->setAmount(500)
 *  ->generateForm())
 *
 * @property string $type
 * @property object $system
 * @property int|string $payId
 * @property string $description
 *
 * @package common\components
 */
class PaySystem extends Component
{
    use ParamsPaySystemTrait;

    /**
     * Тип платежной системы
     *
     * @var string
     */
    protected $type;

    /**
     * объект платежки
     *
     * @var object
     */
    protected $system;

    /**
     * Сумма
     *
     * @var int
     */
    protected $amount;

    /**
     * Id заказа
     *
     * @var int|string
     */
    protected $payId;

    /**
     * Описание покупки
     *
     * @var string
     */
    protected $description = 'Payment';


    /**
     * PaySystemComponent init.
     *
     */
    public function init()
    {
        $this->system = \Yii::createObject([
                'class' => $this->getType(),
                'merchantId' => $this->getMerchantId(),
                'key' => $this->getKey()
            ]
        );

        parent::init();
    }

    /**
     * Гетер описания заказа
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Сетер описания заказа
     *
     * @param string $value значение
     * @return $this
     */
    public function setDescription($value)
    {
        $this->description = $value;

        return $this;
    }

    /**
     * Гетер id заказа
     *
     * @return string
     */
    public function getPayId()
    {
        return $this->payId;
    }

    /**
     * Сетер id заказа
     *
     * @param string $value значение
     * @return $this
     */
    public function setPayId($value)
    {
        $this->payId = $value;

        return $this;
    }

    /**
     * Гетер итоговой суммы
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Сетер итоговой суммы
     *
     * @param string $value значение
     * @return $this
     */
    public function setAmount($value)
    {
        $this->amount = $value;

        return $this;
    }

    /**
     * Гетер типа
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Сетер типа
     *
     * @param string $value значение
     * @return $this
     */
    public function setType($value)
    {
        $this->type = $value;

        return $this;
    }

    /**
     * Отправка запроса
     *
     * @return mixed
     */
    public function send()
    {
        try {
            $this->prepareParams();
            $result = $this->system->form(true);
        } catch (PaySystemException $e) {
            $result = $e->generateMessage();

        }
        return $result;
    }

    /**
     * Генерация формы
     *
     * @return mixed
     */
    public function generateForm()
    {
        try {
            $this->prepareParams();
            $result = $this->system->form();
        } catch (PaySystemException $e) {
            $result = $e->generateMessage();

        }
        return $result;
    }


    /**
     * Установка параметров
     *
     * @return void
     */
    protected function prepareParams()
    {
        $this->system
            ->setSuccsessUrl($this->getSuccsessUrl())
            ->setFailUrl($this->getStatusFail());
        $this->system->generateParams(
            $this->getPayId(),
            (int)$this->getAmount(),
            $this->getDescription()
        );
    }

    /**
     * Статус успеха
     *
     * @return string
     */
    public function getStatusOk()
    {
        return $this->system->getStatusOk();
    }

    /**
     * Статус провала
     *
     * @return string
     */
    public function getStatusFail()
    {
        return $this->system->getStatusFail();
    }

    /**
     * Проверка ответа от платежки
     *
     * @param array $response ответ от платежки
     *
     * @return string
     */
    public function validate(array $response)
    {
        return $this->system->validateResponse($response);
    }
}
