<?php

namespace Xpressengine\Plugins\XeroCommerce\Services;

use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeroCommerce\Handlers\ProductOptionHandler;
use Xpressengine\Plugins\XeroCommerce\Models\Product;
use Xpressengine\Plugins\XeroCommerce\Models\ProductOption;

class ProductOptionSettingService
{
    /** @var ProductOptionHandler $productOptionHandler */
    protected $productOptionHandler;

    public function __construct()
    {
        $this->productOptionHandler = app('xero_commerce.productOptionHandler');
    }

    /**
     * @param Request $request
     * @param $productId
     */
    public function saveOptions(Request $request, $productId)
    {
        // 새로운 옵션들을 입력
        $optionsData = $request->get('options');
        $savedIds = [];
        if($optionsData) {
            foreach ($optionsData as $optionData) {
                $optionData['product_id'] = $productId;

                if(empty($optionId = array_get($optionData, 'id'))) {
                    $savedOption = $this->productOptionHandler->store($optionData);
                } else {
                    $option = ProductOption::find($optionId);
                    $savedOption = $this->productOptionHandler->update($option, $optionData);
                }
            }
        }
        $this->removeObsoleteOptions($savedIds);
    }

    /**
     * 버려진 ID들 삭제
     * @param array $savedIds
     */
    public function removeObsoleteOptions(array $savedIds)
    {
        $obsoletes = ProductOption::whereNotIn('id', $savedIds)->get();

        foreach ($obsoletes as $item) {
            $this->productOptionHandler->destroy($item);
        }
    }

}
