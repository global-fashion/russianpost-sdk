# SDK для [бизнес-сервисов](https://www.pochta.ru/support/business/api) Почты России

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appwilio/russianpost-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appwilio/russianpost-sdk/?branch=master)
[![Build Status](https://travis-ci.org/appwilio/russianpost-sdk.svg?branch=master)](https://travis-ci.org/appwilio/russianpost-sdk)
[![Total Downloads](https://poser.pugx.org/appwilio/russianpost-sdk/downloads)](https://packagist.org/packages/appwilio/russianpost-sdk)
[![Latest Stable Version](https://poser.pugx.org/appwilio/russianpost-sdk/version)](https://packagist.org/packages/appwilio/russianpost-sdk)
[![License](https://poser.pugx.org/appwilio/russianpost-sdk/license)](https://packagist.org/packages/appwilio/russianpost-sdk)

## Содержание
- [Трекинг](#трекинг)
  - [x] [Единичный доступ](#единичный-доступ)
    - [x] информация о наложенном платеже
  - [x] [Пакетный доступ](#пакетный-доступ)
- [Отправка](#отправка)
  - [x] [Расчёт стоимости пересылки](#расчёт-стоимости-пересылки)
  - [x] Получение баланса
  - [x] Нормализация и валидация данных
    - [x] [ФИО](#нормализация-ФИО)
    - [x] [Адреса](#нормализация-адресов)
    - [x] [Телефоны](#нормализация-телефонов)
    - [x] [Проверка благонадёжности получателя](#проверка-благонадёжности-получателя)
  - [x] [Документы](#документы)
    - [x] [Форма Ф7п для заказа](#форма-Ф7п-для-заказа)
    - [x] [Форма Ф112ЭК для заказа](#форма-Ф112ЭК-для-заказа)
    - [x] [Пакет документов для заказа (до формирования партии)](#пакет-документов-для-заказа-(до-формирования-партии))
    - [x] [Пакет документов для заказа (после формирования партии)](#пакет-документов-для-заказа-(после-формирования-партии))
    - [x] [Пакет документов для партии](#пакет-документов-для-партии)
    - [x] [Акт осмотра содержимого партии](#акт-осмотра-содержимого-партии)
    - [x] [Форма Ф103 для партии](#форма-Ф103-для-партии)
    - [x] Подготовка и отправка электронной формы Ф103 для партии
  - [ ] Настройки
    - [x] Точки сдачи
    - [ ] Настройки пользователя
  - [ ] Заказы
    - [ ] Создание
    - [ ] Поиск по идентификатору магазина
    - [ ] Поиск по идентификатору Почты России
    - [ ] Редактирование
    - [ ] Удаление
    - [ ] Возврат в «Новые»
  - [ ] Партии
    - [ ] Создание партии заказов
    - [ ] Изменение дня отправки партии в ОПС
    - [ ] Перенос заказов в партию
    - [ ] Добавление заказов в партию
    - [ ] Удаление заказов из партии
    - [ ] Запрос данных о заказах в партии
    - [ ] Поиск партии по наименованию
    - [ ] Поиск всех партий
    - [ ] Поиск заказов по ШПИ
    - [ ] Поиск заказа по идентификатору Почты России
  - [ ] Архив
    - [ ] Запрос данных о партиях
    - [ ] Перевод партии в архив
    - [ ] Возврат партии из архива
  - [ ] Поиск ОПС
    - [ ] По индексу
    - [ ] По адресу
    - [ ] По координатам
    - [ ] Поиск индексов в населённом пункте
    - [ ] Почтовые сервисы ОПС
    - [ ] Почтовые сервисы ОПС по идентификатору группы сервисов
  - [ ] Долгосрочное хранение
    - [ ] Запрос данных о заказах

> Работа с API возможна только при наличии договора с Почтой России (кроме единичного трекинга, где без договора лимит 100 запросов в сутки).

## Установка

> Минимальные требования — PHP 7.1+, ext-soap, ext-json.

```bash
composer require appwilio/russianpost-sdk
```

## Трекинг

[Документация](https://tracking.pochta.ru/specification)

### Единичный доступ
```php
$tracker = new SingleAccessClient($login, $password);

$response = $tracker->getTrackingEvents('29014562148754');

foreach ($response->getOperations() as $operation) {
    $parameters = $operation->getOperationParameters();
    
    echo $parameters->getOperationId();
    echo $parameters->getAttributeId();
    echo $parameters->getPerformedAt();
}
```

### Пакетный доступ
```php
$tracker = new PacketAccessClient($login, $password);

$ticket = $tracker->getTicket(['29014562148754', 'RA325487125CN']); // максимум 3 000 треков

// рекомендуется подождать 15 минут перед запросом информации

$response = $tracker->getTrackingEvents($ticket->getId());

foreach ($response->getEvents() as $event) {
    echo $event->getBarcode();
    
    foreach ($event->getOperations as $operation) {
        echo $operation->getOperationId();
        echo $operation->getAttributeId();
        echo $operation->getPerformedAt();
    }
}
```

## Отправка

[Документация](https://otpravka.pochta.ru/specification)

```php
$client = new DispatchingClient($login $password, $token);
```

### Расчёт стоимости пересылки
```php
$response = $client->services()->calculate(
    CalculationRequest::create('123456', 200)
        ->ofMailType(MailType::PARCEL_POSTAL)
        ->ofMailCategory(MailCategory::ORDINARY)
        ->ofEntriesType(MailEntryType::SALE_OF_GOODS)
        ->fragile()
        ->withSmsNotice();
);

echo $response->getTotal()->getCost();
```

### Нормализация и валидация данных

#### Нормализация ФИО
```php
$response = $client->services()->normalizeFio(
    NormalizeFioRequest::one('иванов иван иванович')
);

if ($response[0]->isUseful()) {
    echo $response[0]->getFirstName().''.$response[0]->getLastName(); // Иван Иванов
}
```

#### Нормализация адресов
```php
$response = $client->services()->normalizeAddress(
    NormalizeAddressRequest::one('Москва варшавское шоссе 37-45')
);
```

#### Нормализация телефонов
```php
$response = $client->services()->normalizePhone(NormalizePhoneRequest::one('89001234567'));
```

#### Проверка благонадёжности получателя
```php
$response = $client->services()->checkRecipient(
    CheckRecipientRequest::one('Москва, Варшавское шоссе, 37-45')
);

$response[0]->isFraud(); // ненадёжный
$response[0]->isReliable(); // надёжный
```

```php
$response = $client->services()->checkRecipient(
    CheckRecipientRequest::create()
        ->addRecipient('123456 Москва, Варшавское шоссе, 37-45')
        ->addRecipient('654321 Владивосток, пер. Староконный, 12-98');
);

foreach ($response as $recipient) {
    echo $recipient->getAddress.': '.$recipiend->isReliable();
}
```

### Документы
```php
$file = $client->documents()->orderF7Form('12345678');

echo $file->getClientFilename(); // f7p.pdf

// Сохранение
$file->moveTo("storage/printforms/12345678-{$file->getClientFilename()}");

// Перенаправление в браузер (Laravel)
return \response()->streamDownload(function () use ($file) {
    (string) $file->getStream();
}, $file->getClientName(), ['Content-Type' => $file->getClientMediaType()]);
```

#### Форма Ф7п для заказа
```php
$pdf = $client->documents()->orderF7Form(
    '12345678', new \DateTime('2019-01-01'), Documents::PRINT_TYPE_THERMO
);
```

#### Форма Ф112ЭК для заказа
```php
$pdf = $client->documents()->orderF112Form('12345678', new \DateTime('2019-01-01'));
```

#### Пакет документов для заказа (до формирования партии)
```php
$zip = $client->documents()->orderFormsBundleBacklog('12345678', new \DateTime('2019-01-01'));
```

#### Пакет документов для заказа (после формирования партии)
```php
$zip = $client->documents()->orderFormBundle(
    '12345678', new \DateTime('2019-01-01'), Documents::PRINT_TYPE_THERMO
);
```

#### Пакет документов для партии
```php
$zip = $client->documents()->batchFormBundle('87654321');
```

#### Акт осмотра содержимого партии
```php
$pdf = $client->documents()->batchCheckingForm('87654321');
```

#### Форма Ф103 для партии
```php
$pdf = $client->documents()->batchF103Form('87654321');
```

## Запуск тестов

```
$ vendor/bin/phpunit
```
## Авторы

- [greabock](https://github.com/greabock)
- [JhaoDa](https://github.com/jhaoda)

## Лиценция

Данный SDK распространяется под лицензией [MIT](http://opensource.org/licenses/MIT).
