<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Campaigns\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Content display class
 *
 * @api
 * @since 100.0.2
 */
class Content extends \Magento\Ui\Component\Listing\Columns\Column
{
    public const NAME = 'content';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['content'] = substr($item['content'], 0, 50) . "...";
            }
        }
        return $dataSource;
    }
}
