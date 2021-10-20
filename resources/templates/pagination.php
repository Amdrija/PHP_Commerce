<?php
/** @var int $productsPerPage
 * @var int $currentPage
 * @var int $productPageCount
 */

/**
 * Parses the REQUEST_URI to get the query parameters array.
 * @return array
 */
function parseUrlForQueryParameters(): array
{
    $queryString = parse_url($_SERVER['REQUEST_URI'])['query'];
    parse_str($queryString, $query);

    return $query;
}

function constructQuery(int $page, int $productsPerPage)
{
    $query = parseUrlForQueryParameters();
    $query['page'] = $page;
    $query['productsPerPage'] = $productsPerPage;

    return "?" . http_build_query($query);
}

function constructQueryForFirstPage(int $productsPerPage)
{
    return constructQuery(1, $productsPerPage);
}

function constructQueryForPreviousPage(int $currentPage, int $productsPerPage)
{
    $previousPage = $currentPage > 1 ? $currentPage - 1 : 1;

    return constructQuery($previousPage, $productsPerPage);
}

function constructQueryForNextPage(int $currentPage, int $productPageCount, int $productsPerPage)
{
    $nextPage = $currentPage < $productPageCount ? $currentPage + 1 : $productPageCount;

    return constructQuery($nextPage, $productsPerPage);
}

function constructQueryForLastPage(int $productPageCount, int $productsPerPage)
{
    return constructQuery($productPageCount, $productsPerPage);
}

?>
<div class="pagination-container">
    <a href="<?= constructQueryForFirstPage($productsPerPage) ?>">
        <button class="button inline" id="first-page"><<<</button>
    </a>
    <a href="<?= constructQueryForPreviousPage($currentPage, $productsPerPage) ?>">
        <button class="button inline" id="previous-page"><</button>
    </a>
    <div class="button inline" id="page-number"><span id="current-page"><?= $currentPage ?></span>
        / <?= $productPageCount ?></div>
    <a href="<?= constructQueryForNextPage(
        $currentPage,
        $productPageCount,
        $productsPerPage
    ) ?>">

        <button class="button inline" id="next-page">></button>
    </a>
    <a href="<?= constructQueryForLastPage($productPageCount, $productsPerPage) ?>">
        <button class="button inline" id="last-page">>>></button>
    </a>
</div>