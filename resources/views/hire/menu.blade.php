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
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse0"
        aria-expanded="false">
        <div class="btnname">
            <span>Category</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse0">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($category as $cat)
                <li>
                    <input type="checkbox" class="filter-checkbox category-filter" id="{{ $cat->value }}"
                        value="{{ $cat->value }}">
                    <label for="{{ $cat->value }}">{{ $cat->value }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse1"
        aria-expanded="false">
        <div class="btnname">
            <span>Job Type</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse1">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach (['On-Site', 'Remote', 'Full-Time', 'Part-Time', 'Freelance'] as $type)
                <li>
                    <input type="checkbox" class="filter-checkbox type-filter" id="{{ $type }}" value="{{ $type }}">
                    <label for="{{ $type }}">{{ $type }}</label>
                </li>
            @endforeach
        </ul>
    </div>
</li>
<hr>
<li class="mb-3">
    <button class="filterbtn mx-auto collapsed" data-bs-toggle="collapse" data-bs-target="#collapse"
        aria-expanded="false">
        <div class="btnname">
            <span>Location</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            @foreach ($locations as $location)
                <li>
                    <input type="checkbox" class="filter-checkbox location-filter" id="{{ $location->value }}"
                        value="{{ $location->value }}">
                    <label for="{{ $location->value }}">{{ $location->value }}</label>
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
            <span>Experience</span>
        </div>
        <div class="righticon d-flex ms-auto">
            <i class="fa-solid fa-angle-right toggle-icon"></i>
        </div>
    </button>
    <div class="collapse" id="collapse2">
        <ul class="btn-toggle-nav list-unstyled text-start ps-3 pe-0 py-2">
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="Fresher" value="0">
                <label for="Fresher">Fresher</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="1" value="1">
                <label for="1">1 year</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="2" value="2">
                <label for="2">2 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="3" value="3">
                <label for="3">3 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="4" value="4">
                <label for="4">4 years</label>
            </li>
            <li>
                <input type="checkbox" class="filter-checkbox exp-filter" id="5" value="5">
                <label for="5">More than 5 years</label>
            </li>
        </ul>
    </div>
</li>
<hr>