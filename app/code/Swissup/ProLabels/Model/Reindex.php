<?php

namespace Swissup\ProLabels\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface as ObjectManager;

/**
 * A class to manage Magento modes
 *
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 * @SuppressWarnings("PMD.ExcessiveParameterList")
 */
class Reindex
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        ObjectManager $objectManager
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->objectManager = $objectManager;
    }

    /**
     * Reindex All Labels
     *
     * @return void
     */
    public function reindexAll()
    {
        $label = $this->objectManager->create('Swissup\ProLabels\Model\Label');
        $label->reindexAll();
    }
}
