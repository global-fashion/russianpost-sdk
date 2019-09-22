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

namespace Appwilio\RussianPostSDK\Tests\Tracking\Single;

use Appwilio\RussianPostSDK\Tests\TestCase;
use Appwilio\RussianPostSDK\Tracking\Single\TrackingOperationAddress;

class TrackingOperationAddressTest extends TestCase
{
    public function test_getters(): void
    {
        /** @var TrackingOperationAddress $address */
        $address = $this->buildClass(TrackingOperationAddress::class, [
            'Index'       => '644008',
            'Description' => 'Томск',
        ]);

        $this->assertEquals('644008', $address->getIndex());
        $this->assertEquals('Томск', $address->getDescription());
    }
}
