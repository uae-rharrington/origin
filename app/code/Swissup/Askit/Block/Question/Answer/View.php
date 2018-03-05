<?php

namespace Swissup\Askit\Block\Question\Answer;

use Swissup\Askit\Block\Question\AbstractBlock;
use Swissup\Askit\Api\Data\MessageInterface;

class View extends AbstractBlock
{
    protected $answer;

    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    public function getAnswer()
    {
        return $this->answer;
    }
}
