<li class="mb-3">
    <div class="body-head d-block mb-3">
        <h5>
            <i class="fas fa-filter pe-2"></i> Filter
        </h5>
    </div>
</li>
<hr>
<li class="mb-3">
    <input type="text" name="search" id="keywordSearch" class="form-control" placeholder="Search"
        value="{{ request('search') }}">
</li>
<!-- <hr> -->
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse"
        aria-expanded="false">
        <div class="btnname">
            <span>Service Type</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($serviceTypes as $id => $serviceType)
                <li>
                    <input type="checkbox" class="filter-checkbox type-filter" id="{{ $id }}"
                        value="{{ $serviceType }}">
                    <label for="{{ $id }}">{{ $serviceType }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse2"
        aria-expanded="false">
        <div class="btnname">
            <span>Location</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse2">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($locations as $loc)
                <li>
                    <input type="checkbox" class="filter-checkbox loc-filter" id="{{ $loc }}"
                        value="{{ $loc }}">
                    <label for="{{ $loc }}">{{ $loc }}</label>
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
            <span>Budget</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse0">
        <ul class="btn-toggle-nav list-unstyled text-start px-2 py-2">
            <li>
                <input type="number" name="Budget" id="budget" class="form-control" min="0"
                    placeholder="Enter Budget" value="{{ request('budget') }}">
            </li>
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse3"
        aria-expanded="false">
        <div class="btnname">
            <span>Highlighted</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse3">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ([1 => 'Highlighted', 0 => 'Not Highlighted'] as $key => $label)
                <li>
                    <input type="checkbox" class="filter-checkbox highlight-filter" id="highlight_{{ $key }}"
                        value="{{ $key }}">
                    <label for="highlight_{{ $key }}">{{ $label }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
