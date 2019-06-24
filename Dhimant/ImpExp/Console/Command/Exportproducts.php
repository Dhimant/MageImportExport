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
use Magento\ImportExport\Model\ExportFactory;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;

class Exportproducts extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";
    private $exportInfoFactory;


    public function __construct(
      State $state,
      ExportFactory $exportFactory,
      CsvFactory $csvSourceFactory,
      WriteFactory $writeFactory,
      Filesystem $filesystem
    ) {
        $this->state = $state;
        $this->exportFactory = $exportFactory;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->writeFactory = $writeFactory;
        $this->filesystem = $filesystem;
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
 
        $export_path = $input->getArgument('export_path');
        $export_file = pathinfo($export_path);

        $output->writeln("export_path".$export_path);
        $output->writeln("export_file".print_r($export_file,1));

        $export = $this->exportFactory->create();
        $export->setEntity('catalog_product');
        $export->setFileFormat('csv');
        $export->setExportFilter('');

        $csv_data = $export->export();
        $export_to_file = $export_file['dirname'].'/'.$export_file['basename'];
        $handle = fopen($export_to_file, 'w') or die('Cannot open file:  '.$export_to_file);
        //$data = 'This is the data';
        fwrite($handle, $csv_data);
        $output->writeln("<info>Finished exporting products from $export_path</info>");

    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("dhimant_impexp:exportproducts");
        $this->setDescription("Export All Products to CSV");
        $this->addArgument('export_path', InputArgument::REQUIRED, 'The path to export file (ie. ../../path/to/export.csv)');
        parent::configure();
    }
}
