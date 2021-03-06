@extends('layouts.admin',['module'=>'satuan','judul'=>'Satuan Produk'])
@section('title')
    <title>Satuan Produk</title>
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <p class="ml-4"> Satuan produk adalah satuan yang diberikan kepada produk yang digunakan untuk memberikan satuan ketika produk dijual kepada konsumen</p><br>
            </div>
            <div class="row">
                <div class="col-md-4">
                    @component('components.card')
                        @slot('title')
                        Tambah
                        @endslot
                        
                        @if (session('error'))
                            <x-alert>
                                <x-slot name='type'>
                                    danger
                                </x-slot>
                                {!! session('error') !!}
                            </x-alert>
                        @endif
​
                        <form role="form" action="{{ route('satuan.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Satuan</label>
                                <input type="text" 
                                name="name"
                                class="form-control {{ $errors->has('name') ? 'is-invalid':'' }}" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea name="description" id="description" cols="5" rows="5" class="form-control {{ $errors->has('description') ? 'is-invalid':'' }}"></textarea>
                            </div>
                        @slot('footer')
                            <div class="card-footer">
                                <button class="btn btn-primary elevation-3" id="buttonsimpan">Simpan</button>
                            </div>
                        </form>
                        @endslot
                    @endcomponent
                </div>
                <div class="col-md-8">
                    @component('components.card')
                        @slot('title')
                        List Satuan Produk Jual
                        @endslot
                        
                        @if (session('success'))
                            <x-alert>
                                <x-slot name='type'>
                                    success
                                </x-slot>
                                {!! session('success') !!}
                            </x-alert>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="text-bold">
                                        <td>#</td>
                                        <td>Satuan</td>
                                        <td>Deskripsi</td>
                                        <td>Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @forelse ($units as $row)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>{{ $row->description }}</td>
                                        <td>
                                            <form action="{{ route('satuan.destroy', $row->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE">
                                                <a href="{{ route('satuan.edit', $row->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @slot('footer')
​
                        @endslot
                    @endcomponent
                </div>
            </div>
        </div>
    </section>
</div>
@endsection