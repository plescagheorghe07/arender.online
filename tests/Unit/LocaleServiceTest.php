<?php

namespace Tests\Unit;

use App\Locales\Services\LocaleService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class LocaleServiceTest extends TestCase
{
    public function test_it_knows_which_locales_are_saved(): void
    {
        $locales = new LocaleService;

        $this->assertTrue($locales->supports('ro'));
        $this->assertTrue($locales->supports('ru'));
        $this->assertFalse($locales->supports('en'));
        $this->assertSame('Română', $locales->available()['ro']);
    }

    public function test_it_rejects_an_unknown_locale(): void
    {
        $this->expectException(NotFoundHttpException::class);

        (new LocaleService)->remember('xx');
    }
}
