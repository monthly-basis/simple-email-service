<?php
namespace MonthlyBasis\SimpleEmailServiceTest;

use MonthlyBasis\SimpleEmailService\Module;
use MonthlyBasis\LaminasTest\ModuleTestCase;

class ModuleTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        $this->module = new Module();
    }
}
