<?php declare(strict_types=1);

namespace SepaQr;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected SepaQrData $data;

    public function setUp(): void
    {
        $this->data = new SepaQrData();
    }
}
