<?php

namespace App\Http\Controllers;
use App\Category;
use App\Product;
use App\unit;
use File;
use Image;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('updated_at', 'DESC')->paginate(10);
        return view('admin.product', compact('products'));
    }
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        $units = unit::orderBy('name', 'ASC')->get();
        return view('admin.create_produk', compact('categories','units'));
    }
    public function store(Request $request)
    {
        //validasi data
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        try {
            //default $photo = null
            $photo = null;
            //jika terdapat file (Foto / Gambar) yang dikirim
            if ($request->hasFile('photo')) {
                //maka menjalankan method saveFile()
                $photo = $this->saveFile($request->name, $request->file('photo'));
            }

            //Simpan data ke dalam table products
            $product = Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'photo' => $photo
            ]);

            //jika berhasil direct ke produk.index
            return redirect(route('produk.index'))
                ->with(['success' => '<strong>' . $product->name . '</strong> Ditambahkan']);
        } catch (\Exception $e) {
            //jika gagal, kembali ke halaman sebelumnya kemudian tampilkan error
            return redirect()->back()
                ->with(['error' => $e->getMessage()]);
        }
    }
    private function saveFile($name, $photo)
    {
        //set nama file adalah gabungan antara nama produk dan time(). Ekstensi gambar tetap dipertahankan
        $images = Str::slug($name) . time() . '.' . $photo->getClientOriginalExtension();
        //set path untuk menyimpan gambar
        $path = public_path('uploads/product');

        //cek jika uploads/product bukan direktori / folder
        if (!File::isDirectory($path)) {
            //maka folder tersebut dibuat
            File::makeDirectory($path, 0777, true, true);
        } 
        //simpan gambar yang diuplaod ke folrder uploads/produk
        Image::make($photo)->save($path . '/' . $images);
        //mengembalikan nama file yang ditampung divariable $images
        return $images;
    }
    public function destroy($id)
    {
        //query select berdasarkan id
        $products = Product::findOrFail($id);
        //mengecek, jika field photo tidak null / kosong
        if (!empty($products->photo)) {
            //file akan dihapus dari folder uploads/produk
            File::delete(public_path('uploads/product/' . $products->photo));
        }
        //hapus data dari table
        $products->delete();
        return redirect()->back()->with(['success' => '<strong>' . $products->name . '</strong> Telah Dihapus!']);
    }
    public function edit($id)
    {
        //query select berdasarkan id
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name', 'ASC')->get();
        $units = unit::orderBy('name', 'ASC')->get();
        return view('admin.edit_produk', compact('product', 'categories','units'));
    }
    public function update(Request $request, $id)
    {
        //validasi
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        try {
            //query select berdasarkan id
            $product = Product::findOrFail($id);
            $photo = $product->photo;

            //cek jika ada file yang dikirim dari form
            if ($request->hasFile('photo')) {
                //cek, jika photo tidak kosong maka file yang ada di folder uploads/product akan dihapus
                !empty($photo) ? File::delete(public_path('uploads/product/' . $photo)):null;
                //uploading file dengan menggunakan method saveFile() yg telah dibuat sebelumnya
                $photo = $this->saveFile($request->name, $request->file('photo'));
            }

            //perbaharui data di database
            $product->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'photo' => $photo
            ]);

            return redirect(route('produk.index'))
                ->with(['success' => '<strong>' . $product->name . '</strong> Diperbaharui']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(['error' => $e->getMessage()]);
        }
    }

    public function update_stock(Request $request)
    {
        //dd($request);
        $product=Product::findOrFail($request->id);
        $product->stock=$request->stock + $product->stock;
        $product->save();
        //dd($product);
        return redirect()->back()->with(['success' => 'Restock: ' . $product->name . ' Telah berhasil ditambah '.$request->stock]);
    }
}
