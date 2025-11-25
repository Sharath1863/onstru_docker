<div class="body-head mb-3">
    <h5>Services</h5>
    <a>
        <button class="removebtn" id="highlightServices">Highlighted Services</button>
    </a>
</div>

<!-- Search -->
<div class="form-div">
    <div class="inpleftflex mb-3">
        <i class="fas fa-search"></i>
        <input type="text" name="keyword" id="serviceSearch" class="form-control border-0" placeholder="Search"
            value="{{ request('servicesKeyword') }}">
    </div>
</div>

<!-- Empty State -->
<div class="side-cards shadow-none border-0" id="noServices"
    style="{{ count($services) > 0 ? 'display: none;' : 'display: block;' }}">
    <div class="cards-content d-flex align-items-center justify-content-center flex-column gap-2">
        <img src="{{ asset('assets/images/Empty/NoServices.png') }}" height="200px" class="d-flex mx-auto mb-2"
            alt="">
        <h5 class="text-center mb-0">No Services Found</h5>
        <h6 class="text-center bio">No services are available right now - try adjusting your search or browse through other
            options.</h6>
    </div>
</div>

<!-- Service Cards -->
<div class="service-cards">
    @foreach ($services as $service)
        <div class="side-cards service-card filter-services w-100 mx-auto row position-relative"
            data-type="{{ $service->serviceType->value ?? '' }}" data-budget="{{ $service->price_per_sq_ft }}"
            data-location="{{ $service->locationRelation->value ?? '' }}"
            data-highlighted="{{ $service->highlighted }}">
            <div class="col-sm-12 col-md-5 m-auto">
                <img src="{{ asset($service->image ? 'https://onstru-social.s3.ap-south-1.amazonaws.com/' . $service->image : 'assets/images/NoImage.png') }}"
                    class="object-fit-cover object-center w-100 rounded-3" height="175px" alt="{{ $service->title }}">
            </div>
            <div class="col-sm-12 col-md-7 m-auto">
                @if ($service->highlighted == '1')
                    <span class="badge"><img src="{{ asset('assets/images/icon_highlights.png') }}" height="15px"
                            class="pe-1" alt=""> Highlighted</span>
                @endif
                <div class="cards-head">
                    <h5 class="mb-2 long-text">{{ $service->title ?? '-' }}</h5>
                    <h6 class="mb-2 long-text">
                        <i class="fas fa-tools pe-1"></i> {{ $service->serviceType->value ?? '-' }}
                    </h6>
                    <h6 class="mb-2 long-text">{{ $service->description ?? '-' }}</h6>
                </div>
                <div class="cards-content d-flex align-items-start justify-content-between flex-wrap">
                    <div>
                        <h6 class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            {{ number_format($service->reviews_avg_stars, 1) }}
                            ({{ $service->reviews_count }})
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-location-dot pe-1"></i>{{ $service->locationRelation->value ?? '-' }}
                        </h6>
                        <h6 class="mb-2">
                            <i class="fas fa-maximize pe-1"></i> {{ $service->price_per_sq_ft ?? '-' }} per sqft.
                        </h6>
                    </div>
                </div>
                <div class="col-12">
                    <a href="{{ url('individual-service/' . $service->id) }}">
                        <button class="removebtn w-100">View Service</button>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Search Filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const highlightServices = document.getElementById('highlightServices');
        const serviceSearch = document.getElementById('serviceSearch');
        const noServices = document.getElementById('noServices');
        const serviceCards = document.querySelectorAll('.filter-services');
        let showHighlighted = false;

        serviceSearch.addEventListener('input', function() {
            let serviceMatch = false;
            const servicesKeyword = this.value.toLowerCase();
            serviceCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(servicesKeyword)) {
                    card.style.display = 'flex';
                    serviceMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noServices.style.display = serviceMatch ? 'none' : 'block';
        });

        highlightServices.addEventListener('click', function() {
            showHighlighted = !showHighlighted;
            let serviceMatch = false;
            serviceCards.forEach(card => {
                if (!showHighlighted || card.dataset.highlighted === "1") {
                    card.style.display = 'flex';
                    serviceMatch = true;
                } else {
                    card.style.display = 'none';
                }
            });
            noServices.style.display = serviceMatch ? 'none' : 'block';
            highlightServices.textContent = showHighlighted ? "All Services" : "Highlighted Services";
        });
    });
</script>
