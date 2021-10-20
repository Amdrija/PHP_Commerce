<?php

namespace Andrijaj\DemoProject\Controllers\FrontControllers;

use Andrijaj\DemoProject\Framework\Responses\HTMLResponse;
use Andrijaj\DemoProject\Controllers\Controller;

abstract class FrontController extends Controller
{
    /**
     * There is similar duplicate data in the ProductRepository, so if we add/remove/edit a sort order,
     * it has to be added/edited/removed in the ProductRepository as well.
     * Keeping this data in product repository and getting it from there is not an option, since we tightly-couple
     * ProductRepository with FrontController.
     * Making a separate class just for this feature and using it in FrontController and ProductRepository seems
     * like a waste of code, because the class would have 1 method and a couple of public constants.
     * The sorting order most likely isn't going to change and that's why I've stick with separate implementations
     * in ProductRepository and FrontController.
     */
    private const SORT_BYS = ['PRICE_ASC', 'PRICE_DESC', 'TITLE_ASC', 'TITLE_DESC', 'BRAND_ASC', 'BRAND_DESC'];

    protected function buildHtmlResponse(string $view, array $variables = []): HTMLResponse
    {
        return $this->buildHtmlResponseLayout('layout', $view, $variables);
    }

    /**
     * Returns the sortBy queryParameter if it is set, otherwise returns $defaultSortBy.
     * @param array $queryParameters
     * @param string $defaultSortBy
     * @return string
     */
    protected function getSortBy(array $queryParameters, string $defaultSortBy): string
    {
        $sortBy = empty($queryParameters['sortBy']) ? $defaultSortBy :
            $queryParameters['sortBy'];

        return in_array($sortBy, self::SORT_BYS) || $sortBy === $defaultSortBy ? $sortBy : $defaultSortBy;
    }
}