<?php

declare(strict_types=1);

namespace LightTest\Unit\App\Service;

use Light\App\Service\FaqExtractor;
use LightTest\Unit\UnitTest;

class FaqExtractorTest extends UnitTest
{
    public function testExtractPullsTheQuestionAnswerPairsOutOfTheFaqSection(): void
    {
        $body = "Some intro text.\n\n## FAQ\n\n"
            . "**Q: What is it?**\nA: It's an example.\n\n"
            . "**Q: Why does it matter?**\nA: Because tests need coverage.";

        $result = FaqExtractor::extract($body);

        $this->assertSame('Some intro text.', $result['body']);
        $this->assertSame(
            [
                [
                    'question'     => 'What is it?',
                    'answer'       => "It's an example.",
                    'questionText' => 'What is it?',
                    'answerText'   => "It's an example.",
                ],
                [
                    'question'     => 'Why does it matter?',
                    'answer'       => 'Because tests need coverage.',
                    'questionText' => 'Why does it matter?',
                    'answerText'   => 'Because tests need coverage.',
                ],
            ],
            $result['faq']
        );
    }

    public function testExtractKeepsMarkdownForRenderingAndStripsBackticksForPlainText(): void
    {
        $body = "## FAQ\n\n**Q: Is `dot-cache` needed?**\nA: Use `dot-cache` and ``dot-cli``.";

        $result = FaqExtractor::extract($body);

        $this->assertSame('Is `dot-cache` needed?', $result['faq'][0]['question']);
        $this->assertSame('Use `dot-cache` and ``dot-cli``.', $result['faq'][0]['answer']);
        $this->assertSame('Is dot-cache needed?', $result['faq'][0]['questionText']);
        $this->assertSame('Use dot-cache and dot-cli.', $result['faq'][0]['answerText']);
    }

    public function testExtractReturnsAnEmptyFaqWhenThereIsNoFaqSection(): void
    {
        $body = "Some intro text.\n\nMore content.";

        $result = FaqExtractor::extract($body);

        $this->assertSame([], $result['faq']);
        $this->assertSame($body, $result['body']);
    }
}
