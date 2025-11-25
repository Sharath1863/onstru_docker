<div class="body-head mb-3">
    <h5>Products</h5>
    <a>
        <button class="removebtn" id="highlightProducts">Highlighted Products</button>
    </a>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="productSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('productsKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noProducts"
    style="{{ count($products) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoProducts.png') }}" height="200px" class="d-flex mx-auto mb-2"
            alt="">
        <h5 class="text-center mb-0">No Products Found</h5>
        <h6 class="text-center bio">No products are available at the moment - try a different search or explore other
            categories.</h6>
    </div>
</div>

<!-- Product Cards -->
<div class="product-cards">
    @foreach ($products as $product)
        <div class="side-cards product-card filter-products position-relative"
            data-category="{{ $product->category }}" data-location="{{ $product->location }}"
            data-stock="{{ $product->availability }}" data-price="{{ $product->price }}"
            data-size="{{ $product->size }}" data-highlight="{{ $product->highlighted }}">
            <img src="{{ asset('https://onstru-social.s3.ap-south-1.amazonaws.com/' . $product->cover_img ?? 'assets/images/NoImage.png') }}"
                class="mb-3 w-100 rounded-3 position-relative object-fit-cover object-center" height="175px"
                alt="">
            @if ($product->highlighted == '1')
                <a class="badge d-flex align-items-center">
                    <img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px" class="pe-1"
                        alt="">
                    <span>Highlighted</span>
                </a>
            @endif
            <div class="cards-head">
                <h5 class="mb-2">{{ $product->name }}</h5>
                <h6 class="mb-2 long-text">{{ $product->brand_name }} | {{ $product->categoryRelation->value }}</h6>
            </div>
            <div class="cards-content">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h6 class="mb-2">
                        <i class="fas fa-star text-warning"></i>
                        {{ number_format($product->reviews_avg_stars, 1) }}
                        ({{ $product->reviews_count }})
                    </h6>
                    @if ($product->availability === 'In Stock')
                        <h6 class="mb-2 text-success">{{ $product->availability }}</h6>
                    @else
                        <h6 class="mb-2 text-danger">{{ $product->availability }}</h6>
                    @endif
                </div>
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h6 class="mb-2">₹
                        {{ number_format(($product->base_price + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                        <span class="dashed-line ps-1">₹
                            {{ number_format(($product->mrp + $product->cashback_price + $product->margin) * (1 + $product->tax_percentage / 100), 2) }}
                        </span>
                    </h6>
                </div>
            </div>
            <a href="{{ url('individual-product/' . $product->id) }}">
                <button class="removebtn w-100">View Product</button>
            </a>
        </div>
    @endforeach
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const highlightProducts = document.getElementById('highlightProducts');
        const productSearch = document.getElementById('productSearch');
        const noProducts = document.getElementById('noProducts');
        const productCards = document.querySelectorAll('.filter-products');
        let showHighlighted = false;

        productSearch.addEventListener('input', function() {
            let productMatch = false;
            const productsKeyword = this.value.toLowerCase();
            productCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(productsKeyword)) {
                    card.style.display = 'block';
                    productMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noProducts.style.display = productMatch ? 'none' : 'block';
        });

        highlightProducts.addEventListener('click', function() {
            showHighlighted = !showHighlighted;
            let productMatch = false;
            productCards.forEach(card => {
                if (!showHighlighted || card.dataset.highlight === "1") {
                    card.style.display = 'block';
                    productMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noProducts.style.display = productMatch ? 'none' : 'block';
            highlightProducts.textContent = showHighlighted ? "All Products" : "Highlighted Products";
        });
    });
</script>
