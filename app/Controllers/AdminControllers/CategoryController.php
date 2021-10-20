<?php

namespace Andrijaj\DemoProject\Controllers\AdminControllers;

use Andrijaj\DemoProject\Framework\Exceptions\CategoryCircularReferenceException;
use Andrijaj\DemoProject\Framework\Exceptions\CategoryCodeExistsException;
use Andrijaj\DemoProject\Framework\Exceptions\DeletedCategoryHasProductException;
use Andrijaj\DemoProject\Framework\Responses\HTMLResponse;
use Andrijaj\DemoProject\Framework\Responses\JSONResponse;
use Andrijaj\DemoProject\Framework\Exceptions\ParentCategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Request;
use Andrijaj\DemoProject\Services\CategoryService;
use Andrijaj\DemoProject\Services\ServiceNotFoundException;
use Andrijaj\DemoProject\Services\ServiceRegistry;
use \Exception;

class CategoryController extends AdminController
{
    private const MAX_CATEGORY_DESCRIPTION_LENGTH = 512;
    private const MAX_CATEGORY_CODE_LENGTH = 32;
    private const MAX_CATEGORY_TITLE_LENGTH = 64;
    private CategoryService $categoryService;

    /**
     * CategoryController constructor.
     * @param Request $request
     * @throws ServiceNotFoundException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->categoryService = ServiceRegistry::get('categoryService');
    }

    /**
     * Returns the data needed to render the admin category view.
     * @return HTMLResponse
     */
    public function indexAction(): HTMLResponse
    {
        $categoryViewVariables = [
            'title' => 'Admin | Categories',
            'categories' => json_encode($this->categoryService->getCategoryTree()),
        ];

        return $this->buildHtmlResponse('adminCategories', $categoryViewVariables);
    }

    /**
     * Tries to create a category and returns JSON needed to display updated category tree.
     * @return JSONResponse
     */
    public function newCategoryAction(): JSONResponse
    {
        $category = $this->request->getBody();
        if (!$this->isCategoryInputFilled($category)) {
            return JSONResponse::buildErrorJSONResponse('Category input field(s) you submitted is (are) empty.', 409);
        }

        if ($this->isCategoryInputTooLong($category)) {
            return JSONResponse::buildErrorJSONResponse(
                'Category input fields(s) you submitted is (are) too long.',
                409
            );
        }

        if ($this->stringContainsURLReservedCharactersOrSpaces($category['code'])) {
            return JSONResponse::buildErrorJSONResponse(
                'Category code cannot contain URL reserved characters or whitespace.',
                409
            );
        }

        try {
            $this->categoryService->createCategory($category);
        } catch (CategoryCodeExistsException $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 409);
        } catch (ParentCategoryNotFoundException $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 404);
        } catch (Exception $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 400);
        }

        return new JSONResponse($this->categoryService->getCategoryTree());
    }

    /**
     * Tries to update a category and returns JSON needed to display updated category tree.
     * @return JSONResponse
     */
    public function updateCategoryAction(): JSONResponse
    {
        $category = $this->request->getBody();

        if (!$this->isCategoryInputFilled($category)) {
            return JSONResponse::buildErrorJSONResponse('Category input field(s) you submitted is (are) empty.', 409);
        }

        if ($this->isCategoryInputTooLong($category)) {
            return JSONResponse::buildErrorJSONResponse(
                'Category input fields(s) you submitted is (are) too long.',
                409
            );
        }

        if ($this->stringContainsURLReservedCharactersOrSpaces($category['code'])) {
            return JSONResponse::buildErrorJSONResponse(
                'Category code cannot contain URL reserved characters or whitespace.',
                409
            );
        }

        try {
            $this->categoryService->updateCategory($this->request->getBody());
        } catch (CategoryCircularReferenceException $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 409);
        } catch (CategoryCodeExistsException $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 409);
        } catch (Exception $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 400);
        }

        return new JSONResponse($this->categoryService->getCategoryTree());
    }

    /**
     * Tries to delete the category and returns JSON needed to display updated category tree.
     * @return JSONResponse
     */
    public function deleteCategoryAction(): JSONResponse
    {
        $id = $this->request->getBodyParameter('id');

        try {
            $this->categoryService->deleteCategory($id);
        } catch (DeletedCategoryHasProductException $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 409);
        } catch (Exception $e) {
            return JSONResponse::buildErrorJSONResponse($e->getMessage(), 400);
        }

        return new JSONResponse($this->categoryService->getCategoryTree());
    }

    /**
     * Checks if the input is correct.
     * @param array $category
     * @return bool
     */
    private function isCategoryInputFilled(array $category): bool
    {
        return $category['title'] !== '' &&
               $category['code'] !== '' &&
               $category['description'] !== '';
    }

    /**
     * Returns true if the category input is longer than what the database can hold.
     * @param array $category
     * @return bool
     */
    private function isCategoryInputTooLong(array $category): bool
    {
        return strlen($category['title']) > self::MAX_CATEGORY_TITLE_LENGTH ||
               strlen($category['code']) > self::MAX_CATEGORY_CODE_LENGTH ||
               strlen($category['description']) > self::MAX_CATEGORY_DESCRIPTION_LENGTH;
    }
}