<?php
/**
 * Copyright (c) 2019  
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Dhimant\ImpExp\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\App\State;


class Importproducts extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    public function __construct(
      State $state,
      ImportFactory $importFactory,
      CsvFactory $csvSourceFactory,
      ReadFactory $readFactory
    ) {
        $this->state = $state;
        $this->importFactory = $importFactory;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->readFactory = $readFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Intentionally left empty.
        }
 
        $import_path = $input->getArgument('import_path');
        $import_file = pathinfo($import_path);
 
        $import = $this->importFactory->create();
        $import->setData(
            array(
                'entity' => 'catalog_product',
                'behavior' => 'append',
                'validation_strategy' => 'validation-stop-on-errors',
            )
        );
 
        $read_file = $this->readFactory->create($import_file['dirname']);
        $csvSource = $this->csvSourceFactory->create(
            array(
                'file' => $import_file['basename'],
                'directory' => $read_file,
            )
        );
 
        $validate = $import->validateSource($csvSource);
        if (!$validate) {
          $output->writeln('<error>Unable to validate the CSV.</error>');
        }
 
        $result = $import->importSource();
        if ($result) {
          $import->invalidateIndex();
        }
 
        $output->writeln("<info>Finished importing products from $import_path</info>");

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("dhimant_impexp:importproducts");
        $this->setDescription("Import Products using default CSV");
        $this->addArgument('import_path', InputArgument::REQUIRED, 'The path of the import file (ie. ../../path/to/import.csv)');

        parent::configure();
    }
}
