<div class="cards">

    @php
        $list = $insert;
        $config = $list->configuration;
        $items = App\Http\Controllers\ListController::getItems($insert->id, $config);
        $template = str_replace('app\\', '', strtolower($config['model']));
    @endphp

    @foreach($items as $listitem)

        @include('partials.' . $template, [$template => $listitem])

    @endforeach

</div>
