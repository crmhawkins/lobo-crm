<?php

namespace App\Http\Livewire\Productoscategories;

use Livewire\Component;
use App\Models\ProductosCategories;


class IndexComponent extends Component

{

    public $productosCategories;

    public function mount()
    {
        $this->productosCategories = ProductosCategories::all();
    }
    public function render()
    {
        return view('livewire.productos_categories.index-component', [
            'productosCategories' => $this->productosCategories,
        ]);
        // return view('livewire.productos-component');
    }
}
