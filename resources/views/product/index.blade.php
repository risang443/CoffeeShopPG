
@extends('layout.pages')
@section('content')
<div class="top_panel_title top_panel_style_3 title_present breadcrumbs_present scheme_original">
    <div class="top_panel_title_inner top_panel_inner_style_3 title_present_inner breadcrumbs_present_inner breadcrumbs_1">
        <div class="content_wrap">
            <h1 class="page_title">Shop</h1>
            <div class="breadcrumbs">
                <a class="breadcrumbs_item home" href="index.html">Home</a>
                <span class="breadcrumbs_delimiter"></span>
                <span class="breadcrumbs_item current">Shop</span>
            </div>
        </div>
    </div>
</div>
<div class="page_content_wrap page_paddings_yes">
    <div class="content_wrap">
        <div class="content">
            <div class="list_products shop_mode_thumbs">
                <nav class="woocommerce-breadcrumb">
                    <a href="#">Home</a>&nbsp;&#47;&nbsp;Shop
                </nav>
                <ul class="products">
                    @foreach ($product as $p)
                    <li class="product has-post-thumbnail column-1_2 last">
                        <a href="#" class="woocommerce-LoopProduct-link"></a>
                        <div class="post_item_wrap">
                            <div class="post_featured">
                                <div class="post_thumb">
                                    <a class="hover_icon hover_icon_link" href="#">
                                        <img src="{{ $p->image_path ? asset('storage/' . $p->image_path) : asset('assets/images/punyang-Photoroom1.png') }}" class="attachment-shop_catalog size-shop_catalog" alt="panini" title="panini" />
                                    </a>
                                </div>
                            </div>
                            <div class="post_content">
                                <h3>
                                    <a href="#"> {{$p->name}} </a>
                                </h3>
                                <span class="price">
                                    <span class="woocommerce-Price-amount amount">
                                        <span class="woocommerce-Price-currencySymbol">Rp. </span>{{$p->price}}
                                    </span>
                                </span>
                                <a href="#"></a>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <input type="hidden" name="name" value="{{ $p->name }}">
                                    <input type="hidden" name="price" value="{{ $p->price }}">
                                    <input type="hidden" name="quantity" value="1" min="1">
                                    <button class="button product_type_simple add_to_cart_button " type="submit">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                <nav id="pagination" class="pagination_wrap pagination_pages">
                    @if ($product->hasPages())
                        <!-- Tombol 'Previous' -->
                        @if ($product->onFirstPage())
                            <span class="pager_prev disabled"></span>
                        @else
                            <a href="{{ $product->previousPageUrl() }}" class="pager_prev"></a>
                        @endif

                        <!-- Tampilkan halaman pertama jika tidak berada di halaman pertama -->
                        @if ($product->currentPage() > 2)
                            <a href="{{ $product->url(1) }}" class="pager_number">1</a>
                            @if ($product->currentPage() > 3)
                                <span class="pager_dots">...</span>
                            @endif
                        @endif

                        <!-- Tampilkan 2 halaman di sekitar halaman aktif -->
                        @foreach (range(max(1, $product->currentPage() - 1), min($product->lastPage(), $product->currentPage() + 1)) as $page)
                            @if ($page == $product->currentPage())
                                <span class="pager_current active">{{ $page }}</span>
                            @else
                                <a href="{{ $product->url($page) }}" class="pager_number">{{ $page }}</a>
                            @endif
                        @endforeach

                        <!-- Tampilkan halaman terakhir jika tidak berada di halaman terakhir -->
                        @if ($product->currentPage() < $product->lastPage() - 1)
                            @if ($product->currentPage() < $product->lastPage() - 2)
                                <span class="pager_dots">...</span>
                            @endif
                            <a href="{{ $product->url($product->lastPage()) }}" class="pager_number">{{ $product->lastPage() }}</a>
                        @endif

                        <!-- Tombol 'Next' -->
                        @if ($product->hasMorePages())
                            <a href="{{ $product->nextPageUrl() }}" class="pager_next"></a>
                        @else
                            <span class="pager_next disabled"></span>
                        @endif
                    @endif
                </nav>
            </div>
        </div>
        @role('admin')
        <div class="sidebar widget_area scheme_original" role="complementary">
            <div class="sidebar_inner widget_area_inner">
                <aside class="widget widget_text">
                    <h5 class="widget_title">Add New Product</h5>
                    <div class="textwidget">
                        <form class="mc4wp-form mc4wp-form-422" method="post" data-id="422" data-name="">
                            <div class="mc4wp-form-fields">
                                <p>
                                    <label>Enter New Product</label>
                                </p>
                                <p>
                                    <a href="{{route('product.create')}}" class="button product_type_simple add_to_cart_button ">Add Product</a>

                                </p>
                            </div>
                            <div class="mc4wp-response"></div>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
        @endrole
    </div>
</div>
@endsection
