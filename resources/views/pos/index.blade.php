<x-layouts.app>
    <x-slot:title>Point of Sale (POS) Counter</x-slot:title>

    <div x-data="posApp()" class="h-[calc(100vh-6.5rem)] flex flex-col lg:flex-row gap-5 -m-2 sm:-m-4">
        
        <!-- LEFT PANEL: PRODUCT CATALOG & QUICK SELECTION -->
        <div class="flex-1 flex flex-col bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            
            <!-- Filter Bar & Search -->
            <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/50">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="searchQuery" placeholder="Search water products, 500ml, refills, or SKU..."
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 shadow-2xs">
                    </div>

                    <!-- Store Selector for POS -->
                    <div class="shrink-0 flex items-center gap-1.5">
                        <span class="text-[11px] font-semibold text-slate-500">Dispatch Location:</span>
                        <select x-model="selectedStoreId" @change="switchStore()"
                                class="text-xs rounded-lg border-slate-200 bg-white py-1.5 px-3 font-semibold text-slate-800 shadow-2xs">
                            @foreach ($stores as $st)
                                <option value="{{ $st->id }}" {{ $currentStore->id == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                    <button type="button" @click="activeCategory = 'All'"
                            :class="activeCategory === 'All' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                        All Items
                    </button>
                    @foreach ($categories as $cat)
                        <button type="button" @click="activeCategory = '{{ $cat }}'"
                                :class="activeCategory === '{{ $cat }}' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                                class="px-3 py-1.5 rounded-lg whitespace-nowrap transition">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                             class="group cursor-pointer bg-white rounded-xl border border-slate-200/90 p-3.5 hover:border-emerald-500 hover:shadow-md transition flex flex-col justify-between text-left relative overflow-hidden">
                            
                            <!-- Category badge & stock badge -->
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="text-[9px] uppercase tracking-wider font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100 truncate"
                                      x-text="product.category"></span>
                                <span class="text-[10px] font-bold"
                                      :class="product.stock > 0 ? 'text-slate-500' : 'text-rose-600'"
                                      x-text="product.stock + ' in stock'"></span>
                            </div>

                            <div class="my-1">
                                <h3 class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition line-clamp-2 leading-snug"
                                    x-text="product.name"></h3>
                                <p class="text-[10px] font-mono text-slate-400 mt-0.5" x-text="product.sku"></p>
                            </div>

                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Retail</span>
                                    <span class="text-xs font-black text-slate-900">
                                        KES <span x-text="formatNumber(product.price)"></span>
                                    </span>
                                </div>
                                <button type="button" 
                                        class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center transition shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL: CART & CHECKOUT TERMINAL -->
        <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden shrink-0">
            
            <!-- Terminal Header -->
            <div class="p-4 border-b border-slate-100 bg-slate-900 text-white flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-black tracking-wider uppercase text-emerald-400">Order Terminal</h2>
                    <p class="text-[10px] text-slate-400">Cashier: {{ auth()->user()->name ?? 'James Mwangi' }}</p>
                </div>
                <button type="button" @click="clearCart()" x-show="cart.length > 0" 
                        class="text-[10px] text-rose-400 hover:text-rose-300 font-bold uppercase tracking-wider">
                    Reset Cart
                </button>
            </div>

            <!-- Customer Selector -->
            <div class="p-3.5 border-b border-slate-100 bg-slate-50/70">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Customer / Account</label>
                <select x-model="selectedCustomerId" 
                        class="w-full text-xs rounded-xl border-slate-200 bg-white py-2 px-3 font-semibold text-slate-800 shadow-2xs focus:border-emerald-500">
                    @foreach ($customers as $cust)
                        <option value="{{ $cust->id }}">
                            {{ $cust->name }} ({{ $cust->customer_type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-3.5 space-y-2">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 text-center py-12">
                        <svg class="w-12 h-12 text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <p class="text-xs font-semibold">Cart is currently empty</p>
                        <p class="text-[10px] text-slate-400">Select products on the left to begin sale</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.product_id">
                    <div class="p-2.5 rounded-xl border border-slate-200/80 bg-white shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></h4>
                            <p class="text-[10px] text-slate-400">
                                @ KES <span x-text="formatNumber(item.unit_price)"></span>
                            </p>
                        </div>

                        <!-- Quantity control -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="decrementItem(index)" 
                                    class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
                                -
                            </button>
                            <span class="w-7 text-center font-black text-xs text-slate-900" x-text="item.quantity"></span>
                            <button type="button" @click="incrementItem(index)" 
                                    class="w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
                                +
                            </button>
                        </div>

                        <!-- Line total -->
                        <div class="text-right shrink-0 min-w-[65px]">
                            <div class="text-xs font-black text-slate-900">
                                KES <span x-text="formatNumber(item.quantity * item.unit_price)"></span>
                            </div>
                            <button type="button" @click="removeItem(index)" class="text-[10px] text-rose-500 hover:underline">
                                remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Bottom Totals & Tender Form -->
            <div class="p-4 border-t border-slate-200/80 bg-slate-50 space-y-3">
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Items Subtotal:</span>
                        <span class="font-semibold text-slate-800">KES <span x-text="formatNumber(cartTotal)"></span></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>VAT (Inclusive):</span>
                        <span class="font-semibold text-slate-800">KES 0.00</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Grand Total:</span>
                        <span class="text-emerald-700 text-base">KES <span x-text="formatNumber(cartTotal)"></span></span>
                    </div>
                </div>

                <!-- Payment Method Selector -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Select Payment Method</label>
                    <div class="grid grid-cols-4 gap-1.5 text-xs">
                        <button type="button" @click="paymentMethod = 'Cash'"
                                :class="paymentMethod === 'Cash' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-400' : 'bg-white border border-slate-200 text-slate-700'"
                                class="py-2 px-1 rounded-xl text-center transition">
                            Cash
                        </button>
                        <button type="button" @click="paymentMethod = 'Mpesa'"
                                :class="paymentMethod === 'Mpesa' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-400' : 'bg-white border border-slate-200 text-slate-700'"
                                class="py-2 px-1 rounded-xl text-center transition">
                            M-Pesa
                        </button>
                        <button type="button" @click="paymentMethod = 'Bank'"
                                :class="paymentMethod === 'Bank' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-400' : 'bg-white border border-slate-200 text-slate-700'"
                                class="py-2 px-1 rounded-xl text-center transition">
                            Bank
                        </button>
                        <button type="button" @click="paymentMethod = 'Credit'"
                                :class="paymentMethod === 'Credit' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-400' : 'bg-white border border-slate-200 text-slate-700'"
                                class="py-2 px-1 rounded-xl text-center transition">
                            Credit
                        </button>
                    </div>
                </div>

                <!-- Reference input (e.g. Mpesa Code) -->
                <div x-show="paymentMethod !== 'Cash'">
                    <label class="block text-[10px] font-semibold text-slate-600 mb-1" 
                           x-text="paymentMethod === 'Mpesa' ? 'M-Pesa Confirmation Code (e.g. QA84920482):' : 'Transaction Ref / Check No:'"></label>
                    <input type="text" x-model="referenceNo" placeholder="Enter confirmation reference..."
                           class="w-full text-xs rounded-xl border border-slate-200 py-1.5 px-3 bg-white focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Submit / Complete Button -->
                <button type="button" @click="submitOrder()" :disabled="cart.length === 0 || isSubmitting"
                        class="w-full py-3 px-4 rounded-xl font-black text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <span x-show="!isSubmitting">Complete Sale & Print Receipt &rarr;</span>
                    <span x-show="isSubmitting">Processing Sale...</span>
                </button>
            </div>

        </div>

    </div>

    <!-- Hidden form for standard submission -->
    <form id="pos-checkout-form" method="POST" action="{{ route('pos.store') }}" style="display: none;">
        @csrf
        <input type="hidden" name="store_id" id="form-store-id">
        <input type="hidden" name="customer_id" id="form-customer-id">
        <input type="hidden" name="payment_method" id="form-payment-method">
        <input type="hidden" name="reference_no" id="form-reference-no">
        <div id="form-items-container"></div>
    </form>

    <script>
        function posApp() {
            return {
                products: @json($posProducts),
                searchQuery: '',
                activeCategory: 'All',
                selectedStoreId: {{ $currentStore->id }},
                selectedCustomerId: {{ $customers->first()->id ?? 1 }},
                cart: [],
                paymentMethod: 'Mpesa',
                referenceNo: '',
                isSubmitting: false,

                get filteredProducts() {
                    return this.products.filter(p => {
                        const matchesCategory = this.activeCategory === 'All' || p.category === this.activeCategory;
                        const query = this.searchQuery.toLowerCase();
                        const matchesQuery = p.name.toLowerCase().includes(query) || p.sku.toLowerCase().includes(query);
                        return matchesCategory && matchesQuery;
                    });
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },

                addToCart(product) {
                    const existing = this.cart.find(i => i.product_id === product.id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            unit_price: product.price,
                            quantity: 1,
                        });
                    }
                },

                incrementItem(index) {
                    this.cart[index].quantity++;
                },

                decrementItem(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.removeItem(index);
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                },

                formatNumber(num) {
                    return Number(num || 0).toLocaleString('en-KE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                switchStore() {
                    window.location.href = "{{ route('pos.index') }}?store_id=" + this.selectedStoreId;
                },

                submitOrder() {
                    if (this.cart.length === 0) return;
                    this.isSubmitting = true;

                    const form = document.getElementById('pos-checkout-form');
                    document.getElementById('form-store-id').value = this.selectedStoreId;
                    document.getElementById('form-customer-id').value = this.selectedCustomerId;
                    document.getElementById('form-payment-method').value = this.paymentMethod;
                    document.getElementById('form-reference-no').value = this.referenceNo;

                    const container = document.getElementById('form-items-container');
                    container.innerHTML = '';

                    this.cart.forEach((item, idx) => {
                        container.innerHTML += `<input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">`;
                        container.innerHTML += `<input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">`;
                        container.innerHTML += `<input type="hidden" name="items[${idx}][unit_price]" value="${item.unit_price}">`;
                    });

                    form.submit();
                }
            };
        }
    </script>
</x-layouts.app>
