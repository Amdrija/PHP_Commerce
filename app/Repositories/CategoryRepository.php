<?php

namespace Andrijaj\DemoProject\Repositories;

use Andrijaj\DemoProject\Framework\Exceptions\CategoryCodeExistsException;
use Andrijaj\DemoProject\Framework\Exceptions\DeletedCategoryHasProductException;
use Andrijaj\DemoProject\Framework\Exceptions\ParentCategoryNotFoundException;
use Andrijaj\DemoProject\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;
use PDOException;

class CategoryRepository
{
    /**
     * Returns the CategoryEntity with the specified Id.
     * @param int $id
     * @return Category|null
     */
    public function getCategoryById(int $id): ?Category
    {
        /** @var Category|null $category */
        $category = Category::query()->find($id);

        return $category;
    }

    /**
     * Returns the category with the specified category code.
     * @param string $code
     * @return Category|null
     */
    public function getCategoryByCode(string $code): ?Category
    {
        /** @var Category|null $category */
        $category = Category::query()->where('Code', $code)->first();

        return $category;
    }

    /**
     * Returns all the categories in the database.
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return Category::query()->get();
    }

    /**
     * Returns the number of categories in the database.
     * @return int
     */
    public function getCategoryCount(): int
    {
        return Category::query()->count();
    }

    /**
     * Returns an array of all subcategories and their subcategories of the specified root category.
     * @param Category $rootCategory
     * @return array
     */
    public function getSubcategoriesOfCategory(Category $rootCategory): array
    {
        $subcategories = [];
        foreach ($rootCategory->subcategories as $subcategory) {
            $subcategories[] = $subcategory;
            $subcategories = array_merge($subcategories, $this->getSubcategoriesOfCategory($subcategory));
        }

        return $subcategories;
    }

    /**
     * Creates a category based on the given associative array.
     * @param array $categoryData
     * @throws CategoryCodeExistsException
     * @throws ParentCategoryNotFoundException
     */
    public function createCategory(array $categoryData)
    {
        try {
            $category = new Category();
            $category->Title = $categoryData['title'];
            $category->Code = $categoryData['code'];
            $category->ParentId = $categoryData['parentId'] ? $categoryData['parentId'] : null;
            $category->Description = $categoryData['description'];
            $category->save();
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new CategoryCodeExistsException();
            } else if ($e->errorInfo[1] === 1452) {
                throw new ParentCategoryNotFoundException();
            }
            throw $e;
        }
    }

    /**
     * Updates a category with the specified categoryData associative array.
     * @param array $categoryData
     * @throws CategoryCodeExistsException
     * @throws ParentCategoryNotFoundException
     * @throws PDOException
     */
    public function updateCategory(array $categoryData)
    {
        try {
            Category::query()->where('Id', $categoryData['id'])->update(
                [
                    'Title' => $categoryData['title'],
                    'Code' => $categoryData['code'],
                    'ParentId' => $categoryData['parentId'] ? $categoryData['parentId'] : null,
                    'Description' => $categoryData['description'],
                ]
            );
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                throw new CategoryCodeExistsException();
            } else if ($e->errorInfo[1] === 1452) {
                throw new ParentCategoryNotFoundException();
            }
            throw $e;
        }
    }

    /**
     * Deletes a category with the specified Id and all of it's subcategories.
     * If the delete fails, Eloquent will throw PDOException. If that exception
     * has code 1451, that means that a foreign key constraint failed on Category
     * and Product table. In other words, the deleted category or one of it's
     * subcategories has products and therefore we can't delete it.
     * I don't know how to get rid of the warnings, because they are using Laravel magic.
     * @param int $id
     * @throws DeletedCategoryHasProductException
     * @throws Exception
     */
    public function deleteCategoryById(int $id)
    {
        DB::beginTransaction();
        try {
            Category::query()->where('Id', $id)->delete();
            DB::commit();
        } catch (PDOException $e) {
            DB::rollBack();
            if ($e->errorInfo[1] === 1451) {
                throw new DeletedCategoryHasProductException();
            }
            throw $e;
        }
    }
}