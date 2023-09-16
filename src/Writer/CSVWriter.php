<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Cacing69\Cquery\Writer;

use Cacing69\Cquery\AbstractCqueryWriter;

class CSVWriter extends AbstractCqueryWriter
{
    public function save($path = null)
    {
        $output = fopen($path, 'w');

        fputcsv($output, array_keys($this->data[0]));

        foreach ($this->data as $data) {
            fputcsv($output, $data);
        }

        fclose($output);

        return $path;
    }
}
