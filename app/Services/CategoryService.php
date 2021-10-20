<?php

namespace Andrijaj\DemoProject\Services;

use Andrijaj\DemoProject\Entities\CategoryEntity;
use Andrijaj\DemoProject\Framework\Exceptions\CategoryCircularReferenceException;
use Andrijaj\DemoProject\Framework\Exceptions\CategoryCodeExistsException;
use Andrijaj\DemoProject\Framework\Exceptions\CategoryNotFoundException;
use Andrijaj\DemoProject\Framework\Exceptions\DeletedCategoryHasProductException;
use Andrijaj\DemoProject\Framework\Exceptions\ParentCategoryNotFoundException;
use Andrijaj\DemoProject\Models\Category;
use Andrijaj\DemoProject\Repositories\CategoryRepository;
use Andrijaj\DemoProject\Repositories\RepositoryNotFoundException;
use Andrijaj\DemoProject\Repositories\RepositoryRegistry;
use Illuminate\Support\Collection;
use PDOException;

class CategoryService
{
    private const ROOT_CATEGORY_TITLE = 'Root';
    private const ROOT_CATEGORY_ID = 0;
    private CategoryRepository $categoryRepository;

    /**
     * CategoryService constructor.
     * @throws RepositoryNotFoundException
     */
    public function __construct()
    {
        $this->categoryRepository = RepositoryRegistry::get('categoryRepository');
    }

    /**
     * Returns all the categories in the form of a tree.
     * @return array
     */
    public function getCategoryTree()
    {
        $categories = $this->categoryRepository->getAllCategories()->keyBy('Id');
        $rootCategories = $categories->filter(fn($category) => $category->ParentId === null);
        $categoryTree = [];
        foreach ($rootCategories as $rootCategory) {
            $categories->forget($rootCategory->Id);
            $categoryTree[] = new CategoryEntity(
                $rootCategory->Id,
                $rootCategory->Title,
                $rootCategory->Code,
                $rootCategory->Description,
                self::ROOT_CATEGORY_TITLE,
                self::ROOT_CATEGORY_ID,
                $this->getSubcategoryTree($categories, $rootCategory)
            );
        }

        return $categoryTree;
    }

    /**
     * Returns all the categories in the form of a list.
     * @return array
     */
    public function getCategoryList()
    {
        $categories = $this->categoryRepository->getAllCategories()->keyBy('Id');
        $categoryList = [];
        foreach ($categories as $category) {
            /** @var Category $category */
            $categoryList[] = new CategoryEntity(
                $category->Id,
                $category->Title,
                $category->Code,
                $category->Description,
                $category->ParentId ? $category->parentCategory->Title : self::ROOT_CATEGORY_TITLE,
                $category->ParentId ? $category->parentCategory->Id : self::ROOT_CATEGORY_ID,
                []
            );
        }

        return $categoryList;
    }

    /**
     * Returns the Category model with the specified code.
     * @param string $code
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function getCategoryModelByCode(string $code): Category
    {
        $category = $this->categoryRepository->getCategoryByCode($code);
        if ($category === null) {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    /**
     * Returns an array containing the Ids of the specified root category
     * and ids of it's subcategories.
     * @param Category $rootCategory
     * @return array
     */
    public function getArrayOfCategoryAndSubcategoryIds(Category $rootCategory): array
    {
        $subcategories = $this->categoryRepository->getSubcategoriesOfCategory($rootCategory);
        $categories = array_merge([$rootCategory], $subcategories);

        return array_map(fn($category) => $category->Id, $categories);
    }

    /**
     * Creates a category based on the given associative array.
     * @param array $categoryData
     * @throws CategoryCodeExistsException
     * @throws ParentCategoryNotFoundException
     * @throws PDOException
     */
    public function createCategory(array $categoryData)
    {
        $this->categoryRepository->createCategory($categoryData);
    }

    /**
     * Updates a category with the specified categoryData associative array.
     * @param array $categoryData
     * @throws CategoryCircularReferenceException
     * @throws CategoryCodeExistsException
     * @throws ParentCategoryNotFoundException
     */
    public function updateCategory(array $categoryData)
    {
        if ($this->isCircularDependent($categoryData['id'], $categoryData['parentId'])) {
            throw new CategoryCircularReferenceException();
        }

        $this->categoryRepository->updateCategory($categoryData);
    }

    /**
     * Deletes a category with the specified Id.
     * @param int $id
     * @throws DeletedCategoryHasProductException
     * @throws PDOException
     */
    public function deleteCategory(int $id)
    {
        $this->categoryRepository->deleteCategoryById($id);
    }

    /**
     * Checks if the provided parentId isn't a child of the category that should be updated.
     * @param int $id
     * @param int $parentId
     * @return bool
     */
    private function isCircularDependent(int $id, int $parentId): bool
    {
        if ($id === $parentId) {
            return true;
        }
        $parentCategory = $this->categoryRepository->getCategoryById($parentId);
        while (!is_null($parentCategory) && !is_null($parentCategory->ParentId)) {
            if ($parentCategory->ParentId === $id) {
                return true;
            }
            $parentCategory = $this->categoryRepository->getCategoryById($parentCategory->ParentId);
        }

        return false;
    }

    /**
     * @param Collection $categories
     * @param Category $root
     * @return array
     */
    private function getSubcategoryTree(Collection $categories, Category $root): array
    {
        $categorySubTree = [];
        foreach ($categories as $category) {
            if ($category->ParentId === $root->Id) {
                $categories->forget($category->Id);
                $categorySubTree[] = new CategoryEntity(
                    $category->Id,
                    $category->Title,
                    $category->Code,
                    $category->Description,
                    $root->Title,
                    $category->ParentId,
                    $this->getSubcategoryTree($categories, $category)
                );
            }
        }

        return $categorySubTree;
    }
}