<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\FrontMatter;
use LightTest\Unit\UnitTest;

class FrontMatterTest extends UnitTest
{
    public function testParseSplitsTheYamlFrontMatterFromTheBody(): void
    {
        $raw = "---\ntitle: \"A Title\"\nlanguage: \"en\"\n---\n\n# A Title\n\nSome body content.";

        $result = FrontMatter::parse($raw);

        $this->assertSame(['title' => 'A Title', 'language' => 'en'], $result['meta']);
        $this->assertSame("# A Title\n\nSome body content.", $result['body']);
    }

    public function testParseReturnsTheWholeContentAsBodyWhenThereIsNoFrontMatter(): void
    {
        $result = FrontMatter::parse("# A Title\n\nSome body content.");

        $this->assertSame([], $result['meta']);
        $this->assertSame("# A Title\n\nSome body content.", $result['body']);
    }
}
