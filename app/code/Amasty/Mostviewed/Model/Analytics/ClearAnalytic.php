<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Mostviewed
 */


namespace Amasty\Mostviewed\Model\Analytics;

use Amasty\Mostviewed\Model\ResourceModel\Analytics\Click as ClickResource;
use Amasty\Mostviewed\Model\ResourceModel\Analytics\View as ViewResource;
use Amasty\Mostviewed\Model\ResourceModel\Analytics\Analytic as AnalyticResource;

class ClearAnalytic
{
    /**
     * @var ClickResource
     */
    private $clickResource;

    /**
     * @var ViewResource
     */
    private $viewResource;

    /**
     * @var AnalyticResource
     */
    private $analyticResource;

    public function __construct(
        ClickResource $clickResource,
        ViewResource $viewResource,
        AnalyticResource $analyticResource
    ) {
        $this->clickResource = $clickResource;
        $this->viewResource = $viewResource;
        $this->analyticResource = $analyticResource;
    }

    public function execute()
    {
        $this->clickResource->getConnection()->truncateTable($this->clickResource->getMainTable());
        $this->viewResource->getConnection()->truncateTable($this->viewResource->getMainTable());
        $this->analyticResource->getConnection()->update($this->analyticResource->getMainTable(), ['version_id' => 0]);
    }
}
