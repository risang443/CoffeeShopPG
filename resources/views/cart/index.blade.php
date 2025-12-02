@extends('layout.pages')

@section('content')
@push('css')
<link rel='stylesheet' href="{{ asset('assets/js/vendor/comp/comp.min.css') }}" type='text/css' media='all' />

@endpush
@push('js')
<script type='text/javascript' src='{{ asset('assets/js/vendor/comp/comp_front.min.js') }}'></script>
<script type='text/javascript' src='{{ asset('assets/js/vendor/ui/widget.min.js') }}'></script>
<script type='text/javascript' src='{{ asset('assets/js/vendor/ui/tabs.min.js') }}'></script>
<script type='text/javascript' src='{{ asset('assets/js/vendor/ui/effect.min.js') }}'></script>
<script type='text/javascript' src='{{ asset('assets/js/vendor/ui/effect-fade.min.js') }}'></script>
<script>
        function removeItem(productId) {
        fetch('{{ route('cart.remove') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId }),
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to remove item');
            }
        });
    }
</script>
@endpush

<div class="top_panel_title top_panel_style_3 title_present breadcrumbs_present scheme_original">
    <div class="top_panel_title_inner top_panel_inner_style_3 title_present_inner breadcrumbs_present_inner breadcrumbs_7">
        <div class="content_wrap">
            <h1 class="page_title">Checkout</h1>
            <div class="breadcrumbs">
                <a class="breadcrumbs_item home" href="index.html">Home</a>
                <span class="breadcrumbs_delimiter"></span>
                <span class="breadcrumbs_item current">Checkout</span>
            </div>
        </div>
    </div>
</div>
<div class="page_content_wrap page_paddings_yes">
    <div class="content_wrap">
        <div class="content">
            <article class="post_item post_item_single post_featured_default post_format_standard page hentry">
                <section class="post_content">
                    <div class="vc_row wpb_row vc_row-fluid">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <h2 class="sc_title sc_title_regular sc_align_center margin_bottom_small">Order Detail</h2>
                                    <div class="sc_section aligncenter">
                                        <div class="sc_section_inner">
                                            <div class="wpb_text_column wpb_content_element ">
                                                <div class="wpb_wrapper">
                                                    <p>
                                                       ini adalah list order anda
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="sc_tabs_730" class="sc_tabs sc_tabs_style_2 margin_top_large" data-active="0">
                                        <ul class="sc_tabs_titles">
                                            <li class="sc_tabs_title">
                                                <a href="#sc_tab_131-2" class="theme_button" id="sc_tab_131-2_tab">Order</a>
                                            </li>
                                        </ul>
                                        <div id="sc_tab_131-2" class="sc_tabs_content">
                                            <div id="sc_menuitems_347_wrap" class="sc_menuitems_wrap">
                                                {{-- >>> START MODIFIKASI: Form Checkout Guest --}}
                                                <form action="{{ route('order.store') }}" method="POST">
                                                    @csrf
                                                    <div style="margin-bottom: 20px;">
                                                        <label for="username">Nama Pelanggan / Username:</label>
                                                        {{-- Ambil username jika login, jika tidak, biarkan kosong --}}
                                                        <input type="text" name="username" value="{{ auth()->user()->username ?? '' }}" required>
                                                        <small class="text-muted">Nama ini akan digunakan untuk penamaan order Anda.</small>
                                                    </div>
                                                
                                                    <div id="sc_menuitems_347" class="sc_menuitems sc_menuitems_style_menuitems-1 sc_slider_nopagination sc_slider_nocontrols margin_top_medium" data-interval="7492" data-slides-per-view="2">
                                                        <div class="sc_columns columns_wrap">
                                                            @foreach ($cart as $item)
                                                            <div class="column-1_2 column_padding_bottom">
                                                                <div id="sc_menuitems_347_1" class="sc_menuitems_item sc_menuitems_item_1" data-url="menu-item-1.html">
                                                                    <div class="sc_menuitem_image">
                                                                        <a class="show_popup_menuitem" href="#">
                                                                            <img width="90" height="90" alt="" src="{{ asset('assets/images/2000x2000.png') }}">
                                                                        </a>
                                                                    </div>
                                                                    <div class="sc_menuitem_price">x {{ $item['quantity'] }}</div>
                                                                    <div class="sc_menuitem_title">
                                                                        <a class="show_popup_menuitem" href="#">{{ $item['name'] }}</a>
                                                                    </div>
                                                                    <div class="sc_menuitem_description">
                                                                        Total Price: Rp. {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                                        <div>
                                                                            <input type="hidden" name="products[{{ $item['id'] }}][id]" value="{{ $item['id'] }}">
                                                                            <input type="hidden" name="products[{{ $item['id'] }}][quantity]" value="{{ $item['quantity'] }}" min="1" required>
                                                                        </div>
                                                                        <button type="button" onclick="removeItem({{ $item['id'] }})">Remove</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div style="margin-top: 20px;">
                                                        <strong>Total Order Price:</strong>
                                                        Rp. {{ number_format(collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']), 0, ',', '.') }}
                                                    </div>

                                                    <button type="submit" class="sc_button sc_button_square sc_button_style_filled sc_button_size_medium margin_top_medium">
                                                        Pesan & Lanjutkan Pembayaran
                                                    </button>
                                                </form>
                                                {{-- <<< END MODIFIKASI --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="vc_row wpb_row vc_row-fluid">
                        <div class="wpb_column vc_column_container vc_col-sm-12">
                            <div class="vc_column-inner ">
                                <div class="wpb_wrapper">
                                    <div class="sc_section aligncenter">
                                        <div class="sc_section_inner">
                                            <div class="sc_line sc_line_position_center_center sc_line_style_solid margin_top_large margin_bottom_large"></div>

                                            <a href="{{ url('/') }}" class="sc_button sc_button_square sc_button_style_border_1 sc_button_size_medium margin_top_medium margin_bottom_large">Continue Shopping</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </article>
            <section class="related_wrap related_wrap_empty"></section>
        </div>
    </div>
</div>

@endsection