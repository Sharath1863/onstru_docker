<li class="mb-3">
    <div class="body-head d-block mb-3">
        <h5>
            <i class="fas fa-filter pe-2"></i> Filter
        </h5>
    </div>
</li>
<hr>
<li class="mb-3">
    <input type="text" name="keyword" id="keywordSearch" class="form-control" placeholder="Search"
        value="{{ request('keyword') }}">
</li>
<!-- <hr> -->
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse"
        aria-expanded="false">
        <div class="btnname">
            <span>Order Status</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach (['All', 'Processing', 'Shipped', 'Cancelled', 'Delivered'] as $sts)
                <li>
                    <input type="checkbox" class="filter-checkbox sts-filter" id="{{ $sts }}" value="{{ $sts }}">
                    <label for="{{ $sts }}">{{ $sts }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse0"
        aria-expanded="false">
        <div class="btnname">
            <span>Order Time</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse0">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach (['Last 10 Days', 'Last 30 Days', 'Last 6 Months', 'Last Year'] as $time)
                <li>
                    <input type="checkbox" class="filter-checkbox time-filter" id="{{ $time }}" value="{{ $time }}">
                    <label for="{{ $time }}">{{ $time }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>