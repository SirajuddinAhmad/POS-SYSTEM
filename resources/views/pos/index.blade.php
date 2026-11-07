@extends('layouts.app')

@section('title', 'Terminal Kasir POS')
@section('page_title', 'Terminal Kasir POS')
@section('page_subtitle', 'Proses transaksi cepat dengan QRIS Dinamis dan Tunai')

@section('content')
<div class="container-fluid px-0">
    <!-- POS Layout: Products Grid (Left) + Cart & Checkout (Right) -->
    <div class="row">
        <!-- Products Area (Left Column) -->
        <div class="col-lg-7 col-xl-8 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white h-100 p-4">
                <!-- Search & Category Pills -->
                <div class="row mb-3">
                    <div class="col-md-7 mb-2 mb-md-0">
                        <div class="input-group">
                            <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary btn-sm rounded-xl font-semibold shadow-sm">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i> Riwayat Transaksi
                            </a>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="posCategoryFilter" class="form-control custom-select bg-slate-100 border-0 rounded-xl text-sm font-semibold">
                            <option value="all">Semua Kategori (Semua)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Products Grid Container -->
                <div class="overflow-y-auto pr-1" style="max-height: 600px;" id="productGridContainer">
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 row-cols-xl-4 g-3" id="productGrid">
                        <!-- Loaded dynamically via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart & Checkout Area (Right Column) -->
        <div class="col-lg-5 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm rounded-2xl bg-white h-100 d-flex flex-column">
                <!-- Cart Header -->
                <div class="card-header bg-white border-bottom border-slate-100 p-3 d-flex justify-content-between align-items-center">
                    <div>
                    
                        <span class="text-xs text-slate-400" id="cartItemCount">0 item dipilih</span>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm text-xs rounded-lg px-2 py-1" onclick="clearCart()">
                        <i class="fa-solid fa-trash-can mr-1"></i> Kosongkan
                    </button>
                </div>

                <!-- Customer Name -->
                <div class="px-3 pt-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-slate-50 border-slate-200 text-xs text-slate-500">
                                <i class="fa-solid fa-user"></i>
                            </span>
                        </div>
                        <input type="text" id="customerName" class="form-control text-xs border-slate-200" placeholder="Nama Pelanggan (Default: Pelanggan Umum)" value="Pelanggan Umum">
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="card-body p-3 flex-grow-1 overflow-y-auto" style="max-height: 340px;" id="cartList">
                    <div class="text-center py-5 text-slate-400" id="emptyCartMessage">
                        <i class="fa-solid fa-cart-arrow-down text-4xl mb-2 text-slate-300"></i>
                        <p class="text-xs mb-0">Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</p>
                    </div>
                </div>

                <!-- Cart Summary & Checkout Button -->
                <div class="card-footer bg-slate-50 border-top border-slate-200 p-2">
                    <div class="d-flex justify-content-between text-xs text-slate-500 mb-1">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay" class="font-semibold text-slate-700">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-xs text-slate-500 mb-2">
                        <span>Diskon (Rp)</span>
                        <input type="number" id="discountInput" class="form-control form-control-sm text-right w-24 text-xs font-semibold" value="0" min="0" oninput="calculateTotals()">
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mb-3 pt-2 border-top border-slate-200">
                        <span class="font-bold text-slate-800 text-sm">TOTAL AKHIR</span>
                        <span id="grandTotalDisplay" class="text-2xl font-black text-blue-600">Rp 0</span>
                    </div>

                    <!-- Checkout Button -->
                    <button type="button" class="btn btn-primary btn-block btn-lg rounded-xl font-bold py-3 shadow-md hover:shadow-lg transition-all" id="btnOpenPayment" onclick="openPaymentModal()" disabled>
                        <i class="fa-solid fa-credit-card mr-2"></i> BAYAR SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Kasir Fitur 1: MODAL PEMBAYARAN (QRIS DINAMIS & TUNAI) -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-slate-900 text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title font-bold text-white text-base mb-0">
                        <i class="fa-solid fa-shield-check text-blue-400 mr-2"></i> Pilih Metode Pembayaran
                    </h5>
                    <span class="text-xs text-slate-400">Total Tagihan: <strong class="text-emerald-400 text-sm" id="modalTotalTagihan">Rp 0</strong></span>
                </div>
                <button type="button" class="close text-white opacity-80" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <!-- Payment Method Selector Tabs -->
                <ul class="nav nav-tabs nav-justified border-bottom bg-slate-100 font-semibold text-xs" id="paymentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active py-3 text-slate-700" id="tab-qris" data-toggle="tab" href="#content-qris" role="tab" onclick="selectGateway('qris')">
                            <i class="fa-solid fa-qrcode text-base text-red-500 d-block mb-1"></i>
                            1. QRIS 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 text-slate-700" id="tab-cash" data-toggle="tab" href="#content-cash" role="tab" onclick="selectGateway('cash')">
                            <i class="fa-solid fa-money-bill-1-wave text-base text-emerald-600 d-block mb-1"></i>
                            2. Tunai (Cash)
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-4" id="paymentTabsContent">
                    <!-- 1. QRIS DINAMIS (Gambar dari Pengaturan) -->
                    <div class="tab-pane fade show active text-center" id="content-qris" role="tabpanel">
                        <div class="badge badge-light border text-danger font-bold text-xs px-3 py-1 mb-2">
                            <i class="fa-solid fa-qrcode mr-1"></i> QRIS STANDAR PEMBAYARAN NASIONAL
                        </div>
                        <p class="text-xs text-slate-500 mb-3">Scan menggunakan GoPay, OVO, Dana, ShopeePay, BCA, Livin Mandiri, BRImo, atau m-Banking lainnya.</p>

                        <!-- Gambar QRIS dari Pengaturan -->
                        <div class="d-flex justify-content-center mb-3">
                            @if(!empty($qrisImagePath) && file_exists(public_path($qrisImagePath)))
                                <div class="p-3 bg-white border-2 border-dashed border-red-300 rounded-2xl shadow-sm">
                                    <img src="{{ asset($qrisImagePath) }}"
                                         alt="QRIS Merchant"
                                         style="max-width: 230px; max-height: 230px; object-fit: contain;">
                                    <div class="text-xs font-mono font-bold text-slate-600 mt-2" id="qrisRefDisplay">QRIS-{{ date('Ymd') }}-SCAN</div>
                                </div>
                            @else
                                <div class="p-5 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 text-slate-400">
                                    <i class="fa-solid fa-qrcode text-6xl mb-3 d-block text-slate-300"></i>
                                    <p class="text-sm font-semibold mb-1">Gambar QRIS belum diupload</p>
                                    <p class="text-xs mb-0">Silakan upload gambar QRIS di menu
                                        <a href="{{ route('settings.index') }}" class="text-blue-600 font-bold" target="_blank">Pengaturan Toko</a>.
                                    </p>
                                </div>
                            @endif
                        </div>

                    

                        <button type="button" class="btn btn-success btn-lg rounded-xl font-bold px-4 py-2 text-sm shadow" onclick="simulatePaymentSuccess('qris')">
                            <i class="fa-solid fa-circle-check mr-1"></i> Konfirmasi Pembayaran QRIS Berhasil
                        </button>
                    </div>

                    <!-- 2. TUNAI (CASH) -->
                    <div class="tab-pane fade" id="content-cash" role="tabpanel">
                        <div class="max-w-md mx-auto">
                            <div class="form-group mb-3">
                                <label class="text-xs font-bold text-slate-700 uppercase">Uang Diterima dari Pelanggan (Rp):</label>
                                <input type="number" id="cashAmountInput" class="form-control form-control-lg text-xl font-bold text-emerald-600 border-2" placeholder="0" oninput="calculateChange()">
                            </div>

                            <!-- Quick Cash Nominal Buttons -->
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount('pas')">Uang Pas</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(10000)">10.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(20000)">20.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(50000)">50.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(100000)">100.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(200000)">200.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(300000)">300.000</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-lg font-bold text-xs py-2" onclick="setCashAmount(500000)">500.000</button>
                            </div>

                            <!-- Kembalian Display Box -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl mb-4 d-flex justify-content-between align-items-center">
                                <span class="font-semibold text-slate-600 text-sm">Kembalian:</span>
                                <h4 class="font-black text-slate-800 mb-0" id="cashChangeDisplay">Rp 0</h4>
                            </div>

                            <button type="button" class="btn btn-success btn-block btn-lg rounded-xl font-bold py-3 shadow" id="btnSubmitCash" onclick="submitCashPayment()" disabled>
                                <i class="fa-solid fa-check mr-2"></i> LUNASI PEMBAYARAN TUNAI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kasir Fitur 2: MODAL SUKSES & CETAK STRUK PEMBAYARAN -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden">
            <div class="modal-header bg-emerald-600 text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-bold text-white text-base mb-0">
                    <i class="fa-solid fa-circle-check mr-2"></i> Transaksi Berhasil!
                </h5>
                <button type="button" class="close text-white opacity-80" data-dismiss="modal" onclick="resetPos()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <!-- Struk Frame Preview -->
                <div class="bg-white p-4 border border-slate-200 rounded-xl shadow-sm font-mono text-xs" id="receiptThermalPreview">
                    <div class="text-center pb-2 border-bottom border-dashed border-slate-300">
                        <h6 class="font-bold text-slate-800 text-sm mb-1 uppercase">{{ $storeName }}</h6>
                        <p class="text-slate-500 mb-0 text-2xs">{{ \App\Models\Setting::get('store_address', 'Jakarta') }}</p>
                        <p class="text-slate-500 mb-0 text-2xs">Telp: {{ \App\Models\Setting::get('store_phone', '0812-9988-7766') }}</p>
                    </div>

                    <div class="py-2 border-bottom border-dashed border-slate-300 text-2xs text-slate-600">
                        <div class="d-flex justify-content-between">
                            <span>No: <strong id="receiptInvoice">INV-XXXX</strong></span>
                            <span id="receiptDate">25/10/2026 01:00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Kasir: <span id="receiptCashier">{{ Auth::user()->name }}</span></span>
                            <span>Pelanggan: <span id="receiptCustomer">Umum</span></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Metode: <strong id="receiptMethod" class="text-uppercase text-blue-600">QRIS</strong></span>
                            <span>Ref: <span id="receiptRef">REF-XXXX</span></span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="py-2 border-bottom border-dashed border-slate-300" id="receiptItemsContainer">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Totals -->
                    <div class="py-2 border-bottom border-dashed border-slate-300 text-xs">
                        <div class="d-flex justify-content-between text-slate-600">
                            <span>Subtotal:</span>
                            <span id="receiptSubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-slate-600">
                            <span>Diskon:</span>
                            <span id="receiptDiscount">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between font-bold text-slate-900 text-sm mt-1">
                            <span>TOTAL:</span>
                            <span id="receiptGrandTotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-slate-600 mt-1">
                            <span>Bayar:</span>
                            <span id="receiptAmountPaid">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-slate-600">
                            <span>Kembalian:</span>
                            <span id="receiptChange">Rp 0</span>
                        </div>
                    </div>

                    <div class="text-center pt-3 text-2xs text-slate-500">
                        <p class="mb-1">{{ \App\Models\Setting::get('store_footer', 'Terima kasih atas kunjungan Anda!') }}</p>
                        <p class="text-slate-400 mb-0 font-bold">*** LUNAS ***</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top border-slate-100 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-secondary font-semibold text-xs" data-dismiss="modal" onclick="resetPos()">
                        <i class="fa-solid fa-plus mr-1"></i> Transaksi Baru
                    </button>
                    <div class="form-check ml-2 d-none d-sm-inline-block">
                        <input class="form-check-input" type="checkbox" id="chkAutoPrint" {{ ($receiptAutoPrint ?? '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label text-2xs text-slate-500 font-semibold cursor-pointer" for="chkAutoPrint">
                            Auto Print
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a href="#" id="btnOpenReceiptTab" target="_blank" class="btn btn-outline-secondary font-bold text-xs rounded-xl" title="Buka struk di tab baru">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Preview
                    </a>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary font-bold text-xs rounded-l-xl px-3" onclick="printReceiptDirect()">
                            <i class="fa-solid fa-print mr-1"></i> Cetak ke Printer (<span id="currentPaperSizeLabel">{{ $receiptPaperSize ?? '80mm' }}</span>)
                        </button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split text-xs rounded-r-xl px-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Pilih Ukuran Kertas</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 text-xs">
                            <h6 class="dropdown-header text-slate-400 uppercase font-bold text-2xs">Ukuran Kertas Thermal</h6>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="printReceiptDirect('80mm')">
                                <i class="fa-solid fa-receipt mr-2 text-primary"></i> 80mm (Standar POS)
                            </a>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="printReceiptDirect('58mm')">
                                <i class="fa-solid fa-receipt mr-2 text-success"></i> 58mm (Mini Portable)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // State management for POS
    let products = [];
    let cart = [];
    let activeGateway = 'qris';
    let currentTransaction = null;
    let currentReceiptUrl = '';

    $(document).ready(function() {
        loadProducts();

        $('#posSearchInput').on('input', function() {
            filterAndRenderProducts();
        });

        $('#posCategoryFilter').on('change', function() {
            filterAndRenderProducts();
        });
    });

    // 1. Fetch products from server
    function loadProducts() {
        $.ajax({
            url: "{{ route('pos.products') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                products = data;
                renderProducts(products);
            },
            error: function() {
                console.error('Failed to load products');
            }
        });
    }

    // Filter products locally for instantaneous speed
    function filterAndRenderProducts() {
        const query = $('#posSearchInput').val().toLowerCase().trim();
        const categoryId = $('#posCategoryFilter').val();

        let filtered = products.filter(p => {
            const matchSearch = p.name.toLowerCase().includes(query) ||
                                p.code.toLowerCase().includes(query) ||
                                (p.barcode && p.barcode.toLowerCase().includes(query));
            const matchCategory = categoryId === 'all' || p.category_id == categoryId;
            return matchSearch && matchCategory;
        });

        renderProducts(filtered);
    }

    function renderProducts(items) {
        const grid = $('#productGrid');
        grid.empty();

        if (items.length === 0) {
            grid.html(`
                <div class="col-12 text-center py-5 text-slate-400">
                    <i class="fa-solid fa-box-open text-3xl mb-2 d-block"></i>
                    Produk tidak ditemukan
                </div>
            `);
            return;
        }

        const defaultImg = "{{ asset('images/default-product.svg') }}";

        items.forEach(product => {
            const isOutOfStock = product.stock <= 0;
            const imgSrc = product.image_url || defaultImg;
            const card = `
                <div class="col">
                    <div class="card h-100 border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-all cursor-pointer ${isOutOfStock ? 'opacity-60 pointer-events-none' : ''}" onclick="addToCart(${product.id})">
                        <div class="relative bg-slate-100 flex items-center justify-center overflow-hidden" style="height: 112px;">
                            <img src="${imgSrc}" 
                                 alt="${product.name}" 
                                 class="w-full h-28 object-cover" 
                                 onerror="this.onerror=null; this.src='${defaultImg}';">
                            ${isOutOfStock 
                                ? '<span class="absolute top-2 right-2 badge badge-danger font-bold text-2xs px-2 py-0.5 shadow-sm">HABIS</span>' 
                                : `<span class="absolute bottom-2 right-2 badge badge-dark bg-slate-900 text-white font-bold text-2xs px-1.5 py-0.5 shadow-sm">${product.stock} ${product.unit}</span>`}
                        </div>
                        <div class="p-2.5 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h6 class="font-bold text-slate-800 text-xs mb-1 line-clamp-1">${product.name}</h6>
                                <span class="text-2xs text-slate-400 font-mono">${product.code}</span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="font-black text-blue-600 text-sm">Rp ${new Intl.NumberFormat('id-ID').format(product.selling_price)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            grid.append(card);
        });
    }

    // 2. Cart Management
    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        if (!product || product.stock <= 0) return;

        const cartItem = cart.find(item => item.id === productId);
        if (cartItem) {
            if (cartItem.qty + 1 > product.stock) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: `Stok ${product.name} hanya tersisa ${product.stock}`,
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }
            cartItem.qty++;
        } else {
            cart.push({
                id: product.id,
                code: product.code,
                name: product.name,
                selling_price: product.selling_price,
                stock: product.stock,
                unit: product.unit,
                qty: 1
            });
        }

        renderCart();
    }

    function updateCartQty(productId, change) {
        const item = cart.find(i => i.id === productId);
        if (!item) return;

        const newQty = item.qty + change;
        if (newQty <= 0) {
            cart = cart.filter(i => i.id !== productId);
        } else if (newQty > item.stock) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: `Stok maksimal: ${item.stock}`,
                showConfirmButton: false,
                timer: 2000
            });
            return;
        } else {
            item.qty = newQty;
        }

        renderCart();
    }

    function removeCartItem(productId) {
        cart = cart.filter(i => i.id !== productId);
        renderCart();
    }

    function clearCart() {
        if (cart.length === 0) return;
        cart = [];
        renderCart();
    }

    function renderCart() {
        const list = $('#cartList');
        const emptyMsg = $('#emptyCartMessage');
        list.find('.cart-item-row').remove();

        let totalItems = 0;

        if (cart.length === 0) {
            emptyMsg.show();
            $('#btnOpenPayment').prop('disabled', true);
        } else {
            emptyMsg.hide();
            $('#btnOpenPayment').prop('disabled', false);

            cart.forEach(item => {
                totalItems += item.qty;
                const itemTotal = item.selling_price * item.qty;

                const row = `
                    <div class="cart-item-row p-2 mb-2 bg-slate-50 border border-slate-100 rounded-xl d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden mr-2">
                            <h6 class="font-bold text-slate-800 text-xs mb-0 text-truncate">${item.name}</h6>
                            <span class="text-2xs text-slate-400">Rp ${new Intl.NumberFormat('id-ID').format(item.selling_price)} x ${item.qty}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="font-bold text-xs text-slate-800 mr-1">Rp ${new Intl.NumberFormat('id-ID').format(itemTotal)}</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light btn-sm text-xs px-2 py-0 border" onclick="updateCartQty(${item.id}, -1)">-</button>
                                <span class="btn btn-light btn-sm text-xs px-2 py-0 border disabled font-bold text-slate-800">${item.qty}</span>
                                <button type="button" class="btn btn-light btn-sm text-xs px-2 py-0 border" onclick="updateCartQty(${item.id}, 1)">+</button>
                            </div>
                            <button type="button" class="btn btn-link text-danger btn-sm p-0 ml-1" onclick="removeCartItem(${item.id})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                `;
                list.append(row);
            });
        }

        $('#cartItemCount').text(`${totalItems} item dipilih`);
        calculateTotals();
    }

    function calculateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + (item.selling_price * item.qty), 0);
        const discount = Math.max(0, parseFloat($('#discountInput').val()) || 0);
        const grandTotal = Math.max(0, subtotal - discount);

        $('#subtotalDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
        $('#grandTotalDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal));

        return { subtotal, discount, grandTotal };
    }

    // 3. Checkout & Payment Modal
    function openPaymentModal() {
        const totals = calculateTotals();
        if (totals.grandTotal <= 0 && cart.length === 0) return;

        const formattedGrandTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(totals.grandTotal);
        $('#modalTotalTagihan').text(formattedGrandTotal);

        // Reset Cash tab
        $('#cashAmountInput').val('');
        $('#cashChangeDisplay').text('Rp 0');
        $('#btnSubmitCash').prop('disabled', true);

        // Open Modal
        $('#paymentModal').modal('show');
    }

    function selectGateway(method) {
        activeGateway = method;
    }

    // Cash Handling
    function setCashAmount(val) {
        const totals = calculateTotals();
        if (val === 'pas') {
            $('#cashAmountInput').val(totals.grandTotal);
        } else {
            $('#cashAmountInput').val(val);
        }
        calculateChange();
    }

    function calculateChange() {
        const totals = calculateTotals();
        const paid = parseFloat($('#cashAmountInput').val()) || 0;
        const change = paid - totals.grandTotal;

        if (change >= 0) {
            $('#cashChangeDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(change)).removeClass('text-danger').addClass('text-emerald-600');
            $('#btnSubmitCash').prop('disabled', false);
        } else {
            $('#cashChangeDisplay').text('Kurang: Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(change))).removeClass('text-emerald-600').addClass('text-danger');
            $('#btnSubmitCash').prop('disabled', true);
        }
    }

    function submitCashPayment() {
        const totals = calculateTotals();
        const paid = parseFloat($('#cashAmountInput').val()) || 0;
        if (paid < totals.grandTotal) return;

        processPaymentToServer('cash', paid, null);
    }

    function simulatePaymentSuccess(gateway) {
        const totals = calculateTotals();
        let ref = '';

        if (gateway === 'qris') {
            ref = 'QRIS-' + Date.now().toString().slice(-8);
        }

        processPaymentToServer(gateway, totals.grandTotal, ref);
    }

    function processPaymentToServer(paymentMethod, amountPaid, paymentRef) {
        const totals = calculateTotals();
        const customer = $('#customerName').val().trim() || 'Pelanggan Umum';

        const checkoutData = {
            items: cart.map(i => ({ id: i.id, qty: i.qty })),
            payment_method: paymentMethod,
            customer_name: customer,
            discount: totals.discount,
            amount_paid: amountPaid,
            payment_reference: paymentRef,
            notes: 'Transaksi kasir online via ' + paymentMethod.toUpperCase()
        };

        // Show loading spinner
        Swal.fire({
            title: 'Memproses Transaksi...',
            text: 'Menghubungkan ke gateway dan mencatat penjualan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('pos.checkout') }}",
            type: "POST",
            data: JSON.stringify(checkoutData),
            contentType: "application/json",
            dataType: "json",
            success: function(response) {
                $('#paymentModal').modal('hide');
                currentTransaction = response.transaction;
                currentReceiptUrl = response.receipt_url;

                // Reload local products to reflect deducted stock
                loadProducts();

                // Show Receipt Modal
                populateAndShowReceipt(currentTransaction, response.receipt_url);
            },
            error: function(xhr) {
                let errorMsg = 'Transaksi gagal diproses.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi Gagal!',
                    html: errorMsg,
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }

    // 4. Populate Receipt Preview (Kasir Fitur 2: Cetak Struk)
    function populateAndShowReceipt(tx, receiptUrl) {
        $('#receiptInvoice').text(tx.invoice_number);
        $('#receiptDate').text(new Date(tx.created_at).toLocaleString('id-ID'));
        $('#receiptCashier').text(tx.user ? tx.user.name : 'Kasir');
        $('#receiptCustomer').text(tx.customer_name);
        $('#receiptMethod').text(tx.payment_method.toUpperCase());
        $('#receiptRef').text(tx.payment_reference || '-');

        const itemsBox = $('#receiptItemsContainer');
        itemsBox.empty();

        tx.items.forEach(item => {
            const row = `
                <div class="mb-1">
                    <div class="d-flex justify-content-between font-bold text-slate-800">
                        <span>${item.product_name}</span>
                        <span>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
                    </div>
                    <div class="text-2xs text-slate-400">
                        ${item.quantity} x Rp ${new Intl.NumberFormat('id-ID').format(item.selling_price)}
                    </div>
                </div>
            `;
            itemsBox.append(row);
        });

        $('#receiptSubtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(tx.subtotal));
        $('#receiptDiscount').text('Rp ' + new Intl.NumberFormat('id-ID').format(tx.discount));
        $('#receiptGrandTotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(tx.total_amount));
        $('#receiptAmountPaid').text('Rp ' + new Intl.NumberFormat('id-ID').format(tx.amount_paid));
        $('#receiptChange').text('Rp ' + new Intl.NumberFormat('id-ID').format(tx.change_amount));

        $('#btnOpenReceiptTab').attr('href', receiptUrl);

        Swal.close();
        $('#receiptModal').modal('show');

        // Otomatis cetak ke printer jika opsi Auto Print aktif
        if ($('#chkAutoPrint').is(':checked')) {
            setTimeout(function() {
                printReceiptDirect();
            }, 600);
        }
    }

    // Integrasi Cetak Struk ke Printer (Menggunakan Hidden Iframe untuk Direct Print Dialog tanpa pop-up blocker)
    function printReceiptDirect(paperSize) {
        if (!currentReceiptUrl) return;

        let targetUrl = currentReceiptUrl;
        const selectedSize = paperSize || '{{ $receiptPaperSize ?? "80mm" }}';

        $('#currentPaperSizeLabel').text(selectedSize);

        // Tambahkan query parameter paper_size & autoprint
        const separator = targetUrl.includes('?') ? '&' : '?';
        targetUrl = `${targetUrl}${separator}paper_size=${selectedSize}&autoprint=1&t=${Date.now()}`;

        printUrlViaIframe(targetUrl);
    }

    function printUrlViaIframe(url) {
        let printIframe = document.getElementById('receiptPrintIframe');
        if (!printIframe) {
            printIframe = document.createElement('iframe');
            printIframe.id = 'receiptPrintIframe';
            printIframe.style.position = 'fixed';
            printIframe.style.right = '0';
            printIframe.style.bottom = '0';
            printIframe.style.width = '0';
            printIframe.style.height = '0';
            printIframe.style.border = '0';
            printIframe.style.opacity = '0';
            document.body.appendChild(printIframe);
        }

        printIframe.src = url;

        printIframe.onload = function() {
            try {
                printIframe.contentWindow.focus();
                printIframe.contentWindow.print();
            } catch (err) {
                console.warn('Iframe print fallback to window.open:', err);
                const printWindow = window.open(url, '_blank', 'width=450,height=600');
                if (printWindow) printWindow.focus();
            }
        };
    }

    function resetPos() {
        cart = [];
        $('#customerName').val('Pelanggan Umum');
        $('#discountInput').val(0);
        $('#posSearchInput').val('');
        renderCart();
        filterAndRenderProducts();
    }
</script>
@endpush
