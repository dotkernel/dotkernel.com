<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\ArticleBodyCleaner;
use LightTest\Unit\UnitTest;

class ArticleBodyCleanerTest extends UnitTest
{
    public function testCleanStripsTheLeadingTitleAndTlDrSection(): void
    {
        $body = "# A Title\n\n## TL;DR\n\nA short summary.\n\n## First Section\n\nThe real content.";

        $this->assertSame("## First Section\n\nThe real content.", ArticleBodyCleaner::clean($body));
    }

    public function testCleanOnlyStripsTheTitleWhenThereIsNoTlDrSection(): void
    {
        $body = "# A Title\n\n## First Section\n\nThe real content.";

        $this->assertSame("## First Section\n\nThe real content.", ArticleBodyCleaner::clean($body));
    }

    public function testCleanStripsTlDrWithNoLeadingTitleWhenFollowedBySection(): void
    {
        $body = "## TL;DR\n\nA short summary.\n\n## First Section\n\nThe real content.";

        $this->assertSame("## First Section\n\nThe real content.", ArticleBodyCleaner::clean($body));
    }

    public function testCleanStripsTlDrWithNoLeadingTitleAndNoFollowingHeading(): void
    {
        $body = "## TL;DR\n\nA short summary.\n\nJust plain continuing content with no further heading.";

        $this->assertSame(
            'Just plain continuing content with no further heading.',
            ArticleBodyCleaner::clean($body)
        );
    }

    public function testCleanReturnsEmptyStringWhenTlDrIsTheEntireBody(): void
    {
        $body = "## TL;DR\n\nOnly a summary, nothing else.";

        $this->assertSame('', ArticleBodyCleaner::clean($body));
    }
}
