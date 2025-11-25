@php
    $roles = ['Contractor', 'Vendor', 'Consultant', 'Consumer', 'Technical', 'Non-Technical'];
@endphp

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
            <span>Category</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($roles as $role)
                <li>
                    <input type="checkbox" class="filter-checkbox category-filter" id="{{ $role }}"
                        value="{{ $role }}">
                    <label for="{{ $role }}">{{ $role }}</label>
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

