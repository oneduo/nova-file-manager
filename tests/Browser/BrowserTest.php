<?php

declare(strict_types=1);

namespace BBSLab\NovaFileManager\Tests\Browser;

use BBSLab\NovaFileManager\Tests\DuskTestCase;
use Laravel\Dusk\Browser;

/**
 * @group browser
 */
class BrowserTest extends DuskTestCase
{
    /** @test */
    public function can_show_filemanager()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit(route('nova-file-manager.tool', [], false))
                ->assertSee(__('NovaFileManager.title'));
        });
    }
}
