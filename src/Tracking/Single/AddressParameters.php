<?php

/**
 * This file is part of RussianPost SDK package.
 *
 * © Appwilio (http://appwilio.com), greabock (https://github.com/greabock), JhaoDa (https://github.com/jhaoda)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Appwilio\RussianPostSDK\Tracking\Single;

class AddressParameters
{
    /** @var Address */
    public $DestinationAddress;

    /** @var Address */
    public $OperationAddress;

    /** @var Country */
    public $MailDirect;

    /** @var Country */
    public $CountryOper;

    /** @var Country */
    public $CountryFrom;
}
