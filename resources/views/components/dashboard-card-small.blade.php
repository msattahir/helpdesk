@props(['title', 'value', 'total', 'class'])
<div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
    <a class="card card-hover-shadow h-100">
        <div class="card-body">
            <h6 class="card-subtitle">{{$title}}</h6>
            <div class="row align-items-center gx-2 mb-1">
                <h2 class="card-title text-{{$class}}">{{integer_format($value, true)}} &nbsp;</h2>
            </div>
            <span class="badge bg-soft-{{$class}} text-{{$class}}">
                {{@$total > 0 ? round(($value/$total * 100), 2) : 0}}%
            </span>
            <span class="text-body fs-6 ms-1">from {{integer_format(@$total, true)}}</span>
        </div>
    </a>
</div>
