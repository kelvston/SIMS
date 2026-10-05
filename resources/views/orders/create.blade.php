@extends('layouts.app')

@section('title', 'New Order')
@section('subtitle', 'Create an order and generate its invoice.')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6">New Order</h1>
    @if($errors->any())<div class="mb-4 rounded bg-red-100 border border-red-300 text-red-800 p-3"><ul class="list-disc ml-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('orders.store') }}" id="order-form">@csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div><label class="block font-medium mb-1">Customer name</label><input required name="customer_name" value="{{ old('customer_name') }}" class="w-full border rounded p-2"></div>
            <div><label class="block font-medium mb-1">Phone</label><input name="customer_phone" value="{{ old('customer_phone') }}" class="w-full border rounded p-2"></div>
            <div><label class="block font-medium mb-1">Email</label><input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full border rounded p-2"></div>
            <div><label class="block font-medium mb-1">Order date</label><input required type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}" class="w-full border rounded p-2"></div>
            <div><label class="block font-medium mb-1">Status</label><select name="status" class="w-full border rounded p-2">@foreach(['pending','confirmed','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(old('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div><label class="block font-medium mb-1">Discount (Tsh)</label><input id="discount" min="0" step="0.01" type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" class="w-full border rounded p-2"></div>
        </div>
        <div class="mb-5"><div class="flex justify-between items-center mb-2"><h2 class="font-semibold">Order items</h2><button type="button" id="add-item" class="text-blue-700 font-medium">+ Add item</button></div>
            <p class="text-sm text-gray-600 mb-2">Choose a stocked product to fill its name and selling price automatically, or select Custom item.</p>
            <div class="table-scroll"><table class="min-w-full"><thead><tr class="text-left border-b"><th class="p-2">Product</th><th class="p-2 w-28">Qty</th><th class="p-2 w-44">Unit price</th><th class="p-2 w-36">Total</th><th></th></tr></thead><tbody id="items"></tbody></table></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block font-medium mb-1">Notes</label><textarea name="notes" rows="3" class="w-full border rounded p-2">{{ old('notes') }}</textarea></div><div class="border rounded bg-gray-50 p-4 text-right"><p>Subtotal: <b id="subtotal">Tsh 0.00</b></p><p>Discount: <b id="discount-total">Tsh 0.00</b></p><p class="text-xl mt-2">Total: <b id="total">Tsh 0.00</b></p></div></div>
        <div class="mt-6 flex gap-3"><button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded">Create order & invoice</button><a href="{{ route('orders.index') }}" class="px-5 py-2">Cancel</a></div>
    </form>
</div>
<script>
const items=document.getElementById('items'), discount=document.getElementById('discount'), catalog=@json($catalogItems); let index=0;
function money(v){return Math.round((parseFloat(v)||0)*100)} function fmt(v){return `Tsh ${(v/100).toFixed(2)}`}
function escapeHtml(value){const element=document.createElement('div');element.textContent=String(value ?? '');return element.innerHTML}
function totals(){let sum=0;items.querySelectorAll('tr').forEach(row=>{const q=Math.max(1,parseInt(row.querySelector('.qty').value)||1), p=money(row.querySelector('.price').value);row.querySelector('.line-total').textContent=fmt(q*p);sum+=q*p});const d=money(discount.value);document.getElementById('subtotal').textContent=fmt(sum);document.getElementById('discount-total').textContent=fmt(d);document.getElementById('total').textContent=fmt(Math.max(sum-d,0))}
function options(){return `<option value="">Select a product</option>${catalog.map((item,i)=>`<option value="${i}">${escapeHtml(item.label)} — Tsh ${Number(item.price).toFixed(2)} (${item.available} in stock)</option>`).join('')}<option value="custom">Custom item</option>`}
function addItem(){const i=index++;items.insertAdjacentHTML('beforeend',`<tr><td class="p-2"><select class="picker w-full border rounded p-2">${options()}</select><input type="hidden" class="phone-id" name="items[${i}][phone_id]"><input required name="items[${i}][description]" placeholder="Product description" class="description w-full border rounded p-2 mt-1"></td><td class="p-2"><input required name="items[${i}][quantity]" value="1" min="1" type="number" class="qty w-full border rounded p-2"></td><td class="p-2"><input required name="items[${i}][unit_price]" value="0" min="0" step="0.01" type="number" class="price w-full border rounded p-2"></td><td class="p-2 line-total">Tsh 0.00</td><td><button type="button" class="remove text-red-600">Remove</button></td></tr>`); totals()}
document.getElementById('add-item').onclick=addItem; items.addEventListener('input',totals); items.addEventListener('change',e=>{if(!e.target.classList.contains('picker'))return;const row=e.target.closest('tr'), selected=e.target.value, quantity=row.querySelector('.qty'), phoneId=row.querySelector('.phone-id');if(selected === 'custom' || selected === ''){quantity.removeAttribute('max');phoneId.value='';return}const item=catalog[Number(selected)];row.querySelector('.description').value=item.label;row.querySelector('.price').value=Number(item.price).toFixed(2);phoneId.value=item.id;quantity.max=item.available;totals()}); items.addEventListener('click',e=>{if(e.target.classList.contains('remove')){e.target.closest('tr').remove();totals()}});discount.oninput=totals;addItem();
</script>
@endsection
