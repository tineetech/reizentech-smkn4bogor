<!-- BEGIN PAGE HEADER -->
<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $item)
                <li class="breadcrumb-item{{ $item['active'] ? ' active' : '' }}">
                    <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                </li>
            @endforeach
        </ol>
    </div>
</div>
<!-- END PAGE HEADER -->
