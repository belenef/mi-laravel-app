@extends('layouts.app')

@section('content')

<style>
.card-img-top {
    height: 200px;
    object-fit: cover;
}

.card {
    height: 100%;
    cursor: pointer;
}
</style>

<h3 class="mb-3">Tienda Online</h3>
<div class="row">
    <div class="col-lg-8">
        <div class="row">

            <!-- Producto 1 -->
            <div class="col-md-4 mb-3">
                <div class="card product-card"
                     data-name="Disco de Radiocasete"
                     data-price="15"
                     data-img="{{ asset('assets/img/cd.jpg') }}"
                     data-desc="Reproductor clásico retro."
                     data-rating="4.5"
                     data-reviews="120">

                    <img src="{{ asset('assets/img/cd.jpg') }}" class="card-img-top">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Disco de Radiocasete</h5>
                        <p class="card-text">Reproductor clásico retro.</p>

                        <p class="fw-bold text-success">15 €</p>

                        <button class="btn btn-primary add-to-cart mt-auto"
                                data-product="Disco de Radiocasete"
                                data-price="15">
                            Añadir al carrito
                        </button>
                    </div>
                </div>
            </div>

            <!-- Producto 2 -->
            <div class="col-md-4 mb-3">
                <div class="card product-card"
                     data-name="Auriculares grandes"
                     data-price="40"
                     data-img="{{ asset('assets/img/cascos.jpg') }}"
                     data-desc="Sonido profesional."
                     data-rating="4.8"
                     data-reviews="250">

                    <img src="{{ asset('assets/img/cascos.jpg') }}" class="card-img-top">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Auriculares grandes</h5>
                        <p class="card-text">Sonido profesional.</p>

                        <p class="fw-bold text-success">40 €</p>

                        <button class="btn btn-primary add-to-cart mt-auto"
                                data-product="Auriculares grandes"
                                data-price="40">
                            Añadir al carrito
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CARRITO -->
    <div class="col-lg-4">
        <div class="card p-3">
            <h6>Carrito</h6>

            <ul id="cart-list" class="list-group mb-2">
                <li class="list-group-item">Vacío</li>
            </ul>

            <h6>Total: <span id="total-price">0</span> €</h6>

            <select id="payment-method" class="form-select mb-2">
                <option value="">Método de pago</option>
                <option>Tarjeta</option>
                <option>PayPal</option>
            </select>

            <button id="pay-button" class="btn btn-success">Pagar</button>

            <div id="payment-message" class="alert alert-success mt-2 d-none">
                ✅ Pago realizado correctamente
            </div>
        </div>
    </div>
</div>

<!-- MODAL CENTRADO -->
<div class="modal fade" id="productModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body text-center">
                <img id="modal-img" class="img-fluid mb-3">

                <h4 id="modal-title"></h4>
                <p id="modal-desc"></p>

                <p class="fw-bold text-success" id="modal-price"></p>

                <p>⭐ <span id="modal-rating"></span> 
                   (<span id="modal-reviews"></span> opiniones)</p>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const cartList = document.getElementById('cart-list');
    const totalPriceEl = document.getElementById('total-price');
    const paymentMethod = document.getElementById('payment-method');
    const paymentMessage = document.getElementById('payment-message');

    let cart = [];

    // 🛒 Añadir al carrito
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();

            const product = btn.dataset.product;
            const price = parseFloat(btn.dataset.price);

            const existing = cart.find(i => i.product === product);

            if (existing) existing.quantity++;
            else cart.push({product, price, quantity:1});

            renderCart();
        });
    });

    function renderCart(){
        cartList.innerHTML = '';
        let total = 0;

        if(cart.length === 0){
            cartList.innerHTML = '<li class="list-group-item">Vacío</li>';
            totalPriceEl.textContent = 0;
            return;
        }

        cart.forEach((item,i)=>{
            total += item.price * item.quantity;

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';

            li.innerHTML = `
                <span>${item.product} (x${item.quantity}) - ${item.price}€</span>
                <div>
                    <button class="btn btn-sm btn-outline-secondary decrease" data-i="${i}">-</button>
                    <button class="btn btn-sm btn-outline-secondary increase" data-i="${i}">+</button>
                    <button class="btn btn-sm btn-outline-danger remove" data-i="${i}">🗑</button>
                </div>
            `;

            cartList.appendChild(li);
        });

        totalPriceEl.textContent = total.toFixed(2);

        // ➖ Disminuir
        document.querySelectorAll('.decrease').forEach(btn=>{
            btn.onclick = ()=>{
                const i = btn.dataset.i;

                if(cart[i].quantity > 1){
                    cart[i].quantity--;
                } else {
                    cart.splice(i,1);
                }

                renderCart();
            };
        });

        // ➕ Aumentar
        document.querySelectorAll('.increase').forEach(btn=>{
            btn.onclick = ()=>{
                const i = btn.dataset.i;
                cart[i].quantity++;
                renderCart();
            };
        });

        // 🗑 Eliminar
        document.querySelectorAll('.remove').forEach(btn=>{
            btn.onclick = ()=>{
                const i = btn.dataset.i;
                cart.splice(i,1);
                renderCart();
            };
        });
    }

    // 💳 Pagar
    document.getElementById('pay-button').onclick = ()=>{
        if(cart.length===0) return alert('Carrito vacío');
        if(!paymentMethod.value) return alert('Selecciona método');

        cart=[];
        renderCart();
        paymentMessage.classList.remove('d-none');
    };

    // 🔍 MODAL producto
    document.querySelectorAll('.product-card').forEach(card=>{
        card.addEventListener('click', ()=>{

            document.getElementById('modal-title').textContent = card.dataset.name;
            document.getElementById('modal-desc').textContent = card.dataset.desc;
            document.getElementById('modal-price').textContent = card.dataset.price + ' €';
            document.getElementById('modal-img').src = card.dataset.img;
            document.getElementById('modal-rating').textContent = card.dataset.rating;
            document.getElementById('modal-reviews').textContent = card.dataset.reviews;

            new bootstrap.Modal(document.getElementById('productModal')).show();
        });
    });

});
</script>
@endpush