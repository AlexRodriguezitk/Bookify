<?php

namespace App\controllers;

use App\traits\Log;
use App\traits\ApiResponse;
use App\models\Category;
use Flight;

class CategoryController
{
    use ApiResponse;
    use Log;
    
    //index function
    public function index() {
        $categories = Category::getAll();
        $this->success($categories, 'Categories list', 200);
    }

    //show function
    public function show($id) {
        $category = Category::get($id);
        if ($category) {
            $this->success([$category], 'Category found', 200);
        } else {
            $this->failed(null, 'Category not found', 404);
        }
    }

    //store function
    public function store() {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            $this->failed(null, "No data provided", 400);
            return;
        }

        $requiredFields = ['name', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->failed(null, "Field '$field' is required", 400);
                return;
            }
        }

        $category = new Category(null, $data['name'], $data['description']);
        $category = Category::create($category);
        $this->saveLog(null, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: ' . $category->name);
        $this->success([$category], 'Category created', 201);
    }

    //update function
    public function update($id) {
        $data = Flight::request()->data->getData();
        $category = Category::get($id);
        if ($category) {
            $category->name = $data['name'] ?? $category->name;
            $category->description = $data['description'] ?? $category->description;
            $category = Category::update($category);
            $this->saveLog(null, 'CATEGORY_UPDATED', 'CATEGORY WAS UPDATED SUCCESSFULLY: ' . $category->name);
            $this->success([$category], 'Category updated', 200);
        } else {
            $this->failed(null, 'Category not found', 404);
        }
    }

    //delete function
    public function destroy($id) {
        $category = Category::get($id);
        if ($category) {
            Category::delete($category);
            $this->saveLog(null, 'CATEGORY_DELETED', 'CATEGORY WAS DELETED SUCCESSFULLY: ' . $category->name);
            $this->success(null, 'Category deleted', 200);
        } else {
            $this->failed(null, 'Category not found', 404);
        }
    }
}