<?php

namespace App\Http\Controllers\Ez\Editor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CollectionController extends Controller
{
    public function __construct() {
        $this->collectionPaths = $this->getCollections('Collections');
        $this->editableClasses = [];
    }

    /**
     * Instantiates a new instance of a collection model
     * 
     * TODO: Add error checks
     */
    private function loadCollectionClass($className) {
        $fullClassName = "\App\Collections\\".$className;
        return new $fullClassName();
    }

    /**
     * Gets a list of all collection models
     */
    public function getCollections($namespace) {
        $path = app_path()."\\".$namespace;
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = $path . '\\' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, $this->getCollections($filename));
            }else{
                $out[] = substr($filename,0,-4);
            }
        }
        return $out;
    }

    /**
     * List all collection models
     */
    public function index() {
        foreach($this->collectionPaths as $collection) {
            $className = last(explode("\\", $collection));

            $this->editableClasses[] = [
                'class' => $className,
                // TODO: Collection namespace should be dynamic
                'namespace' => 'App\\Collections',
                'label' => Str::plural(ucfirst($className)),
            ];
        }

        return view('ez.collections.index', [
            'collections' => $this->editableClasses,
        ]);
    }

    /**
     * Displays a single collection model (and its rows)
     */
    public function show($collection) {
        $collectionModel = $this->loadCollectionClass($collection);

        return view('ez.collections.model.index', [
            'collection' => $collection,
            'columns' => Schema::getColumnListing($collectionModel->getTable()),
            'rows' => $collectionModel->all(),
        ]);
    }

    /**
     * Displays the edit form for a collection's row
     */
    public function editRow($collection, $id) {
        $collectionModel = $this->loadCollectionClass($collection);

        // Find the row
        $row = $collectionModel->find($id);

        dd($row);
    }
}