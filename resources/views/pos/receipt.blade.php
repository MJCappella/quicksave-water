<x-layouts.app>
    <x-slot:title>Receipt - {{ $invoice->invoice_no }}</x-slot:title>

    <div class="max-w-md mx-auto space-y-4">
        
        <!-- Action Toolbar -->
        <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <a href="{{ route('pos.index') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 flex items-center gap-1.5">
                &larr; Return to POS Counter
            </a>
            <button type="button" onclick="window.print()"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Thermal Receipt</span>
            </button>
        </div>

        <!-- PRINTABLE THERMAL RECEIPT SLIP -->
        <div id="printable-receipt" class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-md font-mono text-xs text-slate-900 space-y-4">
            
            <!-- Company Header -->
            <div class="text-center space-y-1 border-b border-dashed border-slate-300 pb-4">
                <div class="font-black text-base tracking-tight text-slate-950">QUICKSAVE AGENCIES LTD</div>
                <div class="text-[10px] uppercase font-bold text-slate-600">DRINKING WATER PACKAGING & DISTRIBUTION</div>
                <div class="text-[10px] text-slate-500">Commercial St., Industrial Area, Nairobi</div>
                <div class="text-[10px] text-slate-500">PIN: P051982741X • TEL: +254 722 100 200</div>
                <div class="text-[11px] font-bold text-emerald-800 mt-1">*** OFFICIAL SALE RECEIPT ***</div>
            </div>

            <!-- Receipt Metadata -->
            <div class="text-[11px] space-y-1 border-b border-dashed border-slate-300 pb-3">
                <div class="flex justify-between">
                    <span class="text-slate-500">RECEIPT NO:</span>
                    <span class="font-bold">{{ $invoice->invoice_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">DATE & TIME:</span>
                    <span>{{ $invoice->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">DISPATCH STORE:</span>
                    <span>{{ $invoice->store->name ?? 'Superior Center' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">CASHIER:</span>
                    <span>{{ $invoice->cashier_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">CUSTOMER:</span>
                    <span class="font-bold truncate max-w-[180px]">{{ $invoice->customer->name ?? 'Walk-In Customer' }}</span>
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="border-b border-dashed border-slate-300 pb-3 space-y-2">
                <div class="flex justify-between font-bold text-[10px] uppercase text-slate-500 border-b border-slate-200 pb-1">
                    <span>ITEM / DESCRIPTION</span>
                    <span>QTY x RATE</span>
                    <span>TOTAL</span>
                </div>
                @foreach ($invoice->items as $item)
                    <div class="space-y-0.5">
                        <div class="font-bold text-[11px]">{{ $item->product->name ?? 'Product' }}</div>
                        <div class="flex justify-between text-[11px] text-slate-600">
                            <span>{{ $item->product->sku ?? '' }}</span>
                            <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}</span>
                            <span class="font-bold text-slate-900">{{ number_format($item->total, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Totals & Payment Breakdown -->
            <div class="space-y-1.5 text-[11px] border-b border-dashed border-slate-300 pb-3">
                <div class="flex justify-between">
                    <span class="text-slate-500">SUBTOTAL:</span>
                    <span class="font-semibold">KES {{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">VAT (16% INCL):</span>
                    <span class="font-semibold">KES {{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-black pt-1 border-t border-slate-300">
                    <span>TOTAL PAYABLE:</span>
                    <span class="text-emerald-800">KES {{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-slate-500">PAYMENT METHOD:</span>
                    <span class="font-bold uppercase">{{ $invoice->payment_method }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">AMOUNT TENDERED:</span>
                    <span class="font-bold">KES {{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                @if ($invoice->balance_due > 0)
                    <div class="flex justify-between text-rose-600 font-bold">
                        <span>BALANCE DUE:</span>
                        <span>KES {{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-emerald-700 font-bold">
                        <span>CHANGE / BALANCE:</span>
                        <span>KES 0.00</span>
                    </div>
                @endif
            </div>

            <!-- Footer Message & Barcode mock -->
            <div class="text-center pt-2 space-y-1 text-[10px] text-slate-500">
                <p class="font-bold text-slate-700">PURE & CLEAN DRINKING WATER FOR HEALTHY LIVING</p>
                <p>Goods once sold cannot be returned unless damaged prior to delivery.</p>
                <div class="py-2 flex justify-center">
                    <div class="font-mono tracking-widest text-xs bg-slate-100 px-3 py-1 border border-slate-200">
                        *{{ $invoice->invoice_no }}*
                    </div>
                </div>
                <p class="text-[9px]">Powered by Quicksave Packaging ERP</p>
            </div>

        </div>

    </div>
</x-layouts.app>
